<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PipelineStage;
use App\Models\ProjectPipeline;
use App\Models\PipelineCoversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PipelineController extends Controller
{
    public function index()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $projects = Project::with(['client', 'currentPipeline.stage'])->get();

        // Group projects by stage
        $projectsByStage = $projects->groupBy(function($project) {
            return $project->currentPipeline->stage->name ?? 'Unknown';
        });

        // Calculate metrics
        $metrics = $this->calculateMetrics();

        return view('pipeline.index', compact('stages', 'projectsByStage', 'metrics'));
    }

    public function funnel()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $funnelData = [];

        foreach ($stages as $stage) {
            $count = ProjectPipeline::where('stage_id', $stage->id)
                ->where('is_current', true)
                ->count();

            $funnelData[] = [
                'stage' => $stage->name,
                'count' => $count,
                'color' => $stage->color ?? '#3B82F6'
            ];
        }

        $metrics = $this->calculateMetrics();

        return view('pipeline.funnel', compact('funnelData', 'metrics'));
    }

    public function analytics()
    {
        $metrics = $this->calculateMetrics();
        $conversionData = $this->getConversionData();
        $timeAnalysis = $this->getTimeAnalysis();

        return view('pipeline.analytics', compact('metrics', 'conversionData', 'timeAnalysis'));
    }

    /**
     * Update project stage via drag & drop
     */
    public function updateStage(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'stage_id' => 'required|exists:pipeline_stages,id'
        ]);

        $project = Project::findOrFail($request->project_id);
        $newStage = PipelineStage::findOrFail($request->stage_id);

        // Get current pipeline
        $currentPipeline = $project->currentPipeline;

        if ($currentPipeline && $currentPipeline->stage_id == $request->stage_id) {
            return response()->json(['message' => 'Project already in this stage'], 400);
        }

        DB::transaction(function() use ($project, $newStage, $currentPipeline) {
            // Set current pipeline as not current
            if ($currentPipeline) {
                $currentPipeline->update(['is_current' => false]);
            }

            // Create new pipeline entry
            ProjectPipeline::create([
                'project_id' => $project->id,
                'stage_id' => $newStage->id,
                'is_current' => true,
                'moved_at' => now(),
                'moved_by' => auth()->id()
            ]);

            // Update project status
            $project->update(['status' => $newStage->name]);
        });

        // Update conversion tracking
        $this->updateConversionTracking($project->id, $newStage->id);

        return response()->json([
            'message' => 'Project stage updated successfully',
            'project' => $project->load('currentPipeline.stage')
        ]);
    }

    /**
     * Calculate pipeline metrics
     */
    private function calculateMetrics()
    {
        $totalProjects = Project::count();
        $totalValue = Project::sum('project_value') ?? 0;

        // Get projects in each key stage
        $leadStage = PipelineStage::where('name', 'Lead Masuk')->first();
        $surveyStage = PipelineStage::where('name', 'Survey Dilakukan')->first();
        $quotationStage = PipelineStage::where('name', 'Penawaran Dibuat')->first();
        $dealStage = PipelineStage::where('name', 'Deal/Kontrak')->first();

        // Count projects in each stage
        $leadsCount = $this->getProjectsInStage($leadStage?->id);
        $surveysCount = $this->getProjectsInStage($surveyStage?->id);
        $quotationsCount = $this->getProjectsInStage($quotationStage?->id);
        $dealsCount = $this->getProjectsInStage($dealStage?->id);

        // Calculate conversions
        $leadToSurvey = $leadsCount > 0 ? round(($surveysCount / $leadsCount) * 100, 1) : 0;
        $surveyToQuotation = $surveysCount > 0 ? round(($quotationsCount / $surveysCount) * 100, 1) : 0;
        $quotationToDeal = $quotationsCount > 0 ? round(($dealsCount / $quotationsCount) * 100, 1) : 0;

        // Calculate average time per stage
        $avgTimePerStage = $this->calculateAverageTimePerStage();

        return [
            'total_projects' => $totalProjects,
            'total_value' => $totalValue,
            'lead_to_survey' => $leadToSurvey,
            'survey_to_quotation' => $surveyToQuotation,
            'quotation_to_deal' => $quotationToDeal,
            'average_time_per_stage' => $avgTimePerStage
        ];
    }

    /**
     * Get projects count in specific stage
     */
    private function getProjectsInStage($stageId)
    {
        if (!$stageId) return 0;

        return ProjectPipeline::where('stage_id', $stageId)
            ->where('is_current', true)
            ->count();
    }

    /**
     * Calculate average time per stage
     */
    private function calculateAverageTimePerStage()
    {
        $avgTimes = ProjectPipeline::select('stage_id')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, moved_at, COALESCE(
                (SELECT moved_at FROM project_pipelines p2
                 WHERE p2.project_id = project_pipelines.project_id
                 AND p2.moved_at > project_pipelines.moved_at
                 ORDER BY p2.moved_at LIMIT 1),
                NOW()
            ))) as avg_days')
            ->where('is_current', false)
            ->groupBy('stage_id')
            ->get();

        $totalDays = 0;
        $stageCount = 0;

        foreach ($avgTimes as $time) {
            $totalDays += $time->avg_days;
            $stageCount++;
        }

        return $stageCount > 0 ? round($totalDays / $stageCount) : 0;
    }

    /**
     * Get conversion data for analytics
     */
    private function getConversionData()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $conversionData = [];

        foreach ($stages as $index => $stage) {
            $currentCount = $this->getProjectsInStage($stage->id);
            $totalPassed = ProjectPipeline::where('stage_id', $stage->id)->count();

            $conversionData[] = [
                'stage' => $stage->name,
                'current' => $currentCount,
                'total_passed' => $totalPassed,
                'color' => $stage->color ?? '#3B82F6'
            ];
        }

        return $conversionData;
    }

    /**
     * Get time analysis data
     */
    private function getTimeAnalysis()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $timeData = [];

        foreach ($stages as $stage) {
            $avgTime = ProjectPipeline::where('stage_id', $stage->id)
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, moved_at, COALESCE(
                    (SELECT moved_at FROM project_pipelines p2
                     WHERE p2.project_id = project_pipelines.project_id
                     AND p2.moved_at > project_pipelines.moved_at
                     ORDER BY p2.moved_at LIMIT 1),
                    NOW()
                ))) as avg_days')
                ->where('is_current', false)
                ->first();

            $timeData[] = [
                'stage' => $stage->name,
                'avg_days' => round($avgTime->avg_days ?? 0, 1),
                'color' => $stage->color ?? '#3B82F6'
            ];
        }

        return $timeData;
    }

    /**
     * Update conversion tracking
     */
    private function updateConversionTracking($projectId, $stageId)
    {
        $project = Project::find($projectId);
        $stage = PipelineStage::find($stageId);

        if (!$project || !$stage) return;

        // Create or update conversion record
        PipelineCoversion::updateOrCreate([
            'project_id' => $projectId,
            'stage_id' => $stageId
        ], [
            'converted_at' => now(),
            'project_value' => $project->project_value,
            'converted_by' => auth()->id()
        ]);
    }

    /**
     * Get pipeline summary for dashboard
     */
    public function getPipelineSummary()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $summary = [];

        foreach ($stages as $stage) {
            $projects = ProjectPipeline::where('stage_id', $stage->id)
                ->where('is_current', true)
                ->with('project')
                ->get();

            $stageValue = $projects->sum('project.project_value') ?? 0;

            $summary[] = [
                'stage' => $stage->name,
                'count' => $projects->count(),
                'value' => $stageValue,
                'color' => $stage->color ?? '#3B82F6'
            ];
        }

        return response()->json($summary);
    }

    /**
     * Get project details for quick view
     */
    public function getProjectDetails($id)
    {
        $project = Project::with([
            'client',
            'currentPipeline.stage',
            'surveys' => function($query) {
                $query->latest()->take(3);
            },
            'documents' => function($query) {
                $query->latest()->take(3);
            }
        ])->findOrFail($id);

        return response()->json($project);
    }
}
