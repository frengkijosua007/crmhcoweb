<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\PipelineStage;
use App\Models\ProjectPipeline;
use App\Models\PipelineConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PipelineController extends Controller
{
    /**
     * Display the pipeline in kanban or list view
     */
    public function index(Request $request)
    {
        // Get all pipeline stages
        $stages = PipelineStage::where('is_active', true)->orderBy('order')->get();

        // Get projects grouped by status
        $projectsQuery = Project::with(['client', 'pic']);

        // Filter by PIC for marketing role
        if (Auth::user()->hasRole('marketing')) {
            $projectsQuery->where('pic_id', Auth::id());
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $projectsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $projectsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $projectsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $projects = $projectsQuery->get();

        // Map project statuses to pipeline stage slugs
        $statusMapping = [
            'lead' => 'lead',
            'survey' => 'survey',
            'penawaran' => 'quotation',
            'negosiasi' => 'negotiation',
            'deal' => 'deal',
            'eksekusi' => 'execution',
            'selesai' => 'completed',
            'batal' => 'cancelled'
        ];

        // Group projects by status
        $pipeline = [];
        foreach ($stages as $stage) {
            $stageSlug = $stage->slug;

            // Find the matching project status for this stage
            $matchingStatus = array_search($stageSlug, $statusMapping);
            if ($matchingStatus === false) {
                $matchingStatus = $stageSlug; // Fallback to using the slug directly
            }

            $stageProjects = $projects->filter(function($project) use ($matchingStatus, $statusMapping) {
                // Check if the project's status matches this stage
                return $project->status == $matchingStatus;
            });

            $pipeline[] = [
                'stage' => $stage,
                'projects' => $stageProjects,
                'count' => $stageProjects->count(),
                'value' => $stageProjects->sum('project_value')
            ];
        }

        // Calculate metrics
        $metrics = [
            'total_projects' => $projects->count(),
            'total_value' => $projects->sum('project_value'),
            'deal_value' => $projects->where('status', 'deal')->sum('deal_value') ?? $projects->where('status', 'deal')->sum('project_value'),
            'conversion_rate' => $this->calculateConversionRate($projects),
            'average_deal_size' => $projects->where('status', 'deal')->avg('deal_value') ?? $projects->where('status', 'deal')->avg('project_value') ?? 0,
            'win_rate' => $this->calculateWinRate($projects)
        ];

        // View type (kanban or list)
        $viewType = $request->get('view', 'kanban');

        return view('.pipeline.index', compact('pipeline', 'metrics', 'viewType'));
    }

    /**
     * Update project status in the pipeline
     */
    public function updateStage(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'new_status' => 'required|string'
        ]);

        $project = Project::findOrFail($validated['project_id']);

        // Check authorization
        if (Auth::user()->hasRole('marketing') && $project->pic_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized. You can only update your own projects.'], 403);
        }

        // Map stage slug back to project status if needed
        $statusMapping = [
            'lead' => 'lead',
            'survey' => 'survey',
            'quotation' => 'penawaran',
            'negotiation' => 'negosiasi',
            'deal' => 'deal',
            'execution' => 'eksekusi',
            'completed' => 'selesai',
            'cancelled' => 'batal'
        ];

        $newStatus = $validated['new_status'];
        if (array_key_exists($newStatus, $statusMapping)) {
            $newStatus = $statusMapping[$newStatus];
        }

        DB::beginTransaction();
        try {
            $oldStatus = $project->status;

            // Don't update if status is the same
            if ($oldStatus === $newStatus) {
                return response()->json([
                    'success' => true,
                    'message' => 'No change in status'
                ]);
            }

            // Update project status
            $project->update(['status' => $newStatus]);

            // Log pipeline history
            ProjectPipeline::create([
                'project_id' => $project->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'changed_at' => now(),
                'notes' => $request->notes ?? 'Status updated via pipeline drag and drop'
            ]);

            // Special handling for deal status
            if ($newStatus == 'deal' && !$project->deal_value) {
                $project->update(['deal_value' => $project->project_value]);
            }

            // Track conversion for analytics
            $this->trackStageConversion($oldStatus, $newStatus);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status project berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the sales funnel visualization
     */
    public function funnel(Request $request)
    {
        $stages = PipelineStage::where('is_active', true)->orderBy('order')->get();

        // Get date range (default last 30 days)
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Build funnel data
        $funnelData = [];
        $previousCount = null;

        foreach ($stages as $index => $stage) {
            $statusMapping = [
                'lead' => 'lead',
                'survey' => 'survey',
                'quotation' => 'penawaran',
                'negotiation' => 'negosiasi',
                'deal' => 'deal',
                'execution' => 'eksekusi',
                'completed' => 'selesai',
                'cancelled' => 'batal'
            ];

            $projectStatus = array_search($stage->slug, $statusMapping) !== false ?
                array_search($stage->slug, $statusMapping) : $stage->slug;

            $count = Project::whereDate('created_at', '>=', $dateFrom)
                           ->whereDate('created_at', '<=', $dateTo)
                           ->where('status', $projectStatus)
                           ->count();

            $conversionRate = ($index > 0 && $previousCount > 0)
                ? round(($count / $previousCount) * 100, 1)
                : 100;

            $funnelData[] = [
                'stage' => $stage,
                'count' => $count,
                'conversion_rate' => $conversionRate,
                'color' => $stage->color
            ];

            $previousCount = $count;
        }

        return view('.pipeline.funnel', compact('funnelData', 'dateFrom', 'dateTo'));
    }

    /**
     * Display the pipeline analytics
     */
    public function analytics(Request $request)
    {
        // Pipeline velocity (average time in each stage)
        $velocityData = DB::table('project_pipelines')
            ->select(
                'from_status',
                DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, changed_at)) as avg_days')
            )
            ->whereNotNull('from_status')
            ->groupBy('from_status')
            ->get();

        // Win/Loss analysis
        $winLossData = [
            'won' => Project::where('status', 'deal')->orWhere('status', 'eksekusi')->orWhere('status', 'selesai')->count(),
            'lost' => Project::where('status', 'batal')->count(),
            'in_progress' => Project::whereNotIn('status', ['deal', 'eksekusi', 'selesai', 'batal'])->count()
        ];

        // Monthly pipeline value trend
        $monthlyTrend = Project::selectRaw('
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                SUM(project_value) as total_value,
                COUNT(*) as total_projects
            ')
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Top performers (by PIC)
        $topPerformers = Project::select('pic_id', DB::raw('COUNT(*) as total_projects'), DB::raw('SUM(COALESCE(deal_value, project_value)) as total_value'))
            ->whereIn('status', ['deal', 'eksekusi', 'selesai'])
            ->groupBy('pic_id')
            ->with('pic')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get();

        return view('.pipeline.analytics', compact('velocityData', 'winLossData', 'monthlyTrend', 'topPerformers'));
    }

    /**
     * Calculate the lead-to-deal conversion rate
     */
    private function calculateConversionRate($projects)
    {
        $totalLeads = $projects->count();
        $totalDeals = $projects->whereIn('status', ['deal', 'eksekusi', 'selesai'])->count();

        return $totalLeads > 0 ? round(($totalDeals / $totalLeads) * 100, 1) : 0;
    }

    private function calculateWinRate($projects)
    {
        $totalWon = $projects->whereIn('status', ['deal', 'eksekusi', 'selesai'])->count();  // Ganti dengan status yang sesuai
        $totalProjects = $projects->count();

        return $totalProjects > 0 ? round(($totalWon / $totalProjects) * 100, 1) : 0;
    }


    /**
     * Get pipeline metrics via AJAX for real-time updates
     */
    public function getMetrics(Request $request)
    {
        // Get projects based on filters
        $projectsQuery = Project::query();

        // Filter by PIC for marketing role
        if (Auth::user()->hasRole('marketing')) {
            $projectsQuery->where('pic_id', Auth::id());
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $projectsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $projectsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $projectsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $projects = $projectsQuery->get();

        // Calculate metrics
        $metrics = [
            'total_projects' => $projects->count(),
            'total_value' => $projects->sum('project_value'),
            'deal_value' => $projects->where('status', 'deal')->sum('deal_value') ?? $projects->where('status', 'deal')->sum('project_value'),
            'conversion_rate' => $this->calculateConversionRate($projects),
            'average_deal_size' => $projects->where('status', 'deal')->avg('deal_value') ?? $projects->where('status', 'deal')->avg('project_value') ?? 0,
            'win_rate' => $this->calculateWinRate($projects)
        ];

        return response()->json([
            'success' => true,
            'metrics' => $metrics
        ]);
    }

    /**
     * Track stage conversion for analytics
     */
    private function trackStageConversion($fromStatus, $toStatus)
    {
        try {
            $conversionRecord = PipelineConversion::firstOrNew([
                'from_status' => $fromStatus,
                'to_status' => $toStatus
            ]);

            $conversionRecord->count = ($conversionRecord->count ?? 0) + 1;
            $conversionRecord->save();
        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Log::error('Failed to track conversion: ' . $e->getMessage());
        }
    }
}
