<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Hansen CRM</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        :root {
            --sidebar-width: 250px;
            --navbar-height: 60px;
            --primary-color: #1a73e8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
        }

        /* Navbar */
        .navbar {
            height: var(--navbar-height);
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            z-index: 1030;
        }

        /* Fix dropdown z-index */
        .dropdown-menu {
            z-index: 1040;
        }

        /* Notification Styles */
        .notification-menu {
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-dropdown-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .notification-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .notification-icon-small {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .notification-badge {
            font-size: 0.65em;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Toast Container */
        .toast-container {
            z-index: 1060;
        }

        .notification-toast {
            min-width: 300px;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: #fff;
            border-right: 1px solid #e0e0e0;
            overflow-y: auto;
            z-index: 1020;
            transition: all 0.3s;
        }

        .sidebar .nav-link {
            color: #6c757d;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 0.25rem;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(26, 115, 232, 0.08);
            color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            background-color: rgba(26, 115, 232, 0.12);
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            background-color: #fff;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Stat Card */
        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: 100%;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        /* Icon Box */
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Chart Container Fix */
        .chart-container {
            position: relative;
            height: 300px !important;
            width: 100%;
        }

        .chart-container-small {
            position: relative;
            height: 200px !important;
            width: 100%;
        }

        /* Project Status Chart Styles */
        .project-status-bar {
            height: 25px;
            margin: 8px 0;
            border-radius: 4px;
            background-color: #f0f0f0;
            overflow: hidden;
        }

        .project-status-bar .progress-bar {
            height: 100%;
        }

        /* Table */
        .table {
            font-size: 14px;
        }

        .table thead th {
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            color: #6c757d;
        }

        /* Form Label Required */
        .form-label.required:after {
            content: " *";
            color: red;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.active {
                margin-left: var(--sidebar-width);
            }
            .notification-menu {
                width: 300px;
            }

            .notification-dropdown .nav-link {
                padding: 0.5rem 0.75rem;
            }
        }

        /* Fix dropdown positioning */
        .navbar .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
        }
    </style>

    <!-- Pusher JS and Echo Polyfill -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.0.3/dist/web/pusher.min.js"></script>
    <script>
        // Definisikan Echo global
        window.Echo = (function() {
            const Echo = function(options) {
                this.options = options;
                this.connector = options.broadcaster === 'pusher' ? new Pusher(options.key, {
                    cluster: options.cluster,
                    forceTLS: options.forceTLS
                }) : null;
            };

            Echo.prototype.channel = function(channel) {
                const self = this;
                return {
                    listen: function(event, callback) {
                        if (self.connector) {
                            const ch = self.connector.subscribe(channel);
                            ch.bind(event, callback);
                        }
                        return this;
                    }
                };
            };

            Echo.prototype.private = function(channel) {
                return this.channel('private-' + channel);
            };

            Echo.prototype.presence = function(channel) {
                return this.channel('presence-' + channel);
            };

            return Echo;
        })();
    </script>

    <!-- Chart.js in the head for earlier loading -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{ $styles ?? '' }}
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-building me-2"></i>Hansen CRM
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Side Menu (Optional) -->
                <ul class="navbar-nav me-auto">
                    <!-- Menu items bisa ditambahkan di sini jika diperlukan -->
                </ul>

                <!-- Right Side Menu -->
                <ul class="navbar-nav ms-auto">
                    <!-- Notification Bell -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  id="notificationBadge" style="display: none;">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifikasi</h6>
                                <a href="{{ route('notifications.index') }}" class="text-decoration-none small">
                                    Lihat Semua
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <div class="notification-list-dropdown px-3" id="notificationList">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <div class="dropdown-footer text-center px-3 py-2">
                                    <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=fff&color=1a73e8"
                                 alt="{{ Auth::user()->name }}"
                                 class="rounded-circle me-2"
                                 width="32" height="32">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                    <i class="bi bi-bell me-2"></i>Notifikasi
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>

                @php
                    $userRole = Auth::user()->getRoleNames()->first() ?? '';
                @endphp

                @if(in_array($userRole, ['admin', 'manager', 'marketing']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                        <i class="bi bi-people me-2"></i>Clients
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <i class="bi bi-building me-2"></i>Projects
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('surveys.*') ? 'active' : '' }}" href="{{ route('surveys.index') }}">
                        <i class="bi bi-clipboard-check me-2"></i>Survey
                    </a>
                </li>

                @if(in_array($userRole, ['admin', 'manager', 'marketing']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                    href="{{ route('reports.index') }}">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>
                        <span>Reports</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile') || request()->routeIs('profile.edit') ? 'active' : '' }}"
                       href="{{ route('profile.edit') }}">
                        <i class="bi bi-person-circle me-2"></i>My Profile
                    </a>
                </li>

                @if($userRole == 'admin')
                <li class="nav-item mt-3">
                    <h6 class="text-muted px-3">ADMIN</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-people-fill me-2"></i>Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                    href="{{ route('settings.index') }}">
                        <i class="bi bi-gear me-2"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="content">
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Dashboard Content Example -->
        <div class="row g-4 mb-4">
            <!-- Active Clients -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-0">Active Clients</h6>
                            <h3 class="stat-value">6</h3>
                            <p class="small text-success mb-0">+12% vs last month</p>
                        </div>
                        <div class="icon-box bg-primary-subtle">
                            <i class="bi bi-people text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ongoing Projects -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-0">Ongoing Projects</h6>
                            <h3 class="stat-value">8</h3>
                            <p class="small text-warning mb-0">8 on schedule</p>
                        </div>
                        <div class="icon-box bg-warning-subtle">
                            <i class="bi bi-building text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Surveys -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-0">Pending Surveys</h6>
                            <h3 class="stat-value">4</h3>
                            <p class="small text-danger mb-0">Need attention</p>
                        </div>
                        <div class="icon-box bg-danger-subtle">
                            <i class="bi bi-clipboard-check text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-0">Revenue</h6>
                            <h3 class="stat-value">2,500.0M</h3>
                            <p class="small text-success mb-0">+18% growth</p>
                        </div>
                        <div class="icon-box bg-success-subtle">
                            <i class="bi bi-cash-coin text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Overview -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Revenue Overview</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="revenueFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                This Year
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="revenueFilterDropdown">
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Quarter</a></li>
                                <li><a class="dropdown-item active" href="#">This Year</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Custom Range</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Status -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Project Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Lead</span>
                            <span class="fw-bold">12</span>
                        </div>
                        <div class="project-status-bar">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 20%"></div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Survey</span>
                            <span class="fw-bold">8</span>
                        </div>
                        <div class="project-status-bar">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 14%"></div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Negotiation</span>
                            <span class="fw-bold">15</span>
                        </div>
                        <div class="project-status-bar">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 25%"></div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Execution</span>
                            <span class="fw-bold">24</span>
                        </div>
                        <div class="project-status-bar">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 41%"></div>
                        </div>

                        <div class="mt-4">
                            <canvas id="projectStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Projects</h5>
                <a href="{{ route('projects.index') }}" class="btn btn-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>PROJECT</th>
                                <th>CLIENT</th>
                                <th>STATUS</th>
                                <th>PROGRESS</th>
                                <th>VALUE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Office Renovation</td>
                                <td>PT. Maju Jaya</td>
                                <td>
                                    <span class="badge bg-warning text-dark">Survey</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 120px">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"></div>
                                    </div>
                                </td>
                                <td>Rp 450M</td>
                            </tr>
                            <tr>
                                <td>Warehouse Construction</td>
                                <td>PT. Sejahtera Group</td>
                                <td>
                                    <span class="badge bg-success">Execution</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 120px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                                    </div>
                                </td>
                                <td>Rp 1.2B</td>
                            </tr>
                            <tr>
                                <td>Retail Store Design</td>
                                <td>Indah Retail</td>
                                <td>
                                    <span class="badge bg-info">Negotiation</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 120px">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
                                    </div>
                                </td>
                                <td>Rp 380M</td>
                            </tr>
                            <tr>
                                <td>Hotel Renovation</td>
                                <td>Grand Hospitality</td>
                                <td>
                                    <span class="badge bg-success">Execution</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 120px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%"></div>
                                    </div>
                                </td>
                                <td>Rp 850M</td>
                            </tr>
                            <tr>
                                <td>Office Tower Project</td>
                                <td>Mega Developments</td>
                                <td>
                                    <span class="badge bg-secondary">Lead</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 120px">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 10%"></div>
                                    </div>
                                </td>
                                <td>Rp 5.7B</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Container for Notifications -->
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Scripts -->
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Auto-hide alerts after 5 seconds
        window.setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Initialize dropdowns manually
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
            const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));

            // Ensure proper z-index handling
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.addEventListener('show.bs.dropdown', function () {
                    this.style.zIndex = '1050';
                });
                dropdown.addEventListener('hide.bs.dropdown', function () {
                    this.style.zIndex = '';
                });
            });
        });

        // Revenue Chart
        document.addEventListener('DOMContentLoaded', function() {
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                const revenueChart = new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Revenue 2025',
                            data: [180, 210, 245, 270, 310, 350, 400, 450, 480, 520, 560, 600],
                            backgroundColor: 'rgba(26, 115, 232, 0.1)',
                            borderColor: '#1a73e8',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }, {
                            label: 'Revenue 2024',
                            data: [150, 180, 200, 220, 250, 280, 310, 330, 360, 390, 410, 450],
                            borderColor: '#aaaaaa',
                            borderDash: [5, 5],
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [2, 4],
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + 'M';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

            // Project Status Chart
            const projectCtx = document.getElementById('projectStatusChart');
            if (projectCtx) {
                const projectChart = new Chart(projectCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Lead', 'Survey', 'Negotiation', 'Execution'],
                        datasets: [{
                            data: [12, 8, 15, 24],
                            backgroundColor: [
                                '#6c757d',
                                '#0d6efd',
                                '#ffc107',
                                '#198754'
                            ],
                            borderWidth: 0,
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
</body>
</html>
