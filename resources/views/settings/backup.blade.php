@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Backup & Restore</h4>
            <p class="text-muted mb-0">Manage system backups and restoration</p>
        </div>
        <div>
            <a href="{{ route('settings.create-backup') }}" class="btn btn-primary">
                <i class="bi bi-cloud-upload me-2"></i>Create Backup
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
                        <a href="{{ route('settings.system') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-info-circle me-2"></i> System Information
                        </a>
                        <a href="{{ route('settings.backup') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Backup & Restore
                        </a>
                        <a href="{{ route('settings.logs') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-journal-text me-2"></i> System Logs
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Backup Information
                    </h6>
                    <p class="small text-muted mb-0">
                        Backups include your database and application files. They can be used to restore your system in case of data loss or when migrating to a new server.
                    </p>
                    <hr>
                    <div class="small">
                        <div class="mb-1"><strong>Last Backup:</strong>
                            @if(count($backupData) > 0)
                                {{ date('d M Y H:i', $backupData[0]['last_modified']) }}
                            @else
                                Never
                            @endif
                        </div>
                        <div><strong>Total Backups:</strong> {{ count($backupData) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Backup List -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Backup History</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($backupData) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backupData as $backup)
                                <tr>
                                    <td>{{ $backup['name'] }}</td>
                                    <td>{{ formatBytes($backup['size']) }}</td>
                                    <td>{{ date('d M Y H:i', $backup['last_modified']) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('settings.download-backup', $backup['name']) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <form action="{{ route('settings.delete-backup', $backup['name']) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this backup?');"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-cloud-slash fs-1 text-muted d-block mb-3"></i>
                        <h5>No Backups Found</h5>
                        <p class="text-muted">No backup files have been created yet.</p>
                        <a href="{{ route('settings.create-backup') }}" class="btn btn-primary">
                            <i class="bi bi-cloud-upload me-2"></i>Create First Backup
                        </a>
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
