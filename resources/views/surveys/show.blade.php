@extends('layouts.app')

@section('title', 'Edit Survey')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Edit Survey</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('surveys.index') }}">Survey</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('surveys.update', $survey) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- Survey Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Survey</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            Hanya survey dengan status <strong>Pending</strong> yang dapat diedit.
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Project</label>
                            <div class="form-control-plaintext">
                                {{ $survey->project->code }} - {{ $survey->project->name }}
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Surveyor</label>
                            <div class="form-control-plaintext">
                                {{ $survey->surveyor->name ?? '-' }}
                            </div>
                        </div>


                            <div class="col-md-6">
                                <label class="form-label required">Tanggal & Waktu Survey</label>
                                <input type="datetime-local" 
                                       name="scheduled_date" 
                                       class="form-control @error('scheduled_date') is-invalid @enderror" 
                                       value="{{ old('scheduled_date', $survey->scheduled_date->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('scheduled_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Catatan untuk Surveyor</label>
                                <textarea name="notes" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          rows="4">{{ old('notes', $survey->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Survey Status -->
                <div class="card mb-4 border-info">
                    <div class="card-body">
                        <h6 class="text-info mb-3">Status Survey</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Status:</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-{{ $survey->status_badge }}">
                                    {{ ucfirst($survey->status) }}
                                </span>
                            </dd>
                            
                            <dt class="col-sm-5">Dibuat:</dt>
                            <dd class="col-sm-7">{{ $survey->created_at->format('d M Y') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Survey
                            </button>
                            <a href="{{ route('surveys.show', $survey) }}" class="btn btn-outline-secondary">
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