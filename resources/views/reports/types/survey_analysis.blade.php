@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">{{ $title }}</h4>
            <p class="text-muted mb-0">Period: {{ $dateFrom->format('d M Y') }} to {{ $dateTo->format('d M Y') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.generate', [
                'report_type' => 'survey_analysis',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'pdf',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('reports.generate', [
                'report_type' => 'survey_analysis',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'excel',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-success">
                <i class="bi bi-file-excel me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $data['total_surveys'] }}</h5>
                            <small class="text-muted">Total Surveys</small>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="bi bi-clipboard-check text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $data['completed_surveys'] }}</h5>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $data['total_photos'] }}</h5>
                            <small class="text-muted">Photos</small>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="bi bi-camera text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $hours = floor($data['avg_completion_time'] / 60);
                                $minutes = $data['avg_completion_time'] % 60;
                                $timeFormat = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                            @endphp
                            <h5 class="mb-1">{{ $timeFormat }}</h5>
                            <small class="text-muted">Avg Completion Time</small>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10">
                            <i class="bi bi-clock-history text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Surveys by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Surveyor Performance</h6>
                </div>
                <div class="card-body">
                    <canvas id="surveyorChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Surveyor Performance Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Surveyor Performance</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Surveyor</th>
                            <th>Total Surveys</th>
                            <th>Completed</th>
                            <th>Completion Rate</th>
                            <th>Photos</th>
                            <th>Avg Photos/Survey</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $index = 1; @endphp
                        @foreach($data['by_surveyor'] as $surveyorData)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td>{{ $surveyorData['surveyor']->name }}</td>
                            <td>{{ $surveyorData['count'] }}</td>
                            <td>{{ $surveyorData['completed'] }}</td>
                            <td>
                                @php
                                    $completionRate = $surveyorData['count'] > 0 
                                        ? ($surveyorData['completed'] / $surveyorData['count']) * 100 
                                        : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $completionRate }}%;"></div>
                                    </div>
                                    <span class="ms-2">{{ round($completionRate, 1) }}%</span>
                                </div>
                            </td>
                            <td>{{ $surveyorData['photo_count'] }}</td>
                            <td>
                                {{ $surveyorData['completed'] > 0 
                                    ? round($surveyorData['photo_count'] / $surveyorData['completed'], 1) 
                                    : 0 }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Surveys Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Surveys List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Surveyor</th>
                            <th>Scheduled Date</th>
                            <th>Actual Date</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['surveys'] as $survey)
                        <tr>
                            <td>
                                <a href="{{ route('projects.show', $survey->project) }}">{{ $survey->project->name ?? 'N/A' }}</a>
                            </td>
                            <td>{{ $survey->surveyor->name }}</td>
                            <td>{{ $survey->scheduled_date->format('d M Y H:i') }}</td>
                            <td>
                                {{ $survey->actual_date ? $survey->actual_date->format('d M Y H:i') : '-' }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $survey->status_badge }}">
                                    {{ ucfirst($survey->status) }}
                                </span>
                            </td>
                            <td>{{ $survey->photos->count() }}</td>
                            <td>
                                @if($survey->latitude && $survey->longitude)
                                <a href="https://maps.google.com/?q={{ $survey->latitude }},{{ $survey->longitude }}" target="_blank">
                                    <i class="bi bi-geo-alt"></i> View
                                </a>
                                @else
                                -
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
@endsection

@push('scripts')
<script>
// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($data['by_status']->keys()->map(function($status) {
            return ucfirst($status);
        })) !!},
        datasets: [{
            data: {!! json_encode($data['by_status']->pluck('count')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(255, 99, 132, 0.6)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Surveyor Chart
const surveyorData = {!! json_encode($data['by_surveyor']) !!};
const surveyorNames = surveyorData.map(item => item.surveyor.name);
const surveyorTotals = surveyorData.map(item => item.count);
const surveyorCompleted = surveyorData.map(item => item.completed);

const surveyorCtx = document.getElementById('surveyorChart').getContext('2d');
const surveyorChart = new Chart(surveyorCtx, {
    type: 'bar',
    data: {
        labels: surveyorNames,
        datasets: [{
            label: 'Total Surveys',
            data: surveyorTotals,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Completed',
            data: surveyorCompleted,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush