@extends('layouts.app')

@section('title', 'System Logs')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">System Logs</h4>
            <p class="text-muted mb-0">View and manage application logs</p>
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
                        <a href="{{ route('settings.system') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-info-circle me-2"></i> System Information
                        </a>
                        <a href="{{ route('settings.backup') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Backup & Restore
                        </a>
                        <a href="{{ route('settings.logs') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-journal-text me-2"></i> System Logs
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Log Information
                    </h6>
                    <p class="small text-muted mb-0">
                        System logs record application events and errors. They are useful for troubleshooting issues and monitoring system health.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column - Logs List -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Log Files</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($logData) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Last Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logData as $log)
                                <tr>
                                    <td>{{ $log['name'] }}</td>
                                    <td>{{ formatBytes($log['size']) }}</td>
                                    <td>{{ date('d M Y H:i', $log['last_modified']) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('settings.view-log', $log['name']) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('settings.download-log', $log['name']) }}"
                                               class="btn btn-sm btn-outline-success"
                                               data-bs-toggle="tooltip"
                                               title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                        <h5>No Log Files Found</h5>
                        <p class="text-muted">No log files are currently available.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}
@endphp
