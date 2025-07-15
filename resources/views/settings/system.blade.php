@extends('layouts.app')

@section('title', 'System Information')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">System Information</h4>
            <p class="text-muted mb-0">View system configuration and technical details</p>
        </div>
        <div>
            <a href="{{ route('settings.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Back to Settings
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Navigation -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="settings-nav list-group list-group-flush">
                        <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-gear me-2"></i> General Settings
                        </a>
                        <a href="{{ route('settings.system') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-info-circle me-2"></i> System Information
                        </a>
                        <a href="{{ route('settings.backup') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Backup & Restore
                        </a>
                        <a href="{{ route('settings.logs') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-journal-text me-2"></i> System Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - System Information -->
        <div class="col-md-9">
            <!-- System Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">System Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                @foreach($systemInfo as $key => $value)
                                <tr>
                                    <th width="30%">{{ $key }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PHP Extensions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">PHP Extensions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($extensionStatus as $extension => $installed)
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center">
                                @if($installed)
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                @else
                                <i class="bi bi-x-circle-fill text-danger me-2"></i>
                                @endif
                                {{ $extension }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Directory Permissions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Directory Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Directory</th>
                                    <th>Path</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($directoryPermissions as $name => $dir)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td><small>{{ $dir['path'] }}</small></td>
                                    <td>
                                        @if($dir['writable'])
                                        <span class="badge bg-success">Writable</span>
                                        @else
                                        <span class="badge bg-danger">Not Writable</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
