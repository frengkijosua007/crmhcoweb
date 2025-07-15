@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Edit Project</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Project</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Informasi Project</h6>
                        <span class="badge bg-secondary">{{ $project->code }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label required">Nama Project</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $project->name) }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Jenis Project</label>
                                <select name="type" 
                                        class="form-select @error('type') is-invalid @enderror" 
                                        required>
                                    <option value="kantor" {{ old('type', $project->type) == 'kantor' ? 'selected' : '' }}>Kantor</option>
                                    <option value="showroom" {{ old('type', $project->type) == 'showroom' ? 'selected' : '' }}>Showroom</option>
                                    <option value="kafe" {{ old('type', $project->type) == 'kafe' ? 'selected' : '' }}>Kafe</option>
                                    <option value="restoran" {{ old('type', $project->type) == 'restoran' ? 'selected' : '' }}>Restoran</option>
                                    <option value="outlet" {{ old('type', $project->type) == 'outlet' ? 'selected' : '' }}>Outlet</option>
                                    <option value="other" {{ old('type', $project->type) == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Status</label>
                                <select name="status" 
                                        class="form-select @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="lead" {{ old('status', $project->status) == 'lead' ? 'selected' : '' }}>Lead</option>
                                    <option value="survey" {{ old('status', $project->status) == 'survey' ? 'selected' : '' }}>Survey</option>
                                    <option value="penawaran" {{ old('status', $project->status) == 'penawaran' ? 'selected' : '' }}>Penawaran</option>
                                    <option value="negosiasi" {{ old('status', $project->status) == 'negosiasi' ? 'selected' : '' }}>Negosiasi</option>
                                    <option value="deal" {{ old('status', $project->status) == 'deal' ? 'selected' : '' }}>Deal</option>
                                    <option value="eksekusi" {{ old('status', $project->status) == 'eksekusi' ? 'selected' : '' }}>Eksekusi</option>
                                    <option value="selesai" {{ old('status', $project->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="batal" {{ old('status', $project->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Klien</label>
                                <select name="client_id" 
                                        class="form-select @error('client_id') is-invalid @enderror" 
                                        required>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Person in Charge (PIC)</label>
                                <select name="pic_id" 
                                        class="form-select @error('pic_id') is-invalid @enderror" 
                                        required
                                        {{ Auth::user()->hasRole('marketing') && !Auth::user()->hasRole('admin') ? 'disabled' : '' }}>
                                    @foreach($pics as $pic)
                                    <option value="{{ $pic->id }}" 
                                            {{ old('pic_id', $project->pic_id) == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if(Auth::user()->hasRole('marketing') && !Auth::user()->hasRole('admin'))
                                <input type="hidden" name="pic_id" value="{{ $project->pic_id }}">
                                <small class="text-muted">PIC tidak dapat diubah oleh Marketing</small>
                                @endif
                                @error('pic_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label required">Lokasi Project</label>
                                <textarea name="location" 
                                          class="form-control @error('location') is-invalid @enderror" 
                                          rows="2"
                                          required>{{ old('location', $project->location) }}</textarea>
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estimasi Nilai Project</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           name="project_value" 
                                           class="form-control @error('project_value') is-invalid @enderror" 
                                           value="{{ old('project_value', $project->project_value) }}"
                                           min="0">
                                </div>
                                @error('project_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nilai Deal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           name="deal_value" 
                                           class="form-control @error('deal_value') is-invalid @enderror" 
                                           value="{{ old('deal_value', $project->deal_value) }}"
                                           min="0"
                                           {{ !in_array($project->status, ['deal', 'eksekusi', 'selesai']) ? 'readonly' : '' }}>
                                </div>
                                @error('deal_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nilai deal dapat diisi setelah status Deal</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" 
                                       name="start_date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Target Selesai</label>
                                <input type="date" 
                                       name="target_date" 
                                       class="form-control @error('target_date') is-invalid @enderror" 
                                       value="{{ old('target_date', $project->target_date?->format('Y-m-d')) }}">
                                @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Deskripsi Project</label>
                                <textarea name="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="4">{{ old('description', $project->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Project Info -->
                <div class="card mb-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-primary mb-3">Informasi Project</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Kode:</dt>
                            <dd class="col-sm-7">{{ $project->code }}</dd>
                            
                            <dt class="col-sm-5">Dibuat:</dt>
                            <dd class="col-sm-7">{{ $project->created_at->format('d M Y') }}</dd>
                            
                            <dt class="col-sm-5">Update:</dt>
                            <dd class="col-sm-7">{{ $project->updated_at->format('d M Y H:i') }}</dd>
                            
                            <dt class="col-sm-5">Surveys:</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-info">{{ $project->surveys_count ?? 0 }}</span>
                            </dd>
                            
                            <dt class="col-sm-5">Progress:</dt>
                            <dd class="col-sm-7">
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar" style="width: {{ $project->progress_percentage }}%"></div>
                                </div>
                                <small>{{ $project->progress_percentage }}%</small>
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Status History -->
                @if($project->status != 'lead')
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">History Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline-simple">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <strong>{{ ucfirst($project->status) }}</strong>
                                    <br><small>Sekarang</small>
                                </div>
                            </div>
                            @if($project->status != 'lead')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <strong>Lead</strong>
                                    <br><small>{{ $project->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Project
                            </button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
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

.timeline-simple {
    position: relative;
    padding-left: 30px;
}

.timeline-simple::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -21px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Enable/disable deal value based on status
    $('select[name="status"]').on('change', function() {
        const status = $(this).val();
        const dealValueInput = $('input[name="deal_value"]');
        
        if (['deal', 'eksekusi', 'selesai'].includes(status)) {
            dealValueInput.prop('readonly', false);
        } else {
            dealValueInput.prop('readonly', true);
        }
    });

    // Date validation
    $('input[name="start_date"]').on('change', function() {
        const startDate = $(this).val();
        $('input[name="target_date"]').attr('min', startDate);
    });
});
</script>
@endpush