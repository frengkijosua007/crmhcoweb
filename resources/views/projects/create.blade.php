@extends('layouts.app')

@section('title', 'Tambah Project')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Tambah Project Baru</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Project</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif


    <form action="{{ route('projects.store') }}" method="POST" id="projectForm">
        @csrf
        <input type="hidden" name="client_id" value="{{ $selectedClient->id ?? '' }}">
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Project</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label required">Nama Project</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}"
                                       placeholder="Contoh: Renovasi Kantor Lantai 2"
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
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="kantor" {{ old('type') == 'kantor' ? 'selected' : '' }}>Kantor</option>
                                    <option value="showroom" {{ old('type') == 'showroom' ? 'selected' : '' }}>Showroom</option>
                                    <option value="kafe" {{ old('type') == 'kafe' ? 'selected' : '' }}>Kafe</option>
                                    <option value="restoran" {{ old('type') == 'restoran' ? 'selected' : '' }}>Restoran</option>
                                    <option value="outlet" {{ old('type') == 'outlet' ? 'selected' : '' }}>Outlet</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Klien</label>
                                <select name="client_id" 
                                        class="form-select @error('client_id') is-invalid @enderror" 
                                        id="clientSelect"
                                        required>
                                    <option value="">-- Pilih Klien --</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            {{ old('client_id', $selectedClient?->id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Belum ada klien? <a href="{{ route('clients.create') }}" target="_blank">Tambah klien baru</a>
                                </small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label required">Lokasi Project</label>
                                <textarea name="location" 
                                          class="form-control @error('location') is-invalid @enderror" 
                                          rows="2"
                                          placeholder="Alamat lengkap lokasi project"
                                          required>{{ old('location') }}</textarea>
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Person in Charge (PIC)</label>
                                <select name="pic_id" 
                                        class="form-select @error('pic_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih PIC --</option>
                                    @foreach($pics as $pic)
                                    <option value="{{ $pic->id }}" 
                                            {{ old('pic_id', Auth::id()) == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('pic_id')
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
                                           value="{{ old('project_value') }}"
                                           placeholder="0"
                                           min="0">
                                </div>
                                @error('project_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai (Rencana)</label>
                                <input type="date" 
                                       name="start_date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') }}">
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Target Selesai</label>
                                <input type="date" 
                                       name="target_date" 
                                       class="form-control @error('target_date') is-invalid @enderror" 
                                       value="{{ old('target_date') }}">
                                @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Deskripsi Project</label>
                                <textarea name="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="4"
                                          placeholder="Deskripsi detail tentang project...">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Quick Info -->
                <div class="card mb-4 border-info">
                    <div class="card-body">
                        <h6 class="text-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>Informasi
                        </h6>
                        <ul class="small mb-0">
                            <li>Kode project akan di-generate otomatis</li>
                            <li>Status awal project adalah "Lead"</li>
                            <li>Anda dapat menambahkan survey setelah project dibuat</li>
                            <li>Pastikan data klien sudah lengkap</li>
                        </ul>
                    </div>
                </div>

                <!-- Selected Client Info -->
                <div class="card mb-4" id="clientInfo" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">Info Klien</h6>
                    </div>
                    <div class="card-body" id="clientDetails">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Project
                            </button>
                            <button type="submit" name="save_and_survey" value="1" class="btn btn-success">
                                <i class="bi bi-clipboard-check me-2"></i>Simpan & Buat Survey
                            </button>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
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
    $('#clientSelect').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Klien --',
        allowClear: true
    });

    // Load client info when selected
    $('#clientSelect').on('change', function() {
        const clientId = $(this).val();
        if (clientId) {
            // Show loading
            $('#clientInfo').show();
            $('#clientDetails').html('<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</div>');
            
            // Simulate loading client info (in real app, make AJAX call)
            setTimeout(() => {
                $('#clientDetails').html(`
                    <dl class="row mb-0 small">
                        <dt class="col-5">Kontak:</dt>
                        <dd class="col-7">021-123456</dd>
                        <dt class="col-5">Email:</dt>
                        <dd class="col-7">client@email.com</dd>
                        <dt class="col-5">PIC:</dt>
                        <dd class="col-7">Marketing Name</dd>
                    </dl>
                `);
            }, 500);
        } else {
            $('#clientInfo').hide();
        }
    });

    // Date validation
    $('input[name="start_date"]').on('change', function() {
        const startDate = $(this).val();
        $('input[name="target_date"]').attr('min', startDate);
    });

    // Format currency input
    $('input[name="project_value"]').on('input', function() {
        let value = $(this).val();
        value = value.replace(/\D/g, '');
        $(this).val(value);
    });
});
</script>
@endpush