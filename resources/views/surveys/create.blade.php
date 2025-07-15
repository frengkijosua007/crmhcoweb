@extends('layouts.app')

@section('title', 'Jadwalkan Survey')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Jadwalkan Survey</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('surveys.index') }}">Survey</a></li>
                <li class="breadcrumb-item active">Jadwalkan</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('surveys.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-8">
                <!-- Survey Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Survey</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label required">Project</label>
                                <select name="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror"
                                        id="projectSelect"
                                        required>
                                    <option value="">-- Pilih Project --</option>
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                            {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}
                                            data-client="{{ $project->client->name }}"
                                            data-location="{{ $project->location }}"
                                            data-type="{{ ucfirst($project->type) }}">
                                        {{ $project->code }} - {{ $project->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Surveyor</label>
                                <select name="surveyor_id"
                                        class="form-select @error('surveyor_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- Pilih Surveyor --</option>
                                    @foreach($surveyors as $surveyor)
                                    <option value="{{ $surveyor->id }}" {{ old('surveyor_id') == $surveyor->id ? 'selected' : '' }}>
                                        {{ $surveyor->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('surveyor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Tanggal & Waktu Survey</label>
                                <input type="datetime-local"
                                       name="scheduled_date"
                                       class="form-control @error('scheduled_date') is-invalid @enderror"
                                       value="{{ old('scheduled_date') }}"
                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                       required>
                                @error('scheduled_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Catatan untuk Surveyor</label>
                                <textarea name="notes"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          rows="4"
                                          placeholder="Informasi tambahan untuk surveyor...">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Details Card -->
                <div class="card" id="projectDetails" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">Detail Project</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Klien:</dt>
                            <dd class="col-sm-9" id="clientName">-</dd>

                            <dt class="col-sm-3">Jenis Project:</dt>
                            <dd class="col-sm-9" id="projectType">-</dd>

                            <dt class="col-sm-3">Lokasi:</dt>
                            <dd class="col-sm-9" id="projectLocation">-</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Survey Checklist Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Checklist Survey
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Surveyor akan mengisi checklist berikut:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-square text-primary me-2"></i>
                                Ketersediaan Listrik
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-square text-primary me-2"></i>
                                Ketersediaan Air
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-square text-primary me-2"></i>
                                Akses Jalan
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-square text-primary me-2"></i>
                                Status Izin
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-square text-primary me-2"></i>
                                Kondisi Bangunan Existing
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-square text-primary me-2"></i>
                                Luas Area (m²)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-camera text-primary me-2"></i>
                                Foto Lokasi
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                GPS Koordinat
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-calendar-check me-2"></i>Jadwalkan Survey
                            </button>
                            <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#projectSelect').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Project --'
    });

    // Show project details when selected
    $('#projectSelect').on('change', function() {
        const selected = $(this).find(':selected');

        if ($(this).val()) {
            $('#clientName').text(selected.data('client'));
            $('#projectType').text(selected.data('type'));
            $('#projectLocation').text(selected.data('location'));
            $('#projectDetails').slideDown();
        } else {
            $('#projectDetails').slideUp();
        }
    });

    // Trigger change if pre-selected
    if ($('#projectSelect').val()) {
        $('#projectSelect').trigger('change');
    }
});
</script>
@endpush
