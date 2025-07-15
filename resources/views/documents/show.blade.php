@extends('layouts.app')

@section('title', 'Detail Dokumen')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">{{ $document->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Dokumen</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            @if($document->type == 'pdf' || $document->type == 'image')
            <a href="{{ route('documents.preview', $document) }}" 
               class="btn btn-outline-info me-2"
               target="_blank">
                <i class="bi bi-eye me-2"></i>Preview
            </a>
            @endif
            
            <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Download
            </a>
            
            @if(Auth::user()->hasRole('admin') || $document->uploaded_by == Auth::id())
            <a href="{{ route('documents.edit', $document) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Document Preview/Info -->
        <div class="col-md-8">
            <!-- Preview Card -->
            <div class="card mb-4">
                <div class="card-body">
                    @if($document->type == 'image')
                    <!-- Image Preview -->
                    <div class="text-center">
                        <img src="{{ $document->url }}" 
                             alt="{{ $document->name }}" 
                             class="img-fluid rounded"
                             style="max-height: 600px;">
                    </div>
                    @elseif($document->type == 'pdf')
                    <!-- PDF Preview -->
                    <div class="pdf-preview">
                        <embed src="{{ $document->url }}" 
                               type="application/pdf" 
                               width="100%" 
                               height="600px">
                    </div>
                    @else
                    <!-- Other File Types -->
                    <div class="text-center py-5">
                        <i class="{{ $document->icon }} display-1 text-primary mb-3"></i>
                        <h5>{{ $document->name }}</h5>
                        <p class="text-muted">{{ $document->original_name }}</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('documents.download', $document) }}" 
                               class="btn btn-primary btn-lg">
                                <i class="bi bi-download me-2"></i>Download File
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Document Details -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Detail Dokumen</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Nama File:</dt>
                        <dd class="col-sm-9">{{ $document->original_name }}</dd>
                        
                        <dt class="col-sm-3">Kategori:</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-{{ $document->category_badge }}">
                                {{ ucfirst($document->category) }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-3">Tipe File:</dt>
                        <dd class="col-sm-9">
                            <i class="{{ $document->icon }} me-1"></i>
                            {{ strtoupper($document->extension) }}
                        </dd>
                        
                        <dt class="col-sm-3">Ukuran:</dt>
                        <dd class="col-sm-9">{{ $document->formatted_size }}</dd>
                        
                        <dt class="col-sm-3">Terkait dengan:</dt>
                        <dd class="col-sm-9">
                            @if($document->documentable_type == 'App\Models\Project')
                                <i class="bi bi-building me-1"></i>Project: 
                                <a href="{{ route('projects.show', $document->documentable_id) }}">
                                    {{ $document->documentable->name ?? 'N/A' }}
                                </a>
                            @else
                                <i class="bi bi-person me-1"></i>Client: 
                                <a href="{{ route('clients.show', $document->documentable_id) }}">
                                    {{ $document->documentable->name ?? 'N/A' }}
                                </a>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Diupload oleh:</dt>
                        <dd class="col-sm-9">{{ $document->uploadedBy->name }}</dd>
                        
                        <dt class="col-sm-3">Tanggal Upload:</dt>
                        <dd class="col-sm-9">{{ $document->created_at->format('d M Y H:i') }}</dd>
                        
                        <dt class="col-sm-3">Akses:</dt>
                        <dd class="col-sm-9">
                            @if($document->is_public)
                            <span class="badge bg-success">
                                <i class="bi bi-globe me-1"></i>Public
                            </span>
                            @else
                            <span class="badge bg-warning">
                                <i class="bi bi-lock me-1"></i>Private
                            </span>
                            @endif
                        </dd>
                        
                        @if($document->description)
                        <dt class="col-sm-3">Deskripsi:</dt>
                        <dd class="col-sm-9">{{ $document->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-md-4">
            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Statistik</h6>
                    <div class="row text-center g-3">
                        <div class="col-6 border-end">
                            <h4 class="mb-0">{{ $document->views }}</h4>
                            <small class="text-muted">Views</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0">{{ $document->downloads }}</h4>
                            <small class="text-muted">Downloads</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('documents.download', $document) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Download
                        </a>
                        
                        <button class="btn btn-outline-primary" onclick="shareDocument()">
                            <i class="bi bi-share me-2"></i>Share
                        </button>
                        
                        @if(Auth::user()->hasRole('admin') || $document->uploaded_by == Auth::id())
                        <hr>
                        
                        <a href="{{ route('documents.edit', $document) }}" 
                           class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        
                        <form action="{{ route('documents.destroy', $document) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Documents -->
            @if($relatedDocuments->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Dokumen Terkait</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($relatedDocuments as $related)
                        <a href="{{ route('documents.show', $related) }}" 
                           class="list-group-item list-group-item-action px-0">
                            <div class="d-flex align-items-center">
                                <i class="{{ $related->icon }} me-2 text-primary"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-truncate">{{ $related->name }}</h6>
                                    <small class="text-muted">
                                        {{ ucfirst($related->category) }} • {{ $related->formatted_size }}
                                    </small>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.pdf-preview {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
function shareDocument() {
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: '{{ $document->name }}',
            text: 'Check out this document',
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link Copied!',
                text: 'Document link telah disalin ke clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }
}
</script>
@endpush