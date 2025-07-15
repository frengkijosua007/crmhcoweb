@extends('layouts.app')

@section('title', 'Data Klien')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Data Klien</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Klien</li>
                </ol>
            </nav>
        </div>
        <div>
            @can('create-clients')
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Klien
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari nama, email, atau telepon..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="prospek" {{ request('status') == 'prospek' ? 'selected' : '' }}>Prospek</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
                @if(request('search') || request('status'))
                <div class="col-md-2">
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Nama Klien</th>
                            <th>Kontak</th>
                            <th>PIC</th>
                            <th>Status</th>
                            <th>Projects</th>
                            <th>Sumber</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $loop->iteration + ($clients->currentPage() - 1) * $clients->perPage() }}</td>
                            <td>
                                <div class="fw-semibold">{{ $client->name }}</div>
                                <small class="text-muted">{{ $client->email }}</small>
                            </td>
                            <td>
                                <div>{{ $client->phone }}</div>
                                @if($client->whatsapp)
                                <small class="text-success">
                                    <i class="bi bi-whatsapp"></i> {{ $client->whatsapp }}
                                </small>
                                @endif
                            </td>
                            <td>{{ $client->pic->name }}</td>
                            <td>
                                <span class="badge bg-{{ $client->status_badge }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $client->projects_count ?? 0 }}</span>
                            </td>
                            <td>{{ ucfirst($client->source) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('clients.show', $client) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('edit-clients')
                                    <a href="{{ route('clients.edit', $client) }}" 
                                       class="btn btn-sm btn-outline-warning"
                                       data-bs-toggle="tooltip"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('delete-clients')
                                    <form action="{{ route('clients.destroy', $client) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus client ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data klien
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($clients->hasPages())
        <div class="card-footer">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush