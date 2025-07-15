@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Edit User</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Edit</li>
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
                    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                <label for="role" class="form-label required">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror"
                                        id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
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

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                                <div class="form-text">If unchecked, user won't be able to login.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-warning">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $user->created_at->format('d M Y, h:i A') }}</p>
                    <p><strong>Last Updated:</strong> {{ $user->updated_at->format('d M Y, h:i A') }}</p>
                    <p><strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}</p>
                </div>
            </div>

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
