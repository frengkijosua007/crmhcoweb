@extends('layouts.app')

@section('title', 'Dokumen')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Manajemen Dokumen</h4>
            <p class="text-muted mb-0">Upload dan kelola dokumen project</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" id="bulkDownloadBtn" style="display: none;">
                <i class="bi bi-download me-2"></i>Download Selected
            </button>
            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                <i class="bi bi-upload me-2"></i>Upload Dokumen
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $stats['total_documents'] }}</h5>
                            <small class="text-muted">Total Dokumen</small>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="bi bi-file-earmark text-primary"></i>
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
                            <h5 class="mb-1">{{ number_format($stats['total_size'] / 1048576, 1) }} MB</h5>
                            <small class="text-muted">Total Size</small>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="bi bi-hdd text-success"></i>
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
                            <h5 class="mb-1">{{ $stats['this_month'] }}</h5>
                            <small class="text-muted">Bulan Ini</small>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="bi bi-calendar-month text-warning"></i>
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
                            <h5 class="mb-1">{{ $stats['by_category']['penawaran'] ?? 0 }}</h5>
                            <small class="text-muted">Penawaran</small>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10">
                            <i class="bi bi-file-text text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('documents.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari nama dokumen..."
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="penawaran" {{ request('category') == 'penawaran' ? 'selected' : '' }}>Penawaran</option>
                        <option value="kontrak" {{ request('category') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="invoice" {{ request('category') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                        <option value="survey" {{ request('category') == 'survey' ? 'selected' : '' }}>Survey</option>
                        <option value="design" {{ request('category') == 'design' ? 'selected' : '' }}>Design</option>
                        <option value="progress" {{ request('category') == 'progress' ? 'selected' : '' }}>Progress</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Image</option>
                        <option value="word" {{ request('type') == 'word' ? 'selected' : '' }}>Word</option>
                        <option value="excel" {{ request('type') == 'excel' ? 'selected' : '' }}>Excel</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" 
                           placeholder="Dari tanggal" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" 
                           placeholder="Sampai tanggal" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents Grid/List Toggle -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary active" id="gridViewBtn">
                <i class="bi bi-grid-3x3-gap"></i> Grid
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="listViewBtn">
                <i class="bi bi-list"></i> List
            </button>
        </div>
    </div>

    <!-- Documents Grid View -->
    <div id="gridView">
        <div class="row g-3">
            @forelse($documents as $document)
            <div class="col-md-3 col-sm-6">
                <div class="card document-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="document-icon">
                                <i class="{{ $document->icon }} fs-1 text-primary"></i>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input document-checkbox" 
                                       type="checkbox" 
                                       value="{{ $document->id }}">
                            </div>
                        </div>
                        
                        <h6 class="card-title text-truncate" title="{{ $document->name }}">
                            {{ $document->name }}
                        </h6>
                        
                        <p class="text-muted small mb-2">
                            {{ Str::limit($document->description, 50) }}
                        </p>
                        
                        <div class="mb-2">
                            <span class="badge bg-{{ $document->category_badge }}">
                                {{ ucfirst($document->category) }}
                            </span>
                            <span class="badge bg-secondary">
                                {{ strtoupper($document->extension) }}
                            </span>
                        </div>
                        
                        <div class="small text-muted mb-3">
                            <div class="d-flex justify-content-between">
                                <span><i class="bi bi-hdd me-1"></i>{{ $document->formatted_size }}</span>
                                <span><i class="bi bi-eye me-1"></i>{{ $document->views }}</span>
                                <span><i class="bi bi-download me-1"></i>{{ $document->downloads }}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $document->created_at->diffForHumans() }}
                            </small>
                            <div class="btn-group" role="group">
                                <a href="{{ route('documents.show', $document) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('documents.download', $document) }}" 
                                   class="btn btn-sm btn-outline-success"
                                   data-bs-toggle="tooltip" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-x fs-1 text-muted d-block mb-3"></i>
                    <h5>Belum Ada Dokumen</h5>
                    <p class="text-muted">Mulai upload dokumen untuk project Anda</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload Dokumen
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Documents List View (Hidden by default) -->
    <div id="listView" style="display: none;">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Nama Dokumen</th>
                                <th>Kategori</th>
                                <th>Related To</th>
                                <th>Size</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input document-checkbox" 
                                           value="{{ $document->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $document->icon }} me-2 text-primary"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $document->name }}</div>
                                            <small class="text-muted">{{ $document->original_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $document->category_badge }}">
                                        {{ ucfirst($document->category) }}
                                    </span>
                                </td>
                                <td>
                                    @if($document->documentable_type == 'App\Models\Project')
                                        <a href="{{ route('projects.show', $document->documentable_id) }}">
                                            {{ $document->documentable->name ?? 'N/A' }}
                                        </a>
                                    @else
                                        <a href="{{ route('clients.show', $document->documentable_id) }}">
                                            {{ $document->documentable->name ?? 'N/A' }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $document->formatted_size }}</td>
                                <td>{{ $document->uploadedBy->name }}</td>
                                <td>{{ $document->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('documents.show', $document) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('documents.preview', $document) }}" 
                                           class="btn btn-sm btn-outline-info"
                                           data-bs-toggle="tooltip" title="Preview"
                                           target="_blank">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                        <a href="{{ route('documents.download', $document) }}" 
                                           class="btn btn-sm btn-outline-success"
                                           data-bs-toggle="tooltip" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @if(Auth::user()->hasRole('admin') || $document->uploaded_by == Auth::id())
                                        <form action="{{ route('documents.destroy', $document) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($documents->hasPages())
    <div class="mt-4">
        {{ $documents->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.document-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.document-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.document-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 12px;
}

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
// View toggle
document.getElementById('gridViewBtn').addEventListener('click', function() {
    document.getElementById('gridView').style.display = 'block';
    document.getElementById('listView').style.display = 'none';
    this.classList.add('active');
    document.getElementById('listViewBtn').classList.remove('active');
});

document.getElementById('listViewBtn').addEventListener('click', function() {
    document.getElementById('gridView').style.display = 'none';
    document.getElementById('listView').style.display = 'block';
    this.classList.add('active');
    document.getElementById('gridViewBtn').classList.remove('active');
});

// Checkbox handling
const checkboxes = document.querySelectorAll('.document-checkbox');
const bulkDownloadBtn = document.getElementById('bulkDownloadBtn');
const selectAllCheckbox = document.getElementById('selectAll');

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.document-checkbox:checked');
    if (checkedBoxes.length > 0) {
        bulkDownloadBtn.style.display = 'inline-block';
    } else {
        bulkDownloadBtn.style.display = 'none';
    }
}

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });
}

// Bulk download
bulkDownloadBtn.addEventListener('click', function() {
    const selectedIds = Array.from(document.querySelectorAll('.document-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih dokumen yang akan didownload');
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("documents.bulk-download") }}';
    form.innerHTML = `
        @csrf
        ${selectedIds.map(id => `<input type="hidden" name="document_ids[]" value="${id}">`).join('')}
    `;
    document.body.appendChild(form);
    form.submit();
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush