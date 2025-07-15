@extends('layouts.app')

@section('title', 'Detail Klien')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Detail Klien</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Klien</a></li>
                    <li class="breadcrumb-item active">{{ $client->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            @can('edit-clients')
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            @endcan
            <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Project
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Client Information Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </div>
                        <h5 class="mb-1">{{ $client->name }}</h5>
                        <span class="badge bg-{{ $client->status_badge }}">
                            {{ ucfirst($client->status) }}
                        </span>
                    </div>

                    <dl class="row">
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">
                            @if($client->email)
                            <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Telepon:</dt>
                        <dd class="col-sm-8">
                            <a href="tel:{{ $client->phone }}">{{ $client->phone }}</a>
                        </dd>

                        <dt class="col-sm-4">WhatsApp:</dt>
                        <dd class="col-sm-8">
                            @if($client->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->whatsapp) }}" 
                               target="_blank" 
                               class="text-success">
                                <i class="bi bi-whatsapp"></i> {{ $client->whatsapp }}
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">PIC:</dt>
                        <dd class="col-sm-8">{{ $client->pic->name }}</dd>

                        <dt class="col-sm-4">Sumber:</dt>
                        <dd class="col-sm-8">
                            {{ ucfirst($client->source) }}
                            @if($client->source_detail)
                            <br><small class="text-muted">{{ $client->source_detail }}</small>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Alamat:</dt>
                        <dd class="col-sm-8">{{ $client->address }}</dd>

                        <dt class="col-sm-4">Bergabung:</dt>
                        <dd class="col-sm-8">{{ $client->created_at->format('d M Y') }}</dd>
                    </dl>

                    @if($client->notes)
                    <hr>
                    <h6>Catatan:</h6>
                    <p class="text-muted mb-0">{{ $client->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Statistik</h6>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h4 class="mb-0">{{ $client->projects->count() }}</h4>
                            <small class="text-muted">Total Project</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0">{{ $client->projects->where('status', 'eksekusi')->count() }}</h4>
                            <small class="text-muted">Project Aktif</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-12">
                            <h5 class="mb-0">Rp {{ number_format($client->projects->sum('deal_value'), 0, ',', '.') }}</h5>
                            <small class="text-muted">Total Nilai Project</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Projects Tab -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#projects">
                                <i class="bi bi-building me-2"></i>Projects ({{ $client->projects->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#timeline">
                                <i class="bi bi-clock-history me-2"></i>Timeline
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Projects Tab -->
                        <div class="tab-pane fade show active" id="projects">
                            @if($client->projects->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Project</th>
                                            <th>Jenis</th>
                                            <th>Status</th>
                                            <th>Nilai</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($client->projects as $project)
                                        <tr>
                                            <td>{{ $project->code }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $project->name }}</div>
                                                <small class="text-muted">{{ $project->location }}</small>
                                            </td>
                                            <td>{{ ucfirst($project->type) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $project->status_badge }}">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($project->deal_value)
                                                Rp {{ number_format($project->deal_value, 0, ',', '.') }}
                                                @else
                                                <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}" 
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
                                <i class="bi bi-building fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted">Belum ada project untuk klien ini</p>
                                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Project
                                </a>
                            </div>
                            @endif
                        </div>

                        <!-- Timeline Tab -->
                        <div class="tab-pane fade" id="timeline">
                            <div class="activity-timeline">
                                @forelse($client->projects->sortByDesc('created_at')->take(10) as $project)
                                <div class="activity-item">
                                    <div class="activity-icon bg-primary">
                                        <i class="bi bi-building text-white"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="fw-semibold">Project {{ $project->name }} dibuat</div>
                                        <p class="text-muted mb-1">Status: {{ ucfirst($project->status) }}</p>
                                        <small class="text-muted">{{ $project->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted text-center py-4">Belum ada aktivitas</p>
                                @endforelse

                                <div class="activity-item">
                                    <div class="activity-icon bg-success">
                                        <i class="bi bi-person-plus text-white"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="fw-semibold">Klien terdaftar</div>
                                        <p class="text-muted mb-1">PIC: {{ $client->pic->name }}</p>
                                        <small class="text-muted">{{ $client->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 80px;
    height: 80px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 600;
}

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