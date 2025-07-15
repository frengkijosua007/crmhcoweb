@extends('layouts.app')

@section('title', 'Pipeline Analytics')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Pipeline Analytics</h4>
            <p class="text-muted mb-0">Insights dan analisis performa pipeline</p>
        </div>
        <div>
            <div class="btn-group me-2">
                <a href="{{ route('pipeline.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-kanban me-1"></i>Pipeline
                </a>
                <a href="{{ route('pipeline.funnel') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Funnel
                </a>
            </div>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row g-3 mb-4">
        <!-- Pipeline Velocity -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Pipeline Velocity</h6>
                    <small class="text-muted">Average days in each stage</small>
                </div>
                <div class="card-body">
                    <canvas id="velocityChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Win/Loss Analysis -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Win/Loss Analysis</h6>
                    <small class="text-muted">Project outcomes</small>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <canvas id="winLossChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Won</span>
                                    <strong class="text-success">{{ $winLossData['won'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Lost</span>
                                    <strong class="text-danger">{{ $winLossData['lost'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>In Progress</span>
                                    <strong class="text-warning">{{ $winLossData['in_progress'] }}</strong>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <h5 class="mb-0">
                                    @php
                                        $total = $winLossData['won'] + $winLossData['lost'];
                                        $winRate = $total > 0 ? round(($winLossData['won'] / $total) * 100, 1) : 0;
                                    @endphp
                                    {{ $winRate }}%
                                </h5>
                                <small class="text-muted">Win Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Monthly Pipeline Value Trend ({{ now()->year }})</h6>
        </div>
        <div class="card-body">
            <canvas id="monthlyTrendChart" height="100"></canvas>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Top Performers</h6>
            <small class="text-muted">By deal value</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>PIC</th>
                            <th>Total Projects</th>
                            <th>Deal Value</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPerformers as $index => $performer)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($performer->pic->name ?? 'User') }}"
                                         alt="{{ $performer->pic->name ?? 'User' }}"
                                         class="rounded-circle me-2"
                                         width="32" height="32">
                                    {{ $performer->pic->name ?? 'No PIC' }}
                                </div>
                            </td>
                            <td>{{ $performer->total_projects }}</td>
                            <td>
                                <strong>Rp {{ number_format($performer->total_value/1000000, 0) }}M</strong>
                            </td>
                            <td>
                                @php
                                    $maxValue = $topPerformers->first()->total_value ?? 0;
                                    $percentage = $maxValue > 0 ? ($performer->total_value / $maxValue) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No performance data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pipeline Velocity Chart
    const velocityCtx = document.getElementById('velocityChart').getContext('2d');

    // Map status codes to readable names
    const statusMapping = {
        'lead': 'Lead',
        'survey': 'Survey',
        'penawaran': 'Quotation',
        'negosiasi': 'Negotiation',
        'deal': 'Deal',
        'eksekusi': 'Execution',
        'selesai': 'Completed',
        'batal': 'Cancelled'
    };

    const velocityLabels = {!! json_encode($velocityData->pluck('from_status')) !!}.map(status =>
        statusMapping[status] || status.charAt(0).toUpperCase() + status.slice(1)
    );

    new Chart(velocityCtx, {
        type: 'bar',
        data: {
            labels: velocityLabels,
            datasets: [{
                label: 'Average Days',
                data: {!! json_encode($velocityData->pluck('avg_days')) !!},
                backgroundColor: '#1a73e8',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(1) + ' days';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Days'
                    }
                }
            }
        }
    });

    // Win/Loss Chart
    const winLossCtx = document.getElementById('winLossChart').getContext('2d');
    new Chart(winLossCtx, {
        type: 'doughnut',
        data: {
            labels: ['Won', 'Lost', 'In Progress'],
            datasets: [{
                data: [
                    {{ $winLossData['won'] }},
                    {{ $winLossData['lost'] }},
                    {{ $winLossData['in_progress'] }}
                ],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Monthly Trend Chart
    const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');

    // Get month names
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const monthLabels = {!! json_encode($monthlyTrend->map(function($item) {
        return date('M', mktime(0, 0, 0, $item->month, 1));
    })) !!};

    new Chart(monthlyTrendCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Pipeline Value',
                data: {!! json_encode($monthlyTrend->pluck('total_value')) !!},
                borderColor: '#1a73e8',
                backgroundColor: 'rgba(26, 115, 232, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + (context.parsed.y / 1000000).toFixed(0) + 'M';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn-group, .nav, .btn, .navbar, .sidebar {
        display: none !important;
    }

    .card {
        break-inside: avoid;
    }

    body {
        padding: 0;
        margin: 0;
    }

    .container-fluid {
        width: 100%;
        padding: 0;
    }

    h4 {
        font-size: 16pt;
    }

    h6 {
        font-size: 12pt;
    }

    .text-muted {
        color: #666 !important;
    }
}
</style>
@endpush
