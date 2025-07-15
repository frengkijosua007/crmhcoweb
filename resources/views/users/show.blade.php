@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">User Details</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
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

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>

                        @if(Auth::id() != $user->id)
                        <form action="{{ route('users.toggle-active', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'danger' : 'success' }}">
                                <i class="bi bi-{{ $user->is_active ? 'slash-circle' : 'check-circle' }} me-1"></i>
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
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

                    <div>
                        <label class="form-label text-muted">Address</label>
                        <div>{{ $user->address ?? 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Role</label>
                            <div>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Created At</label>
                            <div>{{ $user->created_at->format('d M Y, h:i A') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Last Updated</label>
                            <div>{{ $user->updated_at->format('d M Y, h:i A') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Login</label>
                            <div>
                                {{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log could be added here -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activity</h5>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Logged in</h6>
                                    <small class="text-muted">From IP 192.168.1.1</small>
                                </div>
                                <small class="text-muted">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </small>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Profile updated</h6>
                                    <small class="text-muted">Changed profile information</small>
                                </div>
                                <small class="text-muted">
                                    {{ $user->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Account created</h6>
                                    <small class="text-muted">New user registration</small>
                                </div>
                                <small class="text-muted">
                                    {{ $user->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
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
