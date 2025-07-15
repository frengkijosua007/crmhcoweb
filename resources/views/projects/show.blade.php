@extends('layouts.app')

@section('title', 'Detail Project')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">{{ $project->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Project</a></li>
                    <li class="breadcrumb-item active">{{ $project->code }}</li>
                </ol>
            </nav>
        </div>
        <div>
            @can('edit-projects')
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            @endcan
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-circle me-2"></i>Tambah
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('surveys.create', ['project_id' => $project->id]) }}">
                            <i class="bi bi-clipboard-check me-2"></i>Survey
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-file-earmark-text me-2"></i>Dokumen
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-cash me-2"></i>Invoice
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Status Progress -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-2">Progress Project</h6>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-{{ $project->status_badge }}" 
                             role="progressbar" 
                             style="width: {{ $project->progress_percentage }}%">
                            {{ $project->progress_percentage }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Lead</span>
                        <span>Survey</span>
                        <span>Penawaran</span>
                        <span>Negosiasi</span>
                        <span>Deal</span>
                        <span>Eksekusi</span>
                        <span>Selesai</span>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <h6 class="text-muted mb-1">Status Saat Ini</h6>
                    <h4>
                        <span class="badge bg-{{ $project->status_badge }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Project Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Project</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Kode Project:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-secondary">{{ $project->code }}</span>
                                </dd>
                                
                                <dt class="col-sm-4">Jenis:</dt>
                                <dd class="col-sm-8">{{ ucfirst($project->type) }}</dd>
                                
                                <dt class="col-sm-4">Klien:</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('clients.show', $project->client) }}">
                                        {{ $project->client->name }}
                                    </a>
                                </dd>
                                
                                <dt class="col-sm-4">PIC:</dt>
                                <dd class="col-sm-8">{{ $project->pic->name }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Lokasi:</dt>
                                <dd class="col-sm-8">{{ $project->location }}</dd>
                                
                                <dt class="col-sm-4">Mulai:</dt>
                                <dd class="col-sm-8">
                                    {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
                                </dd>
                                
                                <dt class="col-sm-4">Target:</dt>
                                <dd class="col-sm-8">
                                    {{ $project->target_date ? $project->target_date->format('d M Y') : '-' }}
                                </dd>
                                
                                <dt class="col-sm-4">Nilai Project:</dt>
                                <dd class="col-sm-8">
                                    @if($project->deal_value)
                                    <strong class="text-success">Rp {{ number_format($project->deal_value, 0, ',', '.') }}</strong>
                                    <br><small class="text-muted">Deal</small>
                                    @elseif($project->project_value)
                                    Rp {{ number_format($project->project_value, 0, ',', '.') }}
                                    <br><small class="text-muted">Estimasi</small>
                                    @else
                                    <span class="text-muted">TBD</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($project->description)
                    <hr>
                    <h6>Deskripsi:</h6>
                    <p class="mb-0">{{ $project->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#surveys">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Surveys ({{ $project->surveys->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#documents">
                                <i class="bi bi-folder me-2"></i>
                                Dokumen (0)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#timeline">
                                <i class="bi bi-clock-history me-2"></i>
                                Timeline
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Surveys Tab -->
                        <div class="tab-pane fade show active" id="surveys">
                            @if($project->surveys->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Surveyor</th>
                                            <th>Status</th>
                                            <th>Foto</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->surveys as $survey)
                                        <tr>
                                            <td>
                                                <div>{{ $survey->scheduled_date->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $survey->scheduled_date->format('H:i') }}</small>
                                            </td>
                                            <td>{{ $survey->surveyor->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $survey->status_badge }}">
                                                    {{ ucfirst($survey->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $survey->photos->count() }} foto
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('surveys.show', $survey) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted">Belum ada survey untuk project ini</p>
                                <a href="{{ route('surveys.create', ['project_id' => $project->id]) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-2"></i>Jadwalkan Survey
                                </a>
                            </div>
                            @endif
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents">
                            <div class="text-center py-4">
                                <i class="bi bi-file-earmark fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted">Belum ada dokumen</p>
                                <button class="btn btn-primary btn-sm">
                                    <i class="bi bi-upload me-2"></i>Upload Dokumen
                                </button>
                            </div>
                        </div>

                        <!-- Timeline Tab -->
                        <div class="tab-pane fade" id="timeline">
                            <div class="activity-timeline">
                                @if($project->status != 'lead')
                                <div class="activity-item">
                                    <div class="activity-icon bg-{{ $project->status_badge }}">
                                        <i class="bi bi-flag text-white"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="fw-semibold">Status diubah ke {{ ucfirst($project->status) }}</div>
                                        <small class="text-muted">{{ $project->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endif

                                @foreach($project->surveys->sortByDesc('created_at') as $survey)
                                <div class="activity-item">
                                    <div class="activity-icon bg-info">
                                        <i class="bi bi-clipboard-check text-white"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="fw-semibold">Survey dijadwalkan</div>
                                        <p class="text-muted mb-1">
                                            Surveyor: {{ $survey->surveyor->name }}<br>
                                            Tanggal: {{ $survey->scheduled_date->format('d M Y H:i') }}
                                        </p>
                                        <small class="text-muted">{{ $survey->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach

                                <div class="activity-item">
                                    <div class="activity-icon bg-success">
                                        <i class="bi bi-plus-circle text-white"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="fw-semibold">Project dibuat</div>
                                        <p class="text-muted mb-1">Oleh: {{ $project->pic->name }}</p>
                                        <small class="text-muted">{{ $project->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($project->status == 'lead')
                        <a href="{{ route('surveys.create', ['project_id' => $project->id]) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-clipboard-check me-2"></i>Jadwalkan Survey
                        </a>
                        @endif
                        
                        @if(in_array($project->status, ['survey', 'penawaran']))
                        <button class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-text me-2"></i>Buat Penawaran
                        </button>
                        @endif
                        
                        @if($project->status == 'deal')
                        <button class="btn btn-outline-info">
                            <i class="bi bi-file-earmark-check me-2"></i>Generate Kontrak
                        </button>
                        @endif
                        
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-2"></i>Print Detail
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Kontak Klien</h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">{{ $project->client->name }}</h6>
                    <dl class="row mb-0">
                        <dt class="col-5">Telepon:</dt>
                        <dd class="col-7">
                            <a href="tel:{{ $project->client->phone }}">{{ $project->client->phone }}</a>
                        </dd>
                        
                        @if($project->client->whatsapp)
                        <dt class="col-5">WhatsApp:</dt>
                        <dd class="col-7">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $project->client->whatsapp) }}" 
                               target="_blank" 
                               class="text-success">
                                <i class="bi bi-whatsapp"></i> Chat
                            </a>
                        </dd>
                        @endif
                        
                        @if($project->client->email)
                        <dt class="col-5">Email:</dt>
                        <dd class="col-7">
                            <a href="mailto:{{ $project->client->email }}">{{ $project->client->email }}</a>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-6 border-end">
                            <h4 class="mb-0">{{ $project->surveys->count() }}</h4>
                            <small class="text-muted">Total Survey</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0">0</h4>
                            <small class="text-muted">Dokumen</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <p class="text-muted mb-1">Durasi Project</p>
                        <h5 class="mb-0">
                            @if($project->start_date)
                            {{ $project->start_date->diffInDays($project->target_date ?? now()) }} hari
                            @else
                            -
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.activity-timeline {
    position: relative;
    padding-left: 40px;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.activity-item {
    position: relative;
    margin-bottom: 2rem;
}

.activity-item:last-child {
    margin-bottom: 0;
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

@push('scripts')
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush