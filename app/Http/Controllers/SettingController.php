<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the settings dashboard
     */
    public function index()
    {
        $generalSettings = Setting::where('group', 'general')->get()->keyBy('key');
        $companySettings = Setting::where('group', 'company')->get()->keyBy('key');
        $notificationSettings = Setting::where('group', 'notification')->get()->keyBy('key');
        $userSettings = Setting::where('group', 'user')->get()->keyBy('key');

        return view('settings.index', compact(
            'generalSettings',
            'companySettings',
            'notificationSettings',
            'userSettings'
        ));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'maintenance_mode' => 'boolean',
            'default_pagination' => 'required|integer|min:5|max:100',
            'date_format' => 'required|string|in:d/m/Y,m/d/Y,Y-m-d,d-m-Y,d M Y',
            'time_format' => 'required|string|in:H:i,h:i A',
            'timezone' => 'required|string|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'general'],
                ['value' => $value]
            );
        }

        $this->clearSettingsCache();

        return back()->with('success', 'General settings updated successfully');
    }

    /**
     * Update company settings
     */
    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string|max:1000',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_tax_id' => 'nullable|string|max:50',
            'invoice_prefix' => 'nullable|string|max:10',
            'currency' => 'required|string|in:IDR,USD',
            'fiscal_year_start' => 'required|string|date_format:m-d'
        ]);

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $request->validate([
                'company_logo' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $logoFile = $request->file('company_logo');
            $logoPath = $logoFile->store('settings', 'public');

            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'company_logo')->where('group', 'company')->first();
            if ($oldLogo && Storage::disk('public')->exists($oldLogo->value)) {
                Storage::disk('public')->delete($oldLogo->value);
            }

            Setting::updateOrCreate(
                ['key' => 'company_logo', 'group' => 'company'],
                ['value' => $logoPath]
            );
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'company'],
                ['value' => $value]
            );
        }

        $this->clearSettingsCache();

        return back()->with('success', 'Company settings updated successfully');
    }

    /**
     * Update notification settings
     */
    public function updateNotification(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'survey_notifications' => 'boolean',
            'project_status_notifications' => 'boolean',
            'document_upload_notifications' => 'boolean',
            'client_notifications' => 'boolean',
            'notification_email' => 'required_if:email_notifications,1|nullable|email',
            'email_sender_name' => 'required_if:email_notifications,1|nullable|string|max:255'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'notification'],
                ['value' => $value]
            );
        }

        $this->clearSettingsCache();

        return back()->with('success', 'Notification settings updated successfully');
    }

    /**
     * Update user settings
     */
    public function updateUser(Request $request)
    {
        $validated = $request->validate([
            'default_role' => 'required|string|exists:roles,name',
            'allow_registration' => 'boolean',
            'account_approval' => 'boolean',
            'password_min_length' => 'required|integer|min:6|max:20',
            'password_requires_letters' => 'boolean',
            'password_requires_numbers' => 'boolean',
            'password_requires_symbols' => 'boolean',
            'user_avatar_max_size' => 'required|integer|min:1|max:10',
            'inactive_user_days' => 'required|integer|min:30|max:365'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'user'],
                ['value' => $value]
            );
        }

        $this->clearSettingsCache();

        return back()->with('success', 'User settings updated successfully');
    }

    /**
     * Display the system information
     */
    public function system()
    {
        $phpVersion = phpversion();
        $laravelVersion = app()->version();
        $serverOS = php_uname('s') . ' ' . php_uname('r');
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $databaseType = config('database.default');
        $databaseVersion = match($databaseType) {
            'mysql' => \DB::select('SELECT VERSION() as version')[0]->version,
            'pgsql' => \DB::select('SELECT version()')[0]->version,
            default => 'Unknown'
        };

        $diskTotal = disk_total_space('/');
        $diskFree = disk_free_space('/');
        $diskUsed = $diskTotal - $diskFree;
        $diskUsedPercent = round(($diskUsed / $diskTotal) * 100, 2);

        $memInfo = $this->getSystemMemoryInfo();
        $memoryUsedPercent = isset($memInfo['MemTotal']) && isset($memInfo['MemAvailable'])
            ? round((($memInfo['MemTotal'] - $memInfo['MemAvailable']) / $memInfo['MemTotal']) * 100, 2)
            : null;

        $systemInfo = [
            'PHP Version' => $phpVersion,
            'Laravel Version' => $laravelVersion,
            'Server OS' => $serverOS,
            'Web Server' => $serverSoftware,
            'Database Type' => ucfirst($databaseType),
            'Database Version' => $databaseVersion,
            'Disk Usage' => "$diskUsedPercent% used (" . $this->formatBytes($diskUsed) . " of " . $this->formatBytes($diskTotal) . ")",
            'Memory Usage' => $memoryUsedPercent ? "$memoryUsedPercent%" : 'Unknown',
            'Max Upload Size' => ini_get('upload_max_filesize'),
            'Post Max Size' => ini_get('post_max_size'),
            'Max Execution Time' => ini_get('max_execution_time') . 's',
        ];

        // Extension checks
        $requiredExtensions = [
            'BCMath', 'Ctype', 'Fileinfo', 'JSON', 'Mbstring', 'OpenSSL',
            'PDO', 'Tokenizer', 'XML', 'cURL', 'GD'
        ];

        $extensionStatus = [];
        foreach ($requiredExtensions as $extension) {
            $extensionStatus[$extension] = extension_loaded(strtolower($extension));
        }

        // Directory permissions
        $directories = [
            'Storage' => storage_path(),
            'Bootstrap/Cache' => base_path('bootstrap/cache'),
            'Public' => public_path(),
            'Public/Storage' => public_path('storage'),
        ];

        $directoryPermissions = [];
        foreach ($directories as $name => $path) {
            $directoryPermissions[$name] = [
                'path' => $path,
                'writable' => is_writable($path)
            ];
        }

        return view('settings.system', compact('systemInfo', 'extensionStatus', 'directoryPermissions'));
    }

    /**
     * Display the backup page
     */
    public function backup()
    {
        $backups = Storage::disk('backup')->files();
        $backups = array_filter($backups, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'zip';
        });

        $backupData = [];
        foreach ($backups as $backup) {
            $backupData[] = [
                'name' => basename($backup),
                'size' => Storage::disk('backup')->size($backup),
                'last_modified' => Storage::disk('backup')->lastModified($backup),
            ];
        }

        // Sort backups by last modified (newest first)
        usort($backupData, function($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        return view('settings.backup', compact('backupData'));
    }

    /**
     * Create a new backup
     */
    public function createBackup()
    {
        try {
            // Initiate backup using the backup package
            \Artisan::call('backup:run');

            return back()->with('success', 'Backup created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function downloadBackup($filename)
    {
        $filePath = Storage::disk('backup')->path($filename);

        if (!Storage::disk('backup')->exists($filename)) {
            return back()->with('error', 'Backup file not found');
        }

        return response()->download($filePath);
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup($filename)
    {
        if (!Storage::disk('backup')->exists($filename)) {
            return back()->with('error', 'Backup file not found');
        }

        Storage::disk('backup')->delete($filename);

        return back()->with('success', 'Backup deleted successfully');
    }

    /**
     * Display logs page
     */
    public function logs()
    {
        $logFiles = Storage::disk('logs')->files();
        $logFiles = array_filter($logFiles, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'log';
        });

        $logData = [];
        foreach ($logFiles as $log) {
            $logData[] = [
                'name' => basename($log),
                'size' => Storage::disk('logs')->size($log),
                'last_modified' => Storage::disk('logs')->lastModified($log),
            ];
        }

        // Sort logs by last modified (newest first)
        usort($logData, function($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        return view('settings.logs', compact('logData'));
    }

    /**
     * View a log file
     */
    public function viewLog($filename)
    {
        if (!Storage::disk('logs')->exists($filename)) {
            return back()->with('error', 'Log file not found');
        }

        $content = Storage::disk('logs')->get($filename);

        return view('settings.log-viewer', compact('content', 'filename'));
    }

    /**
     * Download a log file
     */
    public function downloadLog($filename)
    {
        if (!Storage::disk('logs')->exists($filename)) {
            return back()->with('error', 'Log file not found');
        }

        $filePath = Storage::disk('logs')->path($filename);

        return response()->download($filePath);
    }

    /**
     * Clear the settings cache
     */
    private function clearSettingsCache()
    {
        Cache::forget('settings');
    }

    /**
     * Get system memory info
     */
    private function getSystemMemoryInfo()
    {
        if (function_exists('shell_exec') && strtolower(PHP_OS) === 'linux') {
            $meminfo = shell_exec('cat /proc/meminfo');
            $meminfo = explode("\n", $meminfo);
            $meminfo = array_filter($meminfo);

            $memory = [];
            foreach ($meminfo as $line) {
                list($key, $val) = explode(':', $line);
                $memory[trim($key)] = intval(trim(explode(' ', trim($val))[0]));
            }

            return $memory;
        }

        return [];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
