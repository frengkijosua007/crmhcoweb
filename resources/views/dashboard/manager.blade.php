@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Dashboard Manager</h4>
            <p class="text-muted mb-0">Overview performa bisnis {{ now()->format('F Y') }}</p>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active">Bulan Ini</button>
            <button type="button" class="btn btn-outline-primary">Kuartal</button>
            <button type="button" class="btn btn-outline-primary">Tahun</button>
        </div>
    </div>
    
    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Revenue</p>
                            <h4 class="mb-0">Rp 3.2M</h4>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> 12% vs bulan lalu
                            </small>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Conversion Rate</p>
                            <h4 class="mb-0">68%</h4>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> 5% improvement
                            </small>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Avg Deal Size</p>
                            <h4 class="mb-0">Rp 420Jt</h4>
                            <small class="text-danger">
                                <i class="bi bi-arrow-down"></i> 3% vs target
                            </small>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-calculator fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Active Projects</p>
                            <h4 class="mb-0">24</h4>
                            <small class="text-muted">
                                8 on schedule
                            </small>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row g-3 mb-4">
        <!-- Revenue Trend -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Revenue Trend & Forecast</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueTrendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
    <!-- Pipeline Distribution -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Pipeline Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-container-small">
                    <canvas id="pipelineChart"></canvas>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total Pipeline Value</small>
                    <h5>Rp 5.8 Miliar</h5>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Performance -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Team Performance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Marketing</th>
                            <th>Leads</th>
                            <th>Deals</th>
                            <th>Conversion</th>
                            <th>Revenue</th>
                            <th>Target Achievement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Marketing+1" 
                                         class="rounded-circle me-2" width="32" height="32">
                                    <span>Marketing Hansen</span>
                                </div>
                            </td>
                            <td>32</td>
                            <td>22</td>
                            <td>68.8%</td>
                            <td>Rp 1.2M</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                                <small>85%</small>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+M" 
                                         class="rounded-circle me-2" width="32" height="32">
                                    <span>Sarah M</span>
                                </div>
                            </td>
                            <td>28</td>
                            <td>18</td>
                            <td>64.3%</td>
                            <td>Rp 980Jt</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 73%"></div>
                                </div>
                                <small>73%</small>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Budi+S" 
                                         class="rounded-circle me-2" width="32" height="32">
                                    <span>Budi S</span>
                                </div>
                            </td>
                            <td>25</td>
                            <td>19</td>
                            <td>76.0%</td>
                            <td>Rp 1.1M</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                                <small>92%</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('styles')
<style>
/* Fix chart container heights */
canvas {
    max-height: 100% !important;
}

.chart-container {
    position: relative;
    height: 300px;
}

.chart-container-small {
    position: relative;
    height: 200px;
}

/* Icon boxes untuk stats */
.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
// Revenue Trend Chart
const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
new Chart(revenueTrendCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Actual',
            data: [280, 320, 300, 380, 350, 420, 380, 450, 480, 520, null, null],
            borderColor: '#1a73e8',
            backgroundColor: 'rgba(26, 115, 232, 0.1)',
            borderWidth: 3,
            tension: 0.4
        }, {
            label: 'Target',
            data: [300, 350, 350, 400, 400, 450, 450, 500, 500, 550, 550, 600],
            borderColor: '#ea4335',
            borderWidth: 2,
            borderDash: [5, 5]
        }, {
            label: 'Forecast',
            data: [null, null, null, null, null, null, null, null, null, 520, 540, 580],
            borderColor: '#34a853',
            borderWidth: 2,
            borderDash: [2, 2]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Rp ' + context.parsed.y + ' Juta';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value + 'M';
                    }
                }
            }
        }
    }
});



// Pipeline Chart
const pipelineCtx = document.getElementById('pipelineChart').getContext('2d');
new Chart(pipelineCtx, {
    type: 'doughnut',
    data: {
        labels: ['Lead', 'Survey', 'Penawaran', 'Negosiasi', 'Deal'],
        datasets: [{
            data: [800, 1200, 2100, 1500, 200],
            backgroundColor: [
                '#6c757d',
                '#17a2b8',
                '#ffc107',
                '#6f42c1',
                '#28a745'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 10,
                    usePointStyle: true,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});
</script>
@endpush