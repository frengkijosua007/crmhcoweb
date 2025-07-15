@extends('layouts.app')

@section('title', 'Dashboard Surveyor')

@section('content')
<div class="container-fluid px-0">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Dashboard Surveyor</h4>
            <p class="text-muted">Hi {{ Auth::user()->name }}, ada {{ $pendingSurveys }} survey menunggu Anda.</p>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-value text-warning">{{ $pendingSurveys }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-value text-info">3</div>
                <div class="stat-label">Hari Ini</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-value text-success">{{ $mySurveys }}</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-value text-primary">95%</div>
                <div class="stat-label">On Time</div>
            </div>
        </div>
    </div>
    
    <!-- Today's Schedule -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="bi bi-calendar-event me-2"></i>
                Jadwal Hari Ini - {{ now()->format('l, d F Y') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">PT. Maju Jaya - Renovasi Kantor</h6>
                            <p class="mb-1 text-muted">
                                <i class="bi bi-geo-alt me-1"></i>
                                Jl. Sudirman No. 123, Jakarta Selatan
                            </p>
                            <span class="badge bg-warning">10:00 - 11:00 WIB</span>
                        </div>
                        <button class="btn btn-primary btn-sm">
                            <i class="bi bi-play-circle"></i> Mulai
                        </button>
                    </div>
                </a>
                
                <a href="#" class="list-group-item list-group-item-action py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">CV. Berkah - Showroom Mobil</h6>
                            <p class="mb-1 text-muted">
                                <i class="bi bi-geo-alt me-1"></i>
                                Jl. TB Simatupang No. 456, Jakarta Selatan
                            </p>
                            <span class="badge bg-info">14:00 - 15:30 WIB</span>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            <i class="bi bi-clock"></i> Nanti
                        </button>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Pending Surveys -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-hourglass-split me-2"></i>
                Survey Menunggu
            </h6>
            <a href="{{ route('surveys.index') }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Klien</th>
                            <th>Lokasi</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-semibold">Kopi Nusantara</div>
                                <small class="text-muted">Interior Kafe</small>
                            </td>
                            <td>
                                <small>Kemang, Jakarta Selatan</small>
                            </td>
                            <td>
                                <small>Besok, 09:00</small>
                            </td>
                            <td>
                                <span class="badge bg-warning">Pending</span>
                            </td>
                            <td>
                                <a href="{{ route('surveys.mobile.form') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-semibold">PT. Sentosa</div>
                                <small class="text-muted">Renovasi Outlet</small>
                            </td>
                            <td>
                                <small>Tangerang</small>
                            </td>
                            <td>
                                <small>Rabu, 10:00</small>
                            </td>
                            <td>
                                <span class="badge bg-warning">Pending</span>
                            </td>
                            <td>
                                <a href="{{ route('surveys.mobile.form') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions for Mobile -->
    <div class="position-fixed bottom-0 end-0 p-3 d-md-none">
        <a href="{{ route('surveys.mobile.form') }}" class="btn btn-primary btn-lg rounded-circle shadow">
            <i class="bi bi-camera"></i>
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    .stat-card {
        padding: 1rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
}
</style>
@endpush