<div class="position-sticky pt-3">
    <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @php
            // Get user role using Spatie Permission
            $userRole = Auth::user()->getRoleNames()->first() ?? '';
        @endphp

        @if(in_array($userRole, ['admin', 'manager', 'marketing']))
        <!-- Clients -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
               href="{{ route('clients.index') }}">
                <i class="bi bi-people"></i>
                <span>Data Klien</span>
            </a>
        </li>

        <!-- Projects -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}"
               href="{{ route('projects.index') }}">
                <i class="bi bi-building"></i>
                <span>Proyek</span>
            </a>
        </li>
        @endif

        <!-- Survey -->
        @if(in_array($userRole, ['admin', 'surveyor', 'marketing']))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('surveys.*') ? 'active' : '' }}"
               href="{{ route('surveys.index') }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Survey</span>
                @if($userRole == 'surveyor' && isset($pendingSurveys) && $pendingSurveys > 0)
                <span class="badge bg-danger ms-auto">{{ $pendingSurveys }}</span>
                @endif
            </a>
        </li>
        @endif

        @if(in_array($userRole, ['admin', 'manager', 'marketing']))
        <!-- Pipeline -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pipeline.*') ? 'active' : '' }}"
               href="{{ route('pipeline.index') }}">
                <i class="bi bi-funnel"></i>
                <span>Pipeline</span>
            </a>
        </li>

        <!-- Documents -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
               href="{{ route('documents.index') }}">
                <i class="bi bi-folder"></i>
                <span>Dokumen</span>
            </a>
        </li>

        <!-- Reports -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
               href="{{ route('reports.index') }}">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                <span>Reports</span>
            </a>
        </li>
        @endif

        @if(in_array($userRole, ['admin', 'manager']))
        <!-- Reports Section -->
        <li class="nav-item mt-3">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Laporan</span>
            </h6>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.conversion') ? 'active' : '' }}"
               href="{{ route('reports.conversion') }}">
                <i class="bi bi-graph-up"></i>
                <span>Konversi</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.revenue') ? 'active' : '' }}"
               href="{{ route('reports.revenue') }}">
                <i class="bi bi-currency-dollar"></i>
                <span>Revenue</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.performance') ? 'active' : '' }}"
               href="{{ route('reports.performance') }}">
                <i class="bi bi-bar-chart"></i>
                <span>Performa</span>
            </a>
        </li>
        @endif

        @if($userRole == 'admin')
        <!-- Admin Section -->
        <li class="nav-item mt-3">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Administrasi</span>
            </h6>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
               href="{{ route('users.index') }}">
                <i class="bi bi-person-badge"></i>
                <span>Pengguna</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
               href="{{ route('settings.index') }}">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
        </li>
        @endif
    </ul>

    @if(in_array(Auth::user()->getRoleNames()->first(), ['admin', 'manager']))
    <!-- Under Pipeline menu item -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pipeline.analytics') ? 'active' : '' }}"
        href="{{ route('pipeline.analytics') }}">
            <i class="bi bi-graph-up me-2"></i>
            <span>Pipeline Analytics</span>
        </a>
    </li>
    @endif


    <!-- Storage Info -->
    <div class="px-3 mt-5">
        <div class="small text-muted mb-2">Penyimpanan</div>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar" role="progressbar" style="width: 65%"></div>
        </div>
        <div class="small text-muted mt-1">6.5 GB dari 10 GB</div>
    </div>
</div>
