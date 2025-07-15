@extends('layouts.app')

@section('title', 'Edit Dokumen')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Edit Dokumen</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Dokumen</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('documents.update', $document) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- Document Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Dokumen</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Anda hanya dapat mengubah informasi dokumen, tidak dapat mengganti file.
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label required">Nama Dokumen</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $document->name) }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Kategori</label>
                                <select name="category" 
                                        class="form-select @error('category') is-invalid @enderror" 
                                        required>
                                    <option value="penawaran" {{ old('category', $document->category) == 'penawaran' ? 'selected' : '' }}>
                                        Penawaran
                                    </option>
                                    <option value="kontrak" {{ old('category', $document->category) == 'kontrak' ? 'selected' : '' }}>
                                        Kontrak
                                    </option>
                                    <option value="invoice" {{ old('category', $document->category) == 'invoice' ? 'selected' : '' }}>
                                        Invoice
                                    </option>
                                    <option value="survey" {{ old('category', $document->category) == 'survey' ? 'selected' : '' }}>
                                        Survey
                                    </option>
                                    <option value="design" {{ old('category', $document->category) == 'design' ? 'selected' : '' }}>
                                        Design
                                    </option>
                                    <option value="progress" {{ old('category', $document->category) == 'progress' ? 'selected' : '' }}>
                                        Progress
                                    </option>
                                    <option value="other" {{ old('category', $document->category) == 'other' ? 'selected' : '' }}>
                                        Lainnya
                                    </option>
                                </select>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Related To</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $document->documentable_type == 'App\Models\Project' ? 'Project: ' : 'Client: ' }}{{ $document->documentable->name ?? 'N/A' }}"
                                       disabled>
                                <small class="text-muted">Tidak dapat diubah</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3">{{ old('description', $document->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_public" 
                                           name="is_public" 
                                           value="1"
                                           {{ old('is_public', $document->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Dokumen dapat diakses oleh semua user
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- File Info -->
                <div class="card mb-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-primary mb-3">Info File</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">File:</dt>
                            <dd class="col-sm-7 text-truncate" title="{{ $document->original_name }}">
                                {{ $document->original_name }}
                            </dd>
                            
                            <dt class="col-sm-5">Tipe:</dt>
                            <dd class="col-sm-7">
                                <i class="{{ $document->icon }} me-1"></i>
                                {{ strtoupper($document->extension) }}
                            </dd>
                            
                            <dt class="col-sm-5">Ukuran:</dt>
                            <dd class="col-sm-7">{{ $document->formatted_size }}</dd>
                            
                            <dt class="col-sm-5">Diupload:</dt>
                            <dd class="col-sm-7">{{ $document->created_at->format('d M Y') }}</dd>
                            
                            <dt class="col-sm-5">Views:</dt>
                            <dd class="col-sm-7">{{ $document->views }}</dd>
                            
                            <dt class="col-sm-5">Downloads:</dt>
                            <dd class="col-sm-7">{{ $document->downloads }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Dokumen
                            </button>
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-label.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endpush