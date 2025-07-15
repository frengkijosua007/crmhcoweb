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
                'report_type' => 'client_acquisition',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'pdf',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('reports.generate', [
                'report_type' => 'client_acquisition',
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
                            <h5 class="mb-1">{{ $data['total_clients'] }}</h5>
                            <small class="text-muted">New Clients</small>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10">
                            <i class="bi bi-people text-primary"></i>
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
                            <h5 class="mb-1">{{ $data['total_projects'] }}</h5>
                            <small class="text-muted">Projects</small>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10">
                            <i class="bi bi-building text-success"></i>
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
                            <h5 class="mb-1">Rp {{ number_format($data['total_project_value']/1000000, 1) }}M</h5>
                            <small class="text-muted">Project Value</small>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10">
                            <i class="bi bi-cash-stack text-warning"></i>
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
                                $avgValue = $data['total_clients'] > 0 
                                    ? $data['total_project_value'] / $data['total_clients'] 
                                    : 0;
                            @endphp
                            <h5 class="mb-1">Rp {{ number_format($avgValue/1000000, 1) }}M</h5>
                            <small class="text-muted">Avg Value/Client</small>
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
                    <h6 class="mb-0">Client Acquisition by Source</h6>
                </div>
                <div class="card-body">
                    <canvas id="sourceChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Monthly Acquisition Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Source Distribution Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Client Source Distribution</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Client Count</th>
                            <th>Project Count</th>
                            <th>Project Value</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['by_source'] as $source => $sourceData)
                        <tr>
                            <td>{{ ucfirst($source) }}</td>
                            <td>{{ $sourceData['count'] }}</td>
                            <td>{{ $sourceData['project_count'] }}</td>
                            <td>Rp {{ number_format($sourceData['project_value'], 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ ($sourceData['count'] / $data['total_clients']) * 100 }}%;"></div>
                                    </div>
                                    <span class="ms-2">{{ round(($sourceData['count'] / $data['total_clients']) * 100, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">New Clients List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Projects</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['clients'] as $client)
                        <tr>
                            <td>
                                <a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a>
                            </td>
                            <td>{{ $client->email ?: '-' }}</td>
                            <td>{{ $client->phone ?: '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($client->source) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $client->status == 'aktif' ? 'success' : 'warning' }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </td>
                            <td>{{ $client->projects->count() }}</td>
                            <td>{{ $client->created_at->format('d M Y') }}</td>
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
// Source Chart
const sourceCtx = document.getElementById('sourceChart').getContext('2d');
const sourceChart = new Chart(sourceCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($data['by_source']->keys()->map(function($source) {
            return ucfirst($source);
        })) !!},
        datasets: [{
            data: {!! json_encode($data['by_source']->pluck('count')) !!},
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
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

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($data['by_month']->keys()->map(function($month) {
            return Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y');
        })) !!},
        datasets: [{
            label: 'Clients',
            data: {!! json_encode($data['by_month']->pluck('count')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Projects',
            data: {!! json_encode($data['by_month']->pluck('project_count')) !!},
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