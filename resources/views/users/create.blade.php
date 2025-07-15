@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Add New User</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label required">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label required">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label required">Confirm Password</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label required">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror"
                                        id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Image</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            <div class="form-text">Max size: 2MB. Allowed formats: JPG, PNG, GIF.</div>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                                <div class="form-text">If unchecked, user won't be able to login.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                        </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Help</h5>
                </div>
                <div class="card-body">
                    <h6>Role Permissions</h6>
                    <p>Each role has different permissions:</p>
                    <ul>
                        <li><strong>Admin</strong>: Full access to all features</li>
                        <li><strong>Manager</strong>: Manage projects, clients, and reports</li>
                        <li><strong>Marketing</strong>: Manage clients and projects</li>
                        <li><strong>Surveyor</strong>: Conduct surveys and upload reports</li>
                        <li><strong>User</strong>: Basic access</li>
                    </ul>

                    <h6>User Status</h6>
                    <p>Inactive users cannot log into the system.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('avatar').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.createElement('div');
            preview.className = 'mt-2';
            preview.innerHTML = `
                <div class="avatar-preview">
                    <img src="${URL.createObjectURL(file)}" class="img-thumbnail" style="height: 150px;">
                </div>
            `;

            // Remove any existing preview
            const existingPreview = document.querySelector('.avatar-preview');
            if (existingPreview) {
                existingPreview.remove();
            }

            this.parentNode.appendChild(preview);
        }
    }
</script>
@endpush
