<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Project;
use App\Models\User;
use App\Models\SurveyPhoto;
use App\Notifications\SurveyAssigned;
use App\Events\NewSurveyAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\Facades\Image;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SurveyController extends Controller implements HasMiddleware
{
    // [LARAVEL 12+] Definisikan middleware secara static
    public static function middleware(): array
    {
        return [
            new Middleware(['auth']),
            // Jika ingin spesifik role, aktifkan:
            // new Middleware(['role:admin|manager|marketing|surveyor']),
        ];
    }

    public function index(Request $request)
    {
        
        $query = Survey::with(['project.client', 'surveyor']);

        // Filter by role
        if (Auth::user()->hasRole('surveyor')) {
            $query->where('surveyor_id', Auth::id());
        } elseif (Auth::user()->hasRole('marketing')) {
            $query->whereHas('project', function($q) {
                $q->where('pic_id', Auth::id());
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('project', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('project.client', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('scheduled_date', $request->date);
        }

        $surveys = $query->latest('scheduled_date')->paginate(10);

        return view('surveys.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        // If surveyor, redirect to mobile form
        if (Auth::user()->hasRole('surveyor') && !Auth::user()->hasRole('admin')) {
            return redirect()->route('surveys.mobile.form');
        }

        $projects = Project::with('client')
                          ->whereIn('status', ['lead', 'survey'])
                          ->orderBy('name')
                          ->get();

        $surveyors = User::role('surveyor')->get();

        // Pre-select project if provided
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('surveys.create', compact('projects', 'surveyors', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'surveyor_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        $validated['status'] = 'pending';

        DB::beginTransaction();
        try {
            $survey = Survey::create($validated);

            // Update project status to survey if still lead
            $project = Project::find($validated['project_id']);
            if ($project->status == 'lead') {
                $project->update(['status' => 'survey']);
            }

            // Send notification to surveyor
            $survey->surveyor->notify(new SurveyAssigned($survey, Auth::user()));

            // Also notify admin and managers
            $adminAndManagers = User::role(['admin', 'manager'])->get();
            Notification::send($adminAndManagers, new SurveyAssigned($survey, Auth::user()));

            DB::commit();

            return redirect()->route('surveys.show', $survey)
                ->with('success', 'Survey berhasil dijadwalkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(Survey $survey)
    {
        // Check authorization
        if (Auth::user()->hasRole('surveyor') && $survey->surveyor_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (Auth::user()->hasRole('marketing') && $survey->project->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $survey->load(['project.client', 'surveyor', 'photos']);
        $surveyors = User::role('surveyor')->where('is_active', true)->get();

        return view('surveys.show', compact('survey', 'surveyors'));
    }

    public function edit(Survey $survey)
    {
        // Only allow edit if pending
        if ($survey->status != 'pending') {
            return back()->with('error', 'Survey yang sudah dimulai tidak dapat diedit.');
        }

        // Check authorization
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('marketing')) {
            abort(403, 'Unauthorized');
        }

        $projects = Project::with('client')->orderBy('name')->get();
        $surveyors = User::role('surveyor')->get();

        return view('surveys.edit', compact('survey', 'projects', 'surveyors'));
    }

    public function update(Request $request, Survey $survey)
    {
        // Only allow update if pending
        if ($survey->status != 'pending') {
            return back()->with('error', 'Survey yang sudah dimulai tidak dapat diubah.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'surveyor_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $survey->update($validated);

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Survey berhasil diupdate!');
    }

    public function destroy(Survey $survey)
    {
        // Only admin can delete
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        // Check if survey has been started
        if ($survey->status != 'pending') {
            return back()->with('error', 'Survey yang sudah dimulai tidak dapat dihapus.');
        }

        $survey->delete();

        return redirect()->route('surveys.index')
            ->with('success', 'Survey berhasil dihapus!');
    }

    // Mobile specific methods
    public function mobileForm(Request $request)
    {
        // Check if user is surveyor
        if (!Auth::user()->hasRole('surveyor') && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        // Get pending surveys for this surveyor
        $pendingSurveys = Survey::with(['project.client'])
                               ->where('surveyor_id', Auth::id())
                               ->where('status', 'pending')
                               ->whereDate('scheduled_date', '<=', now())
                               ->orderBy('scheduled_date')
                               ->get();

        // If survey_id provided, load that survey
        $survey = null;
        if ($request->has('survey_id')) {
            $survey = Survey::with(['project.client'])
                           ->where('id', $request->survey_id)
                           ->where('surveyor_id', Auth::id())
                           ->first();

            if (!$survey) {
                abort(404);
            }
        }

        return view('surveys.mobile.form', compact('pendingSurveys', 'survey'));
    }

    public function submitMobile(Request $request, Survey $survey)
    {
        // Validate survey belongs to this surveyor
        if ($survey->surveyor_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'electricity' => 'required|in:yes,no',
            'electricity_notes' => 'nullable|string',
            'water' => 'required|in:yes,no',
            'water_notes' => 'nullable|string',
            'road_access' => 'required|in:easy,medium,difficult',
            'permit_status' => 'required|in:complete,process,none',
            'existing_condition' => 'required|in:good,medium,bad,empty',
            'area_size' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'photos.*' => 'image|max:10240' // 10MB max per photo
        ]);

        DB::beginTransaction();
        try {
            // Update survey data
            $survey->update([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'address' => $validated['address'],
                'actual_date' => now(),
                'status' => 'completed',
                'notes' => $validated['notes'],
                'checklist_data' => [
                    'electricity' => $validated['electricity'],
                    'electricity_notes' => $validated['electricity_notes'],
                    'water' => $validated['water'],
                    'water_notes' => $validated['water_notes'],
                    'road_access' => $validated['road_access'],
                    'permit_status' => $validated['permit_status'],
                    'existing_condition' => $validated['existing_condition'],
                    'area_size' => $validated['area_size']
                ]
            ]);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    // Generate filename
                    $filename = 'survey_' . $survey->id . '_' . time() . '_' . $index . '.' . $photo->extension();

                    // Store original
                    $path = $photo->storeAs('surveys/' . $survey->id, $filename, 'public');

                    // Create thumbnail
                    $thumbnailPath = 'surveys/' . $survey->id . '/thumb_' . $filename;
                    $image = Image::make($photo);
                    $image->fit(300, 300);
                    Storage::disk('public')->put($thumbnailPath, $image->stream());

                    // Save to database
                    SurveyPhoto::create([
                        'survey_id' => $survey->id,
                        'filename' => $filename,
                        'path' => $path,
                        'thumbnail_path' => $thumbnailPath,
                        'order' => $index + 1
                    ]);
                }
            }

            // Update project status if needed
            $project = $survey->project;
            if ($project->status == 'survey') {
                $project->update(['status' => 'penawaran']);
            }

            DB::commit();

            return redirect()->route('surveys.show', $survey)
                ->with('success', 'Survey berhasil diselesaikan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function uploadPhotos(Request $request, Survey $survey)
    {
        // Validate
        $request->validate([
            'photos.*' => 'required|image|max:10240'
        ]);

        $uploadedCount = 0;

        foreach ($request->file('photos') as $photo) {
            $filename = 'survey_' . $survey->id . '_' . uniqid() . '.' . $photo->extension();
            $path = $photo->storeAs('surveys/' . $survey->id, $filename, 'public');

            SurveyPhoto::create([
                'survey_id' => $survey->id,
                'filename' => $filename,
                'path' => $path,
                'order' => $survey->photos()->max('order') + 1
            ]);

            $uploadedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => $uploadedCount . ' foto berhasil diupload'
        ]);
    }

    public function assignSurvey(Request $request, Survey $survey, User $user)
    {
        // Validasi apakah user yang login berhak melakukan assignment
        // Misalnya, hanya admin atau manager yang boleh assign survey
        if (!Auth::user()->hasRole(['admin', 'manager'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        // Logic untuk assign survey ke user
        $survey->update([
            'assigned_to' => $user->id,
            'status' => 'assigned', // Update status survey jika diperlukan
            'assigned_at' => now()  // Simpan waktu assignment jika diperlukan
        ]);

        // Broadcast event
        event(new NewSurveyAssigned($survey, $user));

        return back()->with('success', 'Survey berhasil ditetapkan kepada ' . $user->name);
    }
}
