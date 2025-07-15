<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1a73e8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    
    @stack('styles')
</head>
<body class="mobile-body">
    <!-- Mobile Header -->
    <nav class="mobile-header fixed-top bg-primary text-white">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center">
                    @if(!request()->routeIs('dashboard'))
                    <a href="javascript:history.back()" class="text-white me-3">
                        <i class="bi bi-arrow-left fs-4"></i>
                    </a>
                    @endif
                    <h5 class="mb-0">@yield('title', 'Hansen CRM')</h5>
                </div>
                <div>
                    @yield('header-actions')
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="mobile-main">
        @yield('content')
    </main>
    
    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav fixed-bottom bg-white border-top">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-3">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('surveys.index') }}" class="nav-link {{ request()->routeIs('surveys.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Survey</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i class="bi bi-bell"></i>
                        <span>Notifikasi</span>
                        @if($unreadNotifications ?? 0 > 0)
                        <span class="badge bg-danger">{{ $unreadNotifications }}</span>
                        @endif
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person"></i>
                        <span>Profil</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js');
        }
    </script>
    
    @stack('scripts')
</body>
</html>