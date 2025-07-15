<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container-fluid">
        <!-- Logo & Toggle -->
        <div class="d-flex align-items-center">
            <button class="navbar-toggler border-0 me-3" type="button" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Hansen CRM" height="32" class="me-2">
                Hansen CRM
            </a>
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="d-none d-md-flex flex-grow-1 mx-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Cari klien, proyek, atau dokumen...">
            </div>
        </div>

        <!-- Right Menu -->
        <div class="d-flex align-items-center">
            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-link text-dark position-relative p-2" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow" style="width: 350px;">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notifikasi</h6>
                        <a href="#" class="text-primary small">Tandai Semua Dibaca</a>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item py-3" href="#">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                    <i class="bi bi-clipboard-check text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">Survey baru ditugaskan untuk Anda</p>
                                <small class="text-muted">PT. Maju Jaya - 5 menit lalu</small>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item py-3" href="#">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                                    <i class="bi bi-clock-history text-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">Reminder: Follow up penawaran</p>
                                <small class="text-muted">CV. Berkah Abadi - 1 jam lalu</small>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center py-2" href="{{ route('notifications.index') }}">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-link text-dark d-flex align-items-center p-2" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}"
                         alt="Profile"
                         class="rounded-circle me-2"
                         width="32"
                         height="32">
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    <i class="bi bi-chevron-down ms-2 small"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow">
                    <div class="dropdown-header">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <div class="small text-muted">{{ Auth::user()->email }}</div>
                        <div class="badge bg-primary mt-1">{{ ucfirst(Auth::user()->role) }}</div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i> Profil Saya
                    </a>
                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="bi bi-gear me-2"></i> Pengaturan
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
