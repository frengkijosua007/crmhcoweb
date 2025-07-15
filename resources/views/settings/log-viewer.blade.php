@extends('layouts.app')

@section('title', 'Log Viewer')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Log Viewer: {{ $filename }}</h4>
            <p class="text-muted mb-0">Viewing log file contents</p>
        </div>
        <div>
            <a href="{{ route('settings.logs') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Logs
            </a>
            <a href="{{ route('settings.download-log', $filename) }}" class="btn btn-outline-success">
                <i class="bi bi-download me-2"></i>Download
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="log-content bg-dark text-white p-3 rounded" style="max-height: 70vh; overflow-y: auto;">
                <pre style="white-space: pre-wrap;">{{ $content }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection
