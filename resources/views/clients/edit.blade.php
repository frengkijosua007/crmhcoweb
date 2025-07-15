@extends('layouts.app')

@section('title', 'Edit Klien')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Edit Klien</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Klien</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                       value="{{ old('name', $client->name) }}"
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
                                       value="{{ old('email', $client->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Telepon</label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $client->phone) }}"
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
                                           value="{{ old('whatsapp', $client->whatsapp) }}">
                                </div>
                                @error('whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Status</label>
                                <select name="status" 
                                        class="form-select @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="prospek" {{ old('status', $client->status) == 'prospek' ? 'selected' : '' }}>Prospek</option>
                                    <option value="aktif" {{ old('status', $client->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ old('status', $client->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                @error('status')
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
                                    <option value="{{ $pic->id }}" {{ old('pic_id', $client->pic_id) == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if(Auth::user()->hasRole('marketing') && !Auth::user()->hasRole('admin'))
                                <input type="hidden" name="pic_id" value="{{ $client->pic_id }}">
                                <small class="text-muted">PIC tidak dapat diubah oleh Marketing</small>
                                @endif
                                @error('pic_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label required">Alamat</label>
                                <textarea name="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          rows="3"
                                          required>{{ old('address', $client->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Client Info -->
                <div class="card mb-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-primary mb-3">Informasi Klien</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Dibuat:</dt>
                            <dd class="col-sm-7">{{ $client->created_at->format('d M Y') }}</dd>
                            
                            <dt class="col-sm-5">Terakhir Update:</dt>
                            <dd class="col-sm-7">{{ $client->updated_at->format('d M Y H:i') }}</dd>
                            
                            <dt class="col-sm-5">Total Project:</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-primary">{{ $client->projects_count ?? 0 }}</span>
                            </dd>
                        </dl>
                    </div>
                </div>

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
                                <option value="referral" {{ old('source', $client->source) == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="website" {{ old('source', $client->source) == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="walk-in" {{ old('source', $client->source) == 'walk-in' ? 'selected' : '' }}>Walk In</option>
                                <option value="social-media" {{ old('source', $client->source) == 'social-media' ? 'selected' : '' }}>Social Media</option>
                                <option value="other" {{ old('source', $client->source) == 'other' ? 'selected' : '' }}>Lainnya</option>
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
                                   value="{{ old('source_detail', $client->source_detail) }}">
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
                                  rows="4">{{ old('notes', $client->notes) }}</textarea>
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
                                <i class="bi bi-save me-2"></i>Update Klien
                            </button>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary">
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