@extends('layouts.app')

@section('title', 'Dashboard Marketing')

@section('content')
<div class="container-fluid px-0">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Dashboard Marketing</h4>
            <p class="text-muted">Selamat datang, {{ Auth::user()->name }}! Berikut ringkasan aktivitas Anda.</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-value">{{ $myClients ?? 0 }}</div>
                <div class="stat-label">Klien Saya</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-value">{{ $myProjects ?? 0 }}</div>
                <div class="stat-label">Proyek Aktif</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-value">5</div>
                <div class="stat-label">Follow Up Hari Ini</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Penawaran Pending</div>
            </div>
        </div>
    </div>
    
    <!-- Today's Tasks -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Tugas Hari Ini
                    </h6>
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Tambah
                    </a>
                </div>
                <div class="card-body">
                    <div class="task-list">
                        <div class="task-item d-flex align-items-start mb-3">
                            <input type="checkbox" class="form-check-input me-3 mt-1">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Follow up PT. Maju Jaya</div>
                                <small class="text-muted">Konfirmasi penawaran renovasi kantor</small>
                                <div class="mt-1">
                                    <span class="badge bg-warning">10:00 AM</span>
                                    <span class="badge bg-light text-dark">High Priority</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="task-item d-flex align-items-start mb-3">
                            <input type="checkbox" class="form-check-input me-3 mt-1">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Meeting dengan CV. Berkah</div>
                                <small class="text-muted">Presentasi design showroom</small>
                                <div class="mt-1">
                                    <span class="badge bg-primary">2:00 PM</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="task-item d-flex align-items-start">
                            <input type="checkbox" class="form-check-input me-3 mt-1" checked>
                            <div class="flex-grow-1 text-decoration-line-through opacity-50">
                                <div class="fw-semibold">Kirim invoice ke Kopi Nusantara</div>
                                <small class="text-muted">Invoice progress 50%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-activity me-2"></i>
                        Aktivitas Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="bi bi-check-circle text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="fw-semibold">Kontrak ditandatangani</div>
                                <p class="text-muted mb-1">PT. Sentosa Jaya - Renovasi Outlet</p>
                                <small class="text-muted">2 jam yang lalu</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="bi bi-send text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="fw-semibold">Penawaran terkirim</div>
                                <p class="text-muted mb-1">CV. Makmur - Interior Kafe</p>
                                <small class="text-muted">5 jam yang lalu</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="bi bi-telephone text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="fw-semibold">Follow up call</div>
                                <p class="text-muted mb-1">PT. Indah Persada - Showroom</p>
                                <small class="text-muted">Kemarin</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- My Pipeline -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>
                        Pipeline Saya
                    </h6>
                    <a href="{{ route('pipeline.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Klien</th>
                                    <th>Proyek</th>
                                    <th>Status</th>
                                    <th>Nilai</th>
                                    <th>Last Update</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PT. Maju Jaya</td>
                                    <td>Renovasi Kantor Lt. 2</td>
                                    <td><span class="badge bg-warning">Penawaran</span></td>
                                    <td>Rp 450 Juta</td>
                                    <td>2 hari lalu</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>CV. Berkah Abadi</td>
                                    <td>Pembangunan Showroom</td>
                                    <td><span class="badge bg-info">Negosiasi</span></td>
                                    <td>Rp 1.2 Miliar</td>
                                    <td>1 minggu lalu</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kopi Nusantara</td>
                                    <td>Interior Kafe Cabang 3</td>
                                    <td><span class="badge bg-primary">Eksekusi</span></td>
                                    <td>Rp 280 Juta</td>
                                    <td>3 hari lalu</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.task-list .task-item:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 1rem;
}

.activity-timeline {
    position: relative;
    padding-left: 40px;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 20px;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.activity-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.activity-icon {
    position: absolute;
    left: -25px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
</style>
@endpush