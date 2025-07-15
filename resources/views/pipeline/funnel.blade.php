@extends('layouts.app')

@section('title', 'Sales Funnel')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Sales Funnel</h4>
            <p class="text-muted mb-0">Visualisasi conversion rate di setiap tahapan</p>
        </div>
        <div>
            <div class="btn-group">
                <a href="{{ route('pipeline.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-kanban me-1"></i>Pipeline
                </a>
                <a href="{{ route('pipeline.analytics') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-graph-up me-1"></i>Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pipeline.funnel') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-2"></i>Update Funnel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Funnel Visualization -->
    <div class="card">
        <div class="card-body">
            <div class="funnel-container">
                @foreach($funnelData as $index => $data)
                <div class="funnel-stage" style="width: {{ 100 - ($index * 10) }}%;">
                    <div class="funnel-block" style="background-color: {{ $data['stage']->color }}">
                        <div class="funnel-content">
                            <h5 class="text-white mb-1">{{ $data['stage']->name }}</h5>
                            <h3 class="text-white mb-0">{{ $data['count'] }}</h3>
                            <small class="text-white-50">projects</small>
                        </div>
                    </div>
                    @if($index < count($funnelData) - 1)
                    <div class="conversion-rate">
                        <i class="bi bi-arrow-down"></i>
                        <span>{{ $data['conversion_rate'] }}%</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Funnel Stats -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Leads</h6>
                    <h3 class="mb-0">{{ $funnelData[0]['count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Conversions</h6>
                    <h3 class="mb-0">{{ end($funnelData)['count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Overall Conversion Rate</h6>
                    <h3 class="mb-0">
                        @php
                            $first = $funnelData[0]['count'] ?? 0;
                            $last = end($funnelData)['count'] ?? 0;
                            $rate = $first > 0 ? round(($last / $first) * 100, 1) : 0;
                        @endphp
                        {{ $rate }}%
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.funnel-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 0;
}

.funnel-stage {
    margin-bottom: 1.5rem;
    position: relative;
    transition: all 0.3s ease;
}

.funnel-block {
    padding: 2rem;
    text-align: center;
    clip-path: polygon(10% 0%, 90% 0%, 100% 100%, 0% 100%);
    position: relative;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.funnel-stage:first-child .funnel-block {
    clip-path: none;
    border-radius: 8px 8px 0 0;
}

.funnel-stage:last-child .funnel-block {
    clip-path: polygon(0 0, 100% 0, 90% 100%, 10% 100%);
    border-radius: 0 0 8px 8px;
}

.funnel-content {
    z-index: 2;
}

.conversion-rate {
    position: absolute;
    right: -80px;
    top: 50%;
    transform: translateY(-50%);
    text-align: center;
    font-weight: 600;
    color: #666;
    background-color: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.conversion-rate i {
    display: block;
    font-size: 24px;
    color: #999;
}

@media (max-width: 768px) {
    .funnel-stage {
        width: 100% !important;
    }

    .conversion-rate {
        position: static;
        transform: none;
        margin: 0.5rem auto 1.5rem;
        max-width: 120px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effect on funnel stages
    const funnelStages = document.querySelectorAll('.funnel-stage');

    funnelStages.forEach(stage => {
        stage.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });

        stage.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush
