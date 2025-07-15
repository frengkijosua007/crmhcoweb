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
                'report_type' => 'revenue_forecast',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'pdf',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('reports.generate', [
                'report_type' => 'revenue_forecast',
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
        <div class="col-md-4 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $data['total_pipeline_projects'] }}</h5>
                            <small class="text-muted">Pipeline Projects</small>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="bi bi-building text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Rp {{ number_format($data['total_pipeline_value']/1000000, 1) }}M</h5>
                            <small class="text-muted">Pipeline Value</small>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="bi bi-cash-stack text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Rp {{ number_format($data['total_weighted_value']/1000000, 1) }}M</h5>
                            <small class="text-muted">Weighted Value</small>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="bi bi-graph-up text-success"></i>
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
                    <h6 class="mb-0">Monthly Forecast</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Pipeline by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Forecast Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Monthly Forecast</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Projects</th>
                            <th>Total Value</th>
                            <th>Weighted Value</th>
                            <th>Likelihood</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['by_month'] as $month => $monthData)
                        <tr>
                            <td>{{ $monthData['month'] }}</td>
                            <td>{{ $monthData['total_projects'] }}</td>
                            <td>Rp {{ number_format($monthData['total_value'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($monthData['weighted_value'], 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $likelihood = $monthData['total_value'] > 0 
                                        ? ($monthData['weighted_value'] / $monthData['total_value']) * 100 
                                        : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $likelihood }}%;"></div>
                                    </div>
                                    <span class="ms-2">{{ round($likelihood, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Distribution Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Pipeline by Status</h6>
            </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Projects</th>
                            <th>Total Value</th>
                            <th>Weighted Value</th>
                            <th>Probability</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['by_status'] as $status => $statusData)
                        <tr>
                            <td>{{ ucfirst($status) }}</td>
                            <td>{{ $statusData['count'] }}</td>
                            <td>Rp {{ number_format($statusData['value'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($statusData['weighted_value'], 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $probabilities = [
                                        'lead' => 10,
                                        'survey' => 30,
                                        'penawaran' => 50,
                                        'negosiasi' => 80
                                    ];
                                    $probability = $probabilities[$status] ?? 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $probability }}%;"></div>
                                    </div>
                                    <span class="ms-2">{{ $probability }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pipeline Projects Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Pipeline Projects</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>PIC</th>
                            <th>Value</th>
                            <th>Probability</th>
                            <th>Weighted Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['projects'] as $project)
                        <tr>
                            <td>
                                <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                                <div class="small text-muted">{{ $project->code }}</div>
                            </td>
                            <td>{{ $project->client->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $project->status_badge }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td>{{ $project->pic->name ?? 'N/A' }}</td>
                            <td>Rp {{ number_format($project->project_value, 0, ',', '.') }}</td>
                            <td>{{ $project->probability * 100 }}%</td>
                            <td>Rp {{ number_format($project->weighted_value, 0, ',', '.') }}</td>
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
// Monthly Forecast Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(function($item) { return $item['month']; }, $data['by_month'])) !!},
        datasets: [{
            label: 'Total Value (in Millions)',
            data: {!! json_encode(array_map(function($item) { return round($item['total_value']/1000000, 1); }, $data['by_month'])) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Weighted Value (in Millions)',
            data: {!! json_encode(array_map(function($item) { return round($item['weighted_value']/1000000, 1); }, $data['by_month'])) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Value (in Millions)'
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($data['by_status']->keys()->map(function($status) {
            return ucfirst($status);
        })) !!},
        datasets: [{
            data: {!! json_encode($data['by_status']->pluck('weighted_value')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(153, 102, 255, 0.6)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)'
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
                        return `${label}: Rp ${(value/1000000).toFixed(1)}M`;
                    }
                }
            }
        }
    }
});
</script>
@endpush