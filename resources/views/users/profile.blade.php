@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">My Profile</h4>
        <p class="text-muted mb-0">View and update your profile information</p>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <div class="avatar-lg mx-auto mb-4">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle">
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <div>{{ $user->email }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Phone</label>
                        <div>{{ $user->phone ?? 'Not provided' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Address</label>
                        <div>{{ $user->address ?? 'Not provided' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Member Since</label>
                        <div>{{ $user->created_at->format('d M Y') }}</div>
                    </div>

                    <div>
                        <label class="form-label text-muted">Last Login</label>
                        <div>{{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Edit Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update-profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label required">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Image</label>

                            @if($user->avatar)
                                <div class="mb-2">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                         class="img-thumbnail" style="height: 150px;">
                                </div>
                            @endif

                            <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            <div class="form-text">Max size: 2MB. Allowed formats: JPG, PNG, GIF.</div>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update-password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label required">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label required">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label required">Confirm New Password</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-lg {
    width: 120px;
    height: 120px;
    overflow: hidden;
}

.avatar-lg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>
@endpush

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('avatar').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.createElement('img');
            preview.src = URL.createObjectURL(file);
            preview.className = 'img-thumbnail mb-2';
            preview.style.height = '150px';

            // Remove any existing preview
            const existingPreview = this.previousElementSibling;
            if (existingPreview && existingPreview.tagName === 'DIV') {
                existingPreview.innerHTML = '';
                existingPreview.appendChild(preview);
            } else {
                const container = document.createElement('div');
                container.className = 'mb-2';
                container.appendChild(preview);
                this.parentNode.insertBefore(container, this);
            }
        }
    }
</script>
@endpush
