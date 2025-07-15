@extends('layouts.app')

@section('title', 'Sales Pipeline')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Sales Pipeline</h4>
            <p class="text-muted mb-0">Track dan kelola progress project dari lead hingga selesai</p>
        </div>
        <div>
            <div class="btn-group me-2">
                <a href="{{ route('pipeline.index', ['view' => 'kanban']) }}"
                   class="btn btn-sm {{ $viewType == 'kanban' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-kanban"></i> Kanban
                </a>
                <a href="{{ route('pipeline.index', ['view' => 'list']) }}"
                   class="btn btn-sm {{ $viewType == 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-list-ul"></i> List
                </a>
                <a href="{{ route('pipeline.funnel') }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-funnel"></i> Funnel
                </a>
                <a href="{{ route('pipeline.analytics') }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-graph-up"></i> Analytics
                </a>
            </div>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-2"></i>Filter
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-1">{{ $metrics['total_projects'] }}</h5>
                    <small class="text-muted">Total Projects</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-1">Rp {{ number_format($metrics['total_value']/1000000, 0) }}M</h5>
                    <small class="text-muted">Pipeline Value</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-1">{{ $metrics['conversion_rate'] }}%</h5>
                    <small class="text-muted">Conversion Rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-1">Rp {{ number_format($metrics['average_deal_size']/1000000, 0) }}M</h5>
                    <small class="text-muted">Avg Deal Size</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-1">{{ $metrics['win_rate'] }}%</h5>
                    <small class="text-muted">Win Rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-1">{{ $metrics['deal_value'] ? 'Rp ' . number_format($metrics['deal_value']/1000000, 0) . 'M' : '-' }}</h5>
                    <small class="text-muted">Deal Value</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline View -->
    @if($viewType == 'kanban')
    <!-- Kanban Board -->
    <div class="kanban-board">
        @php
            // Menggabungkan proyek dengan status yang sama
            $consolidatedPipeline = [];

            // Tahapan pipeline sesuai dengan SRD FR-PIPE-001
            $stageDefinitions = [
                [
                    'name' => 'Lead Masuk',
                    'slug' => 'lead',
                    'color' => '#6c757d'
                ],
                [
                    'name' => 'Penjadwalan Survey',
                    'slug' => 'survey',
                    'color' => '#0d6efd'
                ],
                [
                    'name' => 'Survey Dilakukan',
                    'slug' => 'survey_done',
                    'color' => '#6610f2'
                ],
                [
                    'name' => 'Penawaran Dibuat',
                    'slug' => 'quotation',
                    'color' => '#fd7e14'
                ],
                [
                    'name' => 'Negosiasi',
                    'slug' => 'negotiation',
                    'color' => '#ffc107'
                ],
                [
                    'name' => 'Deal/Kontrak',
                    'slug' => 'deal',
                    'color' => '#198754'
                ],
                [
                    'name' => 'Eksekusi Proyek',
                    'slug' => 'execution',
                    'color' => '#20c997'
                ],
                [
                    'name' => 'Selesai/Close',
                    'slug' => 'completed',
                    'color' => '#0dcaf0'
                ]
            ];

            // Bangun pipeline terkonsolidasi berdasarkan definisi tahapan
            foreach($stageDefinitions as $stageDef) {
                $stageProjects = [];
                $stageInfo = null;
                $stageCount = 0;
                $stageValue = 0;

                // Mencari stage yang cocok dari data pipeline
                foreach($pipeline as $column) {
                    if($column['stage']->slug === $stageDef['slug'] ||
                       (empty($stageInfo) && similar_text($column['stage']->name, $stageDef['name']) > 0.7)) {
                        $stageProjects = array_merge($stageProjects, $column['projects']->all());
                        $stageInfo = $column['stage'];
                        $stageCount += $column['count'];
                        $stageValue += $column['value'];
                    }
                }

                // Jika tidak ada stage yang cocok, buat stage kosong
                if(empty($stageInfo)) {
                    $stageInfo = (object)[
                        'name' => $stageDef['name'],
                        'slug' => $stageDef['slug'],
                        'color' => $stageDef['color']
                    ];
                }

                $consolidatedPipeline[] = [
                    'stage' => $stageInfo,
                    'projects' => collect($stageProjects),
                    'count' => $stageCount,
                    'value' => $stageValue
                ];
            }
        @endphp

        @foreach($consolidatedPipeline as $index => $column)
        <div class="kanban-column" data-status="{{ $column['stage']->slug }}">
            <div class="kanban-column-header" style="border-top: 3px solid {{ $column['stage']->color }}">
                <div>
                    <h6 class="mb-1">{{ $column['stage']->name }}</h6>
                    <small class="text-muted">
                        {{ $column['count'] }} projects • Rp {{ number_format($column['value']/1000000, 0) }}M
                    </small>

                    @if($index > 0 && $consolidatedPipeline[$index-1]['count'] > 0)
                    <div class="conversion-stat">
                        @php
                            $prevCount = $consolidatedPipeline[$index-1]['count'];
                            $currentCount = $column['count'];
                            $conversionRate = $prevCount > 0 ? round(($currentCount / $prevCount) * 100, 1) : 0;
                        @endphp
                        <small class="text-{{ $conversionRate >= 50 ? 'success' : 'warning' }}">
                            <i class="bi bi-arrow-right"></i> {{ $conversionRate }}% dari {{ $consolidatedPipeline[$index-1]['stage']->name }}
                        </small>
                    </div>
                    @endif
                </div>
                <span class="badge rounded-pill" style="background-color: {{ $column['stage']->color }}">
                    {{ $column['count'] }}
                </span>
            </div>

            <div class="kanban-column-body" data-status="{{ $column['stage']->slug }}">
                @foreach($column['projects'] as $project)
                <div class="kanban-card"
                     data-project-id="{{ $project->id }}"
                     data-project-status="{{ $project->status }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0 text-truncate">{{ $project->name }}</h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('projects.show', $project) }}">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('projects.edit', $project) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('projects.timeline', $project) }}">
                                        <i class="bi bi-clock-history me-2"></i>View Timeline
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <p class="text-muted small mb-2">{{ $project->client->name ?? 'No Client' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary">{{ $project->code }}</span>
                        <strong class="text-primary">
                            Rp {{ number_format(($project->deal_value ?? $project->project_value)/1000000, 0) }}M
                        </strong>
                    </div>

                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $project->pic->name ?? 'Unassigned' }}
                            </small>
                            <small class="text-muted">
                                {{ $project->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- List View -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Value</th>
                            <th>PIC</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Flatten the pipeline structure for list view
                            $allProjects = [];
                            foreach($consolidatedPipeline ?? $pipeline as $column) {
                                foreach($column['projects'] as $project) {
                                    $allProjects[] = [
                                        'project' => $project,
                                        'stage' => $column['stage']
                                    ];
                                }
                            }
                        @endphp

                        @foreach($allProjects as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item['project']->name }}</div>
                                <small class="text-muted">{{ $item['project']->code }}</small>
                            </td>
                            <td>{{ $item['project']->client->name ?? 'No Client' }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $item['stage']->color }}">
                                    {{ $item['stage']->name }}
                                </span>
                            </td>
                            <td>
                                <strong>Rp {{ number_format(($item['project']->deal_value ?? $item['project']->project_value)/1000000, 0) }}M</strong>
                            </td>
                            <td>{{ $item['project']->pic->name ?? 'Unassigned' }}</td>
                            <td>{{ $item['project']->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('projects.show', $item['project']) }}">
                                                <i class="bi bi-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('projects.edit', $item['project']) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('projects.timeline', $item['project']) }}">
                                                <i class="bi bi-clock-history me-2"></i>View Timeline
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Conversion Metrics Section (FR-PIPE-003) -->
    @if($viewType == 'kanban')
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Conversion Metrics</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h6 class="mb-2">Lead to Survey</h6>
                            @php
                                $leadCount = $consolidatedPipeline[0]['count'] ?? 0;
                                $surveyCount = $consolidatedPipeline[1]['count'] + $consolidatedPipeline[2]['count'] ?? 0;
                                $leadToSurvey = $leadCount > 0 ? round(($surveyCount / $leadCount) * 100, 1) : 0;
                            @endphp
                            <h3 class="mb-0 {{ $leadToSurvey >= 50 ? 'text-success' : 'text-warning' }}">
                                {{ $leadToSurvey }}%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h6 class="mb-2">Survey to Quotation</h6>
                            @php
                                $surveyCount = $consolidatedPipeline[1]['count'] + $consolidatedPipeline[2]['count'] ?? 0;
                                $quotationCount = $consolidatedPipeline[3]['count'] ?? 0;
                                $surveyToQuotation = $surveyCount > 0 ? round(($quotationCount / $surveyCount) * 100, 1) : 0;
                            @endphp
                            <h3 class="mb-0 {{ $surveyToQuotation >= 50 ? 'text-success' : 'text-warning' }}">
                                {{ $surveyToQuotation }}%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h6 class="mb-2">Quotation to Deal</h6>
                            @php
                                $quotationCount = $consolidatedPipeline[3]['count'] ?? 0;
                                $dealCount = $consolidatedPipeline[5]['count'] ?? 0;
                                $quotationToDeal = $quotationCount > 0 ? round(($dealCount / $quotationCount) * 100, 1) : 0;
                            @endphp
                            <h3 class="mb-0 {{ $quotationToDeal >= 30 ? 'text-success' : 'text-warning' }}">
                                {{ $quotationToDeal }}%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h6 class="mb-2">Average Time per Stage</h6>
                            <h3 class="mb-0">{{ $metrics['avg_time_per_stage'] ?? '7' }} days</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Pipeline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('pipeline.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Nama project atau client..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    @foreach($consolidatedPipeline as $column)
                                        <option value="{{ $column['stage']->slug }}" {{ request('status') == $column['stage']->slug ? 'selected' : '' }}>
                                            {{ $column['stage']->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai Minimum (Juta)</label>
                                <input type="number" name="min_value" class="form-control"
                                       placeholder="Contoh: 100" min="0"
                                       value="{{ request('min_value') }}">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="view" value="{{ $viewType }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.kanban-board {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    min-height: 600px;
}

.kanban-column {
    min-width: 320px;
    max-width: 320px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 300px);
}

.kanban-column-header {
    padding: 1rem;
    background-color: #fff;
    border-radius: 8px 8px 0 0;
    border-bottom: 2px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: start;
}

.kanban-column-body {
    padding: 0.5rem;
    overflow-y: auto;
    flex: 1;
}

.kanban-card {
    background-color: #fff;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    cursor: move;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.kanban-card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.kanban-card.dragging {
    opacity: 0.5;
    transform: rotate(3deg);
}

.kanban-column.drag-over {
    background-color: #e3f2fd;
}

.conversion-stat {
    margin-top: 5px;
    font-size: 0.75rem;
}

/* Color coding for urgency (Feature from FR-PIPE-002) */
.kanban-card[data-urgency="high"] {
    border-left: 3px solid #dc3545;
}

.kanban-card[data-urgency="medium"] {
    border-left: 3px solid #ffc107;
}

.kanban-card[data-urgency="low"] {
    border-left: 3px solid #198754;
}

/* Custom scrollbar for kanban columns */
.kanban-column-body::-webkit-scrollbar {
    width: 6px;
}

.kanban-column-body::-webkit-scrollbar-track {
    background: transparent;
}

.kanban-column-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.kanban-column-body::-webkit-scrollbar-thumb:hover {
    background: #999;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable for each kanban column
    const columns = document.querySelectorAll('.kanban-column-body');

    columns.forEach(column => {
        new Sortable(column, {
            group: 'shared',
            animation: 150,
            ghostClass: 'dragging',
            dragClass: 'dragging',
            onStart: function(evt) {
                evt.item.classList.add('dragging');

                // Highlight possible drop zones
                document.querySelectorAll('.kanban-column-body').forEach(col => {
                    if (col !== evt.from) {
                        col.classList.add('highlight-dropzone');
                    }
                });
            },
            onEnd: function(evt) {
                evt.item.classList.remove('dragging');

                // Remove highlight from drop zones
                document.querySelectorAll('.kanban-column-body').forEach(col => {
                    col.classList.remove('highlight-dropzone');
                });

                // Get project ID and new status
                const projectId = evt.item.dataset.projectId;
                const newStatus = evt.to.dataset.status;
                const oldStatus = evt.from.dataset.status;

                // If status changed, update in backend
                if (newStatus !== oldStatus) {
                    updateProjectStatus(projectId, newStatus);
                }
            }
        });
    });

    // Quick view functionality for project details
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dblclick', function() {
            const projectId = this.dataset.projectId;
            window.location.href = '/projects/' + projectId;
        });
    });
});

function updateProjectStatus(projectId, newStatus) {
    // Show loading
    Swal.fire({
        title: 'Updating...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("pipeline.update-stage") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            project_id: projectId,
            new_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Reload setelah sukses untuk memperbarui metrics dan cards
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.error || 'Update failed');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
        // Reload to revert changes
        setTimeout(() => window.location.reload(), 1500);
    });
}
</script>
@endpush
