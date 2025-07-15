@extends('layouts.app')

@section('title', 'Timeline Project')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Timeline: {{ $project->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Project</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->code }}</a></li>
                    <li class="breadcrumb-item active">Timeline</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="timeline-detailed">
                        @foreach($timeline as $event)
                        <div class="timeline-block">
                            <div class="timeline-dot bg-{{ $event['color'] }}">
                                <i class="bi {{ $event['icon'] }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $event['title'] }}</h6>
                                <p class="mb-2">{{ $event['description'] }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $event['date']->format('d M Y H:i') }} 
                                    ({{ $event['date']->diffForHumans() }})
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-detailed {
    position: relative;
    padding: 20px 0;
}

.timeline-detailed::before {
    content: '';
    position: absolute;
    left: 25px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-block {
    position: relative;
    margin-bottom: 30px;
    padding-left: 70px;
}

.timeline-dot {
    position: absolute;
    left: 10px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endpush