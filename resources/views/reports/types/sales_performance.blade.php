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
                'report_type' => 'sales_performance',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'pdf',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('reports.generate', [
                'report_type' => 'sales_performance',
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'format' => 'excel',
                'parameters' => $parameters
            ]) }}" class="btn btn-outline-success">
                <i class="bi bi-file-excel me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Sales Performance Comparison</h6>
        </div>
        <div class="card-body">
            <canvas id="performanceChart" height="300"></canvas>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Sales Team Performance</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PIC</th>
                            <th>Total Projects</th>
                            <th>Won Projects</th>
                            <th>Conversion Rate</th>
                            <th>Pipeline Value</th>
                            <th>Deal Value</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $performance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $performance['user']->name }}</td>
                            <td>{{ $performance['total_projects'] }}</td>
                            <td>{{ $performance['won_projects'] }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $performance['conversion_rate'] }}%;"></div>
                                    </div>
                                    <span class="ms-2">{{ round($performance['conversion_rate'], 1) }}%</span>
                                </div>
                            </td>
                            <td>Rp {{ number_format($performance['total_value'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($performance['deal_value'], 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $maxDealValue = max(array_column($data, 'deal_value'));
                                    $performancePercentage = $maxDealValue > 0 ? ($performance['deal_value'] / $maxDealValue) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $performancePercentage }}%;"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Project Details (if requested) -->
    @if(isset($parameters['include_details']) && $parameters['include_details'])
        @foreach($data as $performance)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Projects by {{ $performance['user']->name }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Project Code</th>
                                <th>Project Name</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Value</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performance['projects'] as $project)
                            <tr>
                                <td>{{ $project->code }}</td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                                </td>
                                <td>{{ $project->client->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $project->status_badge }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
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
        @endforeach
    @endif
</div>
@endsection

@push('scripts')
<script>
// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(function($user) { return $user->name; }, array_column($data, 'user'))) !!},
        datasets: [{
            label: 'Pipeline Value (in Millions)',
            data: {!! json_encode(array_map(function($item) { return round($item['total_value']/1000000, 1); }, $data)) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Deal Value (in Millions)',
            data: {!! json_encode(array_map(function($item) { return round($item['deal_value']/1000000, 1); }, $data)) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }, {
            label: 'Conversion Rate (%)',
            data: {!! json_encode(array_map(function($item) { return round($item['conversion_rate'], 1); }, $data)) !!},
            type: 'line',
            fill: false,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 1)',
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
                    text: 'Value (in Millions)'
                }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                max: 100,
                grid: {
                    drawOnChartArea: false
                },
                title: {
                    display: true,
                    text: 'Conversion Rate (%)'
                }
            }
        }
    }
});
</script>
@endpush
