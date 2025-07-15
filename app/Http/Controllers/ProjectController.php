<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\PipelineStage;
use App\Notifications\ProjectStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'role:admin|manager|marketing']),
        ];
    }

    public function index(Request $request)
    {
        $query = Project::with(['client', 'pic', 'latestSurvey']);
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Filter by PIC for marketing role
        if (Auth::user()->hasRole('marketing')) {
            $query->where('pic_id', Auth::id());
        }
        
        $projects = $query->latest()->paginate(10);
        
        return view('projects.index', compact('projects'));
    }

    public function create($client_id = null)
    {
        $clients = Client::orderBy('name')->get();
        $pics = User::role(['marketing', 'admin'])->get();
        
        // If client_id provided, pre-select the client
        $selectedClient = $client_id ? Client::find($client_id) : null;
        
        return view('projects.create', compact('clients', 'pics', 'selectedClient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:kantor,showroom,kafe,restoran,outlet,other',
            'location' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'pic_id' => 'required|exists:users,id',
            'project_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string'
        ]);

        // Set initial status
        $validated['status'] = 'lead';

        // Jika marketing, force PIC ke dirinya sendiri
        if (Auth::user()->hasRole('marketing')) {
            $validated['pic_id'] = Auth::id();
        }

        DB::beginTransaction();
        try {
            $project = Project::create($validated);

            // Insert pipeline history SESUAI MIGRASI project_pipelines
            $firstStage = PipelineStage::orderBy('order')->first();
            if ($firstStage) {
                $project->pipelineHistory()->create([
                    'from_status' => null, // Awal, boleh kosong
                    'to_status' => $firstStage->slug ?? 'lead', // Atau hardcode 'lead' jika slug tidak ada
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                    'notes' => null,
                ]);
            }

            DB::commit();

            // Send notification to relevant users after successful creation
            $users = collect();
            
            // Notify admin and managers
            $adminAndManagers = User::role(['admin', 'manager'])->get();
            $users = $users->merge($adminAndManagers);
            
            // Notify project PIC if different from creator
            if ($project->pic_id != Auth::id()) {
                $users->push($project->pic);
            }
            
            // Remove duplicates and current user
            $users = $users->unique('id')->reject(function ($user) {
                return $user->id === Auth::id();
            });
            
            if ($users->isNotEmpty()) {
                Notification::send($users, new ProjectStatusChanged(
                    $project, 
                    null, 
                    'lead', 
                    Auth::user()
                ));
            }

            // Redirect sesuai pilihan
            if ($request->has('save_and_survey')) {
                return redirect()->route('surveys.create', ['project_id' => $project->id])
                                ->with('success', 'Project berhasil dibuat. Silakan lanjutkan ke pembuatan survey.');
            }

            return redirect()->route('projects.show', $project)
                            ->with('success', 'Project berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }


    public function show(Project $project)
    {
        // Check authorization
        if (Auth::user()->hasRole('marketing') && $project->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $project->load(['client', 'pic', 'surveys.surveyor', 'surveys.photos', 'documents']);
        
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        // Check authorization
        if (Auth::user()->hasRole('marketing') && $project->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $clients = Client::orderBy('name')->get();
        $pics = User::role(['marketing', 'admin'])->get();
        
        return view('projects.edit', compact('project', 'clients', 'pics'));
    }

    public function update(Request $request, Project $project)
    {
        // Check authorization
        if (Auth::user()->hasRole('marketing') && $project->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:kantor,showroom,kafe,restoran,outlet,other',
            'location' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'pic_id' => 'required|exists:users,id',
            'status' => 'required|in:lead,survey,penawaran,negosiasi,deal,eksekusi,selesai,batal',
            'project_value' => 'nullable|numeric|min:0',
            'deal_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string'
        ]);
        
        // If marketing, can't change PIC
        if (Auth::user()->hasRole('marketing') && !Auth::user()->hasRole('admin')) {
            $validated['pic_id'] = $project->pic_id;
        }
        
        // Track status change
        $oldStatus = $project->status;
        
        $project->update($validated);
        
        // Send notification if status changed
        if ($oldStatus != $validated['status']) {
            $users = collect();
            
            // Notify project PIC
            $users->push($project->pic);
            
            // Notify admin and managers
            $adminAndManagers = User::role(['admin', 'manager'])->get();
            $users = $users->merge($adminAndManagers);
            
            // Remove duplicates and current user
            $users = $users->unique('id')->reject(function ($user) {
                return $user->id === Auth::id();
            });
            
            // Send notification
            if ($users->isNotEmpty()) {
                Notification::send($users, new ProjectStatusChanged(
                    $project, 
                    $oldStatus, 
                    $validated['status'], 
                    Auth::user()
                ));
            }
        }
        
        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil diupdate!');
    }

    public function destroy(Project $project)
    {
        // Only admin can delete
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
        
        // Check if project has surveys
        if ($project->surveys()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus project yang memiliki survey!');
        }
        
        $project->delete();
        
        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil dihapus!');
    }
    
    public function timeline(Project $project)
    {
        $project->load(['surveys', 'documents', 'pipelineHistory']);
        
        // Collect all timeline events
        $timeline = collect();
        
        // Add project creation
        $timeline->push([
            'date' => $project->created_at,
            'type' => 'project_created',
            'title' => 'Project dibuat',
            'description' => 'Project ' . $project->name . ' dibuat oleh ' . $project->pic->name,
            'icon' => 'bi-plus-circle',
            'color' => 'primary'
        ]);
        
        // Add surveys
        foreach ($project->surveys as $survey) {
            $timeline->push([
                'date' => $survey->scheduled_date,
                'type' => 'survey_scheduled',
                'title' => 'Survey dijadwalkan',
                'description' => 'Survey dijadwalkan untuk ' . $survey->scheduled_date->format('d M Y'),
                'icon' => 'bi-calendar',
                'color' => 'info'
            ]);
            
            if ($survey->status == 'completed') {
                $timeline->push([
                    'date' => $survey->actual_date ?? $survey->updated_at,
                    'type' => 'survey_completed',
                    'title' => 'Survey selesai',
                    'description' => 'Survey diselesaikan oleh ' . $survey->surveyor->name,
                    'icon' => 'bi-check-circle',
                    'color' => 'success'
                ]);
            }
        }
        
        // Sort by date descending
        $timeline = $timeline->sortByDesc('date');
        
        return view('projects.timeline', compact('project', 'timeline'));
    }
}