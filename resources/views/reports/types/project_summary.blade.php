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
                'report_type' => 'project_summary',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'pdf',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('reports.generate', [
                'report_type' => 'project_summary',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'excel',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-success">
                <i class="bi bi-file-excel me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $data['total_projects'] }}</h5>
                            <small class="text-muted">Total Projects</small>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="bi bi-building text-primary"></i>
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
                            <h5 class="mb-1">Rp {{ number_format($data['total_value']/1000000, 1) }}M</h5>
                            <small class="text-muted">Total Value</small>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="bi bi-cash-stack text-success"></i>
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
                            <h5 class="mb-1">Rp {{ number_format($data['total_deal_value']/1000000, 1) }}M</h5>
                            <small class="text-muted">Deal Value</small>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="bi bi-check-circle text-warning"></i>
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
                                $avgValue = $data['total_projects'] > 0 
                                    ? $data['total_value'] / $data['total_projects'] 
                                    : 0;
                            @endphp
                            <h5 class="mb-1">Rp {{ number_format($avgValue/1000000, 1) }}M</h5>
                            <small class="text-muted">Avg Project Value</small>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10">
                            <i class="bi bi-graph-up text-info"></i>
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
                    <h6 class="mb-0">Projects by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Projects by Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Projects List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>PIC</th>
                            <th>Value</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['projects'] as $project)
                        <tr>
                            <td>{{ $project->code }}</td>
                            <td>
                                <a href="{{ route('projects.show', $project) }}">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td>{{ $project->client->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($project->type) }}</td>
                            <td>
                                <span class="badge bg-{{ $project->status_badge }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td>{{ $project->pic->name ?? 'N/A' }}</td>
                            <td>
                                @if($project->deal_value)
                                <strong class="text-success">
                                    Rp {{ number_format($project->deal_value, 0, ',', '.') }}
                                </strong>
                                @else
                                Rp {{ number_format($project->project_value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>{{ $project->created_at->format('d M Y') }}</td>
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
    type: 'bar',
    data: {
        labels: {!! json_encode($data['status_distribution']->keys()->map(function($status) {
            return ucfirst($status);
        })) !!},
        datasets: [{
            label: 'Number of Projects',
            data: {!! json_encode($data['status_distribution']->pluck('count')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(220, 53, 69, 0.6)',
                'rgba(108, 117, 125, 0.6)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(108, 117, 125, 1)'
            ],
            borderWidth: 1
        }, {
            label: 'Total Value (in Millions)',
            data: {!! json_encode($data['status_distribution']->pluck('value')->map(function($value) {
                return round($value / 1000000, 1);
            })) !!},
            type: 'line',
            fill: false,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 1)',
            pointBorderColor: 'rgba(255, 99, 132, 1)',
            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Projects'
                }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false
                },
                title: {
                    display: true,
                    text: 'Value (in Millions)'
                }
            }
        }
    }
});

// Type Chart
const typeCtx = document.getElementById('typeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($data['type_distribution']->keys()->map(function($type) {
            return ucfirst($type);
        })) !!},
        datasets: [{
            data: {!! json_encode($data['type_distribution']->pluck('count')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(255, 99, 132, 0.6)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
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
</script>
@endpush