@extends('layouts.app')

@section('title', 'Data Project')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Data Project</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Project</li>
                </ol>
            </nav>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="exportProjects()">
                <i class="bi bi-download me-2"></i>Export
            </button>
            @can('create-projects')
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Project
            </a>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Projects</p>
                            <h5 class="mb-0">{{ $projects->total() }}</h5>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="bi bi-building text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Active</p>
                            <h5 class="mb-0">{{ $projects->where('status', 'eksekusi')->count() }}</h5>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="bi bi-play-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pending Survey</p>
                            <h5 class="mb-0">{{ $projects->where('status', 'survey')->count() }}</h5>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Value</p>
                            <h5 class="mb-0">{{ number_format($projects->sum('project_value')/1000000, 0) }}M</h5>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10">
                            <i class="bi bi-currency-dollar text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari kode, nama, atau klien..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="lead" {{ request('status') == 'lead' ? 'selected' : '' }}>Lead</option>
                        <option value="survey" {{ request('status') == 'survey' ? 'selected' : '' }}>Survey</option>
                        <option value="penawaran" {{ request('status') == 'penawaran' ? 'selected' : '' }}>Penawaran</option>
                        <option value="negosiasi" {{ request('status') == 'negosiasi' ? 'selected' : '' }}>Negosiasi</option>
                        <option value="deal" {{ request('status') == 'deal' ? 'selected' : '' }}>Deal</option>
                        <option value="eksekusi" {{ request('status') == 'eksekusi' ? 'selected' : '' }}>Eksekusi</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="kantor" {{ request('type') == 'kantor' ? 'selected' : '' }}>Kantor</option>
                        <option value="showroom" {{ request('type') == 'showroom' ? 'selected' : '' }}>Showroom</option>
                        <option value="kafe" {{ request('type') == 'kafe' ? 'selected' : '' }}>Kafe</option>
                        <option value="restoran" {{ request('type') == 'restoran' ? 'selected' : '' }}>Restoran</option>
                        <option value="outlet" {{ request('type') == 'outlet' ? 'selected' : '' }}>Outlet</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
                @if(request('search') || request('status') || request('type'))
                <div class="col-md-2">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="10%">Kode</th>
                            <th>Project</th>
                            <th>Klien</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Nilai</th>
                            <th>PIC</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $project->code }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $project->name }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ Str::limit($project->location, 30) }}
                                </small>
                            </td>
                            <td>{{ $project->client->name }}</td>
                            <td>
                                <span class="text-capitalize">{{ $project->type }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $project->status_badge }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" 
                                         role="progressbar" 
                                         style="width: {{ $project->progress_percentage }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $project->progress_percentage }}%</small>
                            </td>
                            <td>
                                @if($project->deal_value)
                                <div class="fw-semibold">Rp {{ number_format($project->deal_value/1000000, 0) }}M</div>
                                <small class="text-muted">Deal</small>
                                @elseif($project->project_value)
                                <div>Rp {{ number_format($project->project_value/1000000, 0) }}M</div>
                                <small class="text-muted">Estimasi</small>
                                @else
                                <span class="text-muted">TBD</span>
                                @endif
                            </td>
                            <td>{{ $project->pic->name }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('edit-projects')
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="btn btn-sm btn-outline-warning"
                                       data-bs-toggle="tooltip"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data project
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($projects->hasPages())
        <div class="card-footer">
            {{ $projects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
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

function exportProjects() {
    window.location.href = '{{ route("projects.index") }}?export=excel';
}
</script>
@endpush