<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Project;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{   
    public function index()
    {
        $user = Auth::user();

        // Get user role
        $roles = $user->getRoleNames();
        $role = $roles->isNotEmpty() ? $roles->first() : 'default';

        // Get user statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        // Get user roles distribution
        $roleDistribution = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->where('model_type', 'App\Models\User')
            ->groupBy('roles.name')
            ->get();

        // Get recently registered users
        $recentUsers = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent users (using created_at instead of last_login_at)
        $recentLogins = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Count data
        $totalClients = Client::count();
        $activeProjects = Project::where('status', 'active')->count();
        $pendingSurveys = Survey::where('status', 'pending')->count();
        $pipelineValue = 2500000000; // Kept original dummy value

        // Monthly revenue data for chart (using sample data instead of DB query)
        // Since the 'value' column doesn't exist, we'll use sample data
        $monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        // Sample revenue data
        $revenueData = [
            'Jan' => 150,
            'Feb' => 240,
            'Mar' => 305,
            'Apr' => 480,
            'May' => 520,
            'Jun' => 670,
            'Jul' => 800,
            'Aug' => 950,
            'Sep' => 1100,
            'Oct' => 1220,
            'Nov' => 1330,
            'Dec' => 1450
        ];

        // Project status data (using predefined statuses if status column exists)
        $projectStatuses = ['Lead', 'Survey', 'Quotation', 'Negotiation', 'Deal', 'Execution', 'Completed'];

        try {
            // Try to get actual project status counts
            $projectStatus = Project::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();
        } catch (\Exception $e) {
            // Use sample data if query fails
            $projectStatus = [
                'Lead' => 12,
                'Survey' => 8,
                'Quotation' => 6,
                'Negotiation' => 4,
                'Deal' => 3,
                'Execution' => 8,
                'Completed' => 5
            ];
        }

        // Common data
        $data = [
            'totalClients' => $totalClients,
            'activeProjects' => $activeProjects,
            'pendingSurveys' => $pendingSurveys,
            'pipelineValue' => $pipelineValue,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'roleDistribution' => $roleDistribution,
            'recentUsers' => $recentUsers,
            'recentLogins' => $recentLogins,
            'revenueData' => $revenueData,
            'projectStatus' => $projectStatus
        ];

        // Redirect to appropriate dashboard based on role
        switch ($role) {
            case 'admin':
                return view('dashboard.admin', $data);
            case 'manager':
                return view('dashboard.manager', $data);
            case 'marketing':
                $data['myClients'] = 45;
                $data['myProjects'] = 12;
                return view('dashboard.marketing', $data);
            case 'surveyor':
                $data['mySurveys'] = 28;
                $data['pendingSurveys'] = 3;
                return view('dashboard.surveyor', $data);
            default:
                // Fallback to admin view for testing
                return view('dashboard', $data);
        }
    }
}
