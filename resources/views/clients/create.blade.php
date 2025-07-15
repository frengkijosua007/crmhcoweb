@extends('layouts.app')

@section('title', 'Tambah Klien')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Tambah Klien Baru</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Klien</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Dasar</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label required">Nama Klien</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}"
                                       placeholder="PT. Contoh Nama Perusahaan"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}"
                                       placeholder="email@perusahaan.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Telepon</label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}"
                                       placeholder="021-1234567"
                                       required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-whatsapp"></i>
                                    </span>
                                    <input type="text" 
                                           name="whatsapp" 
                                           class="form-control @error('whatsapp') is-invalid @enderror" 
                                           value="{{ old('whatsapp') }}"
                                           placeholder="08123456789">
                                </div>
                                @error('whatsapp')
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
                                    <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('pic_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label required">Alamat</label>
                                <textarea name="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Alamat lengkap..."
                                          required>{{ old('address') }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Source Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Sumber Lead</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Sumber</label>
                            <select name="source" 
                                    class="form-select @error('source') is-invalid @enderror" 
                                    required>
                                <option value="">-- Pilih Sumber --</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="walk-in" {{ old('source') == 'walk-in' ? 'selected' : '' }}>Walk In</option>
                                <option value="social-media" {{ old('source') == 'social-media' ? 'selected' : '' }}>Social Media</option>
                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Detail Sumber</label>
                            <input type="text" 
                                   name="source_detail" 
                                   class="form-control @error('source_detail') is-invalid @enderror" 
                                   value="{{ old('source_detail') }}"
                                   placeholder="Misal: Nama yang mereferensikan">
                            @error('source_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Catatan</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  rows="4"
                                  placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Klien
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
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
// Auto-fill WhatsApp from phone number
document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
    const phone = e.target.value;
    const whatsappInput = document.querySelector('input[name="whatsapp"]');
    
    // If WhatsApp is empty and phone starts with 0, auto-fill
    if (!whatsappInput.value && phone.startsWith('0')) {
        whatsappInput.value = phone;
    }
});

// Initialize Select2 for PIC selection
$(document).ready(function() {
    $('select[name="pic_id"]').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih PIC --'
    });
});
</script>
@endpush