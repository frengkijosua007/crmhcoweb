<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllerV2;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth Routes (legacy) - kept for backward compatibility
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('users.update-profile');
    Route::post('/profile/password', [UserController::class, 'updatePassword'])->name('users.update-password');

    // Profile (legacy) - kept for backward compatibility
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/user/password', function(Request $request) {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    })->middleware(['auth'])->name('password.update');

    // User management
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });

    // Projects
    Route::resource('projects', ProjectController::class)->parameters([
        'projects' => 'project'
    ]);
    Route::get('/projects/create/{client_id?}', [ProjectController::class, 'create'])->name('projects.create.withClient');
    Route::get('/projects/{project}/timeline', [ProjectController::class, 'timeline'])->name('projects.timeline');

    // Clients
    Route::resource('clients', ClientController::class);

    // Leads
    Route::resource('leads', LeadController::class);

    // Surveys
    Route::resource('surveys', SurveyController::class);
    Route::get('/surveys', [SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/mobile/form', [SurveyController::class, 'mobileForm'])->name('surveys.mobile.form');
    Route::post('/surveys/{survey}/submit', [SurveyController::class, 'submitMobile'])->name('surveys.submit');
    Route::post('/surveys/{survey}/photos', [SurveyController::class, 'uploadPhotos'])->name('surveys.photos.upload');

    // Debugging route for roles and permissions
    Route::get('/test-role', function() {
        $user = Auth::user();
        return [
            'user' => $user->name,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ];
    });

    // Pipeline routes
    Route::middleware(['role:admin|manager|marketing'])->group(function () {
        Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
        Route::get('/pipeline/analytics', [PipelineController::class, 'analytics'])->name('pipeline.analytics');
        Route::get('/pipeline/funnel', [PipelineController::class, 'funnel'])->name('pipeline.funnel');
        Route::post('/pipeline/update-stage', [PipelineController::class, 'updateStage'])->name('pipeline.update-stage');
        Route::post('/pipeline/metrics', [PipelineController::class, 'getMetrics'])->name('pipeline.metrics');
    });

    // Document Management Routes
    Route::middleware(['role:admin|manager|marketing'])->prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview');
        Route::post('/bulk-download', [DocumentController::class, 'bulkDownload'])->name('bulk-download');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::get('/test-notification', [TestController::class, 'testNotification']);

    // Report Routes
    Route::middleware(['role:admin|manager|marketing'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/generate', [ReportController::class, 'generate'])->name('generate');
    });

    // Settings Routes
    Route::middleware(['role:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/update/general', [SettingController::class, 'updateGeneral'])->name('update.general');
        Route::post('/update/company', [SettingController::class, 'updateCompany'])->name('update.company');
        Route::post('/update/notification', [SettingController::class, 'updateNotification'])->name('update.notification');
        Route::post('/update/user', [SettingController::class, 'updateUser'])->name('update.user');

        // System Info
        Route::get('/system', [SettingController::class, 'system'])->name('system');

        // Backup & Restore
        Route::get('/backup', [SettingController::class, 'backup'])->name('backup');
        Route::get('/backup/create', [SettingController::class, 'createBackup'])->name('create-backup');
        Route::get('/backup/download/{filename}', [SettingController::class, 'downloadBackup'])->name('download-backup');
        Route::delete('/backup/delete/{filename}', [SettingController::class, 'deleteBackup'])->name('delete-backup');

        // Logs
        Route::get('/logs', [SettingController::class, 'logs'])->name('logs');
        Route::get('/logs/view/{filename}', [SettingController::class, 'viewLog'])->name('view-log');
        Route::get('/logs/download/{filename}', [SettingController::class, 'downloadLog'])->name('download-log');

        // Clear cache
        Route::get('/clear-cache', function() {
            \Artisan::call('optimize:clear');
            return back()->with('success', 'Cache cleared successfully!');
        })->name('clear-cache');
    });

    // Assign Survey
    Route::patch('/surveys/{survey}/assign/{user}', [SurveyController::class, 'assignSurvey'])
        ->name('surveys.assign')
        ->middleware('can:assign-surveys');
});
