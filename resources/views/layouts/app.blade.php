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
            --sidebar-width: 280px;
            --navbar-height: 70px;
            --primary-color: #1a73e8;
            --primary-dark: #1557b0;
            --primary-light: #eef5fe;
            --secondary-color: #f0f4f8;
            --text-primary: #344767;
            --text-secondary: #718096;
            --border-color: #e9ecef;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-primary);
        }

        /* Navbar - Modern Blue Theme */
        .navbar {
            height: var(--navbar-height);
            background: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            z-index: 1040;
            padding: 0 1.5rem;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .navbar-logo {
            height: 40px;
            width: auto;
            transition: all 0.3s;
            filter: drop-shadow(0 0 3px rgba(255,255,255,0.2));
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 1.3rem;
            color: white !important;
            padding: 0;
            margin-right: 1.5rem;
        }

        .navbar-brand img {
            margin-right: 12px;
            height: 36px;
        }

        .navbar .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .navbar .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Notification Styles */
        .notification-menu {
            width: 380px;
            max-height: 500px;
            overflow-y: auto;
            padding: 0;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .notification-dropdown-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .notification-dropdown-item:hover {
            background-color: var(--secondary-color);
        }

        .notification-badge {
            position: absolute;
            top: 0px;
            right: 0px;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #ff3e5e;
            color: white;
            font-size: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: translate(50%, -50%);
        }

        .notification-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* Toast Container */
        .toast-container {
            z-index: 1060;
        }

        .notification-toast {
            min-width: 320px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }

        /* Sidebar - Modern Design */
        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            z-index: 1030;
            transition: all 0.3s;
            padding-top: 1.5rem;
        }

        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .sidebar .nav-item {
            padding: 0 1.25rem;
            margin-bottom: 0.5rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            padding: 0.85rem 1rem;
            border-radius: 10px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.85rem;
            color: inherit;
        }

        .sidebar .nav-section {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            font-weight: 600;
            padding: 1.25rem 1.25rem 0.5rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
        }

        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-menu:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-menu img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }

        .user-menu-name {
            margin-left: 0.75rem;
            margin-right: 0.75rem;
            font-weight: 500;
        }

        .user-dropdown {
            width: 280px;
            padding: 0;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .user-dropdown-header {
            background-color: var(--primary-light);
            padding: 1.25rem;
            text-align: center;
        }

        .user-dropdown-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 0.75rem;
        }

        .user-dropdown-header .user-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .user-dropdown-header .user-email {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .user-dropdown-header .user-role {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-top: 0.5rem;
        }

        .user-dropdown-item {
            padding: 0.85rem 1.25rem;
            display: flex;
            align-items: center;
            color: var(--text-primary);
            transition: all 0.2s;
        }

        .user-dropdown-item:hover {
            background-color: var(--secondary-color);
        }

        .user-dropdown-item i {
            font-size: 1.1rem;
            margin-right: 0.75rem;
            color: var(--text-secondary);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            background-color: #fff;
            overflow: hidden;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* PERBAIKAN: Pastikan navbar selalu dapat menyimpan ikon di kanan atas */
        .navbar-right-icons {
            display: flex;
            align-items: center;
        }

        /* PERBAIKAN: Memastikan navbar tetap menampilkan tombol-tombol penting */
        @media (max-width: 992px) {
            .navbar-logo {
                height: 30px; /* Ukuran lebih kecil untuk tampilan mobile */
            }
            .navbar-collapse {
                background-color: var(--primary-dark);
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                z-index: 1050;
                padding: 1rem;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            }

            .navbar-right-icons {
                margin-left: auto;
                display: flex !important;
            }

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

    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid px-0">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Hansen Construction" class="navbar-logo">
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0 shadow-none text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list" style="font-size: 1.8rem;"></i>
            </button>

            <!-- Sidebar Toggle for mobile -->
            <button class="btn btn-link text-white d-lg-none p-0 me-3" type="button" id="sidebarToggle">
                <i class="bi bi-layout-sidebar" style="font-size: 1.4rem;"></i>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Side Menu (Optional) -->
                <ul class="navbar-nav me-auto">
                    <!-- Additional menu items can be added here if needed -->
                </ul>
            </div>

            <!-- Right aligned icons - using ms-auto to push to the right -->
            <div class="navbar-right-icons ms-auto d-flex align-items-center">
                <!-- Notification Bell -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link position-relative px-2" href="#" id="notificationDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill" style="font-size: 1.3rem;"></i>
                        <span class="notification-badge"
                            style="display: none;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown">
                        <div class="dropdown-header d-flex justify-content-between align-items-center p-3">
                            <h6 class="mb-0 fw-bold">Notifikasi</h6>
                            <a href="{{ route('notifications.index') }}" class="text-decoration-none small text-primary">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="dropdown-divider m-0"></div>
                        <div class="notification-list-dropdown" id="notificationList">
                            <!-- This will show the existing notification content as in the original design -->
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider m-0"></div>
                        <div class="text-center py-3">
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light text-primary fw-medium">
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown - PERBAIKAN: Menambahkan class dropdown dan data-bs-toggle -->
                <div class="nav-item dropdown">
                    <a class="user-menu dropdown-toggle" href="#" id="userDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1a73e8&color=fff"
                            alt="{{ Auth::user()->name }}">
                        <span class="user-menu-name d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                        <div class="user-dropdown-header">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1a73e8&color=fff"
                                alt="{{ Auth::user()->name }}">
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-email">{{ Auth::user()->email }}</div>
                            <div class="user-role">{{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}</div>
                        </div>
                        <div class="user-dropdown-body">
                            <a class="user-dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a class="user-dropdown-item" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell"></i>
                                <span>Notifikasi</span>
                            </a>
                            @if(Auth::user()->hasRole('admin'))
                            <a class="user-dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear"></i>
                                <span>Pengaturan</span>
                            </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                @csrf
                                <button type="submit" class="user-dropdown-item text-danger w-100 text-start">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modern Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
            @php
                $userRole = Auth::user()->getRoleNames()->first() ?? '';
            @endphp

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(in_array($userRole, ['admin', 'manager', 'marketing']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Clients</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <i class="bi bi-building"></i>
                        <span>Projects</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('surveys.*') ? 'active' : '' }}" href="{{ route('surveys.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Survey</span>
                    </a>
                </li>

                @if(in_array($userRole, ['admin', 'manager', 'marketing']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pipeline.*') ? 'active' : '' }}" href="{{ route('pipeline.index') }}">
                        <i class="bi bi-funnel"></i>
                        <span>Pipeline</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Reports</span>
                    </a>
                </li>
                @endif

                @if(in_array($userRole, ['admin', 'manager']))
                <div class="nav-section">Analytics</div>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pipeline.analytics') ? 'active' : '' }}" href="{{ route('pipeline.analytics') }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Pipeline Analytics</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                @if($userRole == 'admin')
                <div class="nav-section">Administration</div>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Users</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif
            </ul>

            <!-- Storage Info -->
            <div class="px-4 mt-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Storage</span>
                    <span class="text-muted small">65%</span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; overflow: hidden;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 65%"></div>
                </div>
                <div class="text-muted small mt-2">6.5 GB of 10 GB used</div>
            </div>
        </div>
    </div>

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

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Scripts -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        // Notification Scripts
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        // Listen for notifications
        Echo.private('App.Models.User.{{ Auth::id() }}')
            .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(notification) {
                showNotificationToast(notification);
                updateNotificationBadge();
                loadNotificationDropdown();
                playNotificationSound();
            });

        // Listen for custom events
        Echo.private('user.' + {{ Auth::id() }})
            .listen('.survey.assigned', (e) => {
                showNotification('Survey Baru', `Anda telah ditugaskan untuk survey "${e.title}" untuk klien ${e.client_name}`, 'info');
                const pendingSurveysElement = document.getElementById('pending-surveys-count');
                if (pendingSurveysElement) {
                    const currentCount = parseInt(pendingSurveysElement.textContent);
                    pendingSurveysElement.textContent = currentCount + 1;
                }
            });

        // Notification Badge
        function updateNotificationBadge() {
            fetch('{{ route("notifications.unread") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.notification-badge');
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }

        // Initial notification load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationBadge();
            loadNotificationDropdown();
        });

        // Load notifications for dropdown - using existing notification system
        function loadNotificationDropdown() {
            // Using the existing notification system without new routes
            const notificationList = document.getElementById('notificationList');
            if (notificationList) {
                // Display a placeholder or use existing system's loading state
                notificationList.innerHTML = '<div class="text-center py-4">Memuat notifikasi...</div>';

                // We'll use the existing system's route if available in the future
                // For now, just show a placeholder UI to avoid route errors
            }
        }

        function createNotificationItem(notification) {
            // Placeholder function retained but not used to avoid errors
            // This can be used if you implement the dropdown route in the future
            const iconClass = getNotificationIconClass(notification.type);
            const bgClass = 'bg-primary bg-opacity-10 text-primary';
            return `
                <a href="#" class="notification-dropdown-item d-flex align-items-start">
                    <div class="notification-icon ${bgClass} me-3">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-medium mb-1">${notification.title}</div>
                        <div class="text-muted small">${notification.message || ''}</div>
                        <div class="text-muted small mt-1">Baru saja</div>
                    </div>
                </a>
            `;
        }

        function getNotificationIconClass(type) {
            const icons = {
                'project_status_changed': 'bi-building',
                'survey_assigned': 'bi-clipboard-check',
                'document_uploaded': 'bi-file-earmark',
                'client_assigned': 'bi-person-plus'
            };
            return icons[type] || 'bi-bell';
        }

        function getNotificationBgClass(type) {
            // Simple function retained but simplified to avoid errors
            return 'bg-primary bg-opacity-10 text-primary';
        }

        // Notification Toast
        function showNotificationToast(notification) {
            const title = notification.title || 'Notifikasi Baru';
            const message = notification.body || notification.message || (notification.data ? notification.data.message : '');
            const type = notification.type || 'info';
            const time = notification.time || new Date().toLocaleTimeString();

            const toastHtml = `
                <div class="toast notification-toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="${getNotificationIconClass(type)} text-primary me-2"></i>
                        <strong class="me-auto">${title}</strong>
                        <small>${time}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                const container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(container);
                toastContainer = container;
            }
            const toastElement = document.createElement('div');
            toastElement.innerHTML = toastHtml;
            toastContainer.appendChild(toastElement.firstElementChild);
            const toast = new bootstrap.Toast(toastElement.firstElementChild, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        }

        function playNotificationSound() {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Could not play notification sound'));
        }

        // Initialize notification functions
        updateNotificationBadge();
    </script>

    {{ $scripts ?? '' }}

    <!-- Notification Container -->
    <div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
</body>
</html>
