@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-0">
    <h4 class="mb-4">Dashboard Admin</h4>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Total Clients</h6>
                        <div class="stat-value">{{ $totalClients }}</div>
                        <small class="text-success">+12% vs last month</small>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10">
                        <i class="bi bi-people fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Active Projects</h6>
                        <div class="stat-value">{{ $activeProjects }}</div>
                        <small class="text-warning">8 on schedule</small>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10">
                        <i class="bi bi-building fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Pending Surveys</h6>
                        <div class="stat-value">{{ $pendingSurveys }}</div>
                        <small class="text-danger">Need attention</small>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10">
                        <i class="bi bi-clipboard-check fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Pipeline Value</h6>
                        <div class="stat-value">{{ number_format($pipelineValue/1000000, 1) }}M</div>
                        <small class="text-success">+18% growth</small>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10">
                        <i class="bi bi-currency-dollar fs-4 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Revenue Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Project Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container-small">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Lead</span>
                            <strong>12</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Survey</span>
                            <strong>8</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Negotiation</span>
                            <strong>15</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Execution</span>
                            <strong>24</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Projects</h6>
                    <a href="#" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Office Renovation</div>
                                        <small class="text-muted">PRJ-2025-001</small>
                                    </td>
                                    <td>PT. Maju Jaya</td>
                                    <td><span class="badge bg-warning">Survey</span></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" style="width: 25%"></div>
                                        </div>
                                    </td>
                                    <td>Rp 450M</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Showroom Construction</div>
                                        <small class="text-muted">PRJ-2025-002</small>
                                    </td>
                                    <td>CV. Berkah Abadi</td>
                                    <td><span class="badge bg-primary">Execution</span></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" style="width: 65%"></div>
                                        </div>
                                    </td>
                                    <td>Rp 1.2B</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Chart container fixes */
.chart-container {
    position: relative;
    height: 300px;
}

.chart-container-small {
    position: relative;
    height: 200px;
}

canvas {
    max-height: 100% !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Log data untuk melihat apakah data yang dikirim benar
    console.log('Revenue Data:', @json(array_values($revenueData)));  // Untuk chart Revenue
    console.log('Project Status Data:', @json(array_values($projectStatus))); // Untuk chart Project Status

    // Chart untuk Revenue
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',  // Tipe chart, bisa 'line', 'bar', dll.
        data: {
            labels: @json(array_keys($revenueData)),  // Data label (x-axis)
            datasets: [{
                label: 'Revenue',
                data: @json(array_values($revenueData)),  // Data untuk chart (y-axis)
                backgroundColor: 'rgba(26, 115, 232, 0.2)',  // Warna background
                borderColor: 'rgba(26, 115, 232, 1)',  // Warna border
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,  // Responsif untuk ukuran layar
            scales: {
                y: {
                    beginAtZero: true  // Memulai dari 0 di sumbu Y
                }
            }
        }
    });

    // Chart untuk Project Status
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',  // Tipe chart bisa berbeda
        data: {
            labels: @json(array_keys($projectStatus)),  // Status proyek
            datasets: [{
                data: @json(array_values($projectStatus)),  // Jumlah masing-masing status proyek
                backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56'],  // Warna untuk tiap status
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>

<script>
    // Fungsi untuk mengupdate chart
    function updateDashboardData() {
        fetch('{{ route('dashboard.data') }}')
            .then(response => response.json())
            .then(data => {
                // Perbarui chart dengan data terbaru
                updateRevenueChart(data.revenueData);
                updateStatusChart(data.projectStatus);
            })
            .catch(error => console.error('Error:', error));
    }

    // Fungsi untuk memperbarui chart revenue
    function updateRevenueChart(data) {
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Revenue',
                    data: Object.values(data),
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
                                return 'Revenue: Rp ' + context.parsed.y + 'M';
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
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Fungsi untuk memperbarui chart status proyek
    function updateStatusChart(data) {
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: [
                        '#ffc107', '#17a2b8', '#6f42c1', '#28a745'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Polling setiap 10 detik
    setInterval(updateDashboardData, 10000); // Update setiap 10 detik
</script>




