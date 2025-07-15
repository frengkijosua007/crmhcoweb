@extends('layouts.app')

@section('title', 'Data Survey')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Data Survey</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Survey</li>
                </ol>
            </nav>
        </div>
        <div>
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager'))
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#assignSurveyModal">
                <i class="bi bi-person-check me-2"></i>Assign Surveyor
            </button>
            @endif

            @if(Auth::user()->hasRole('surveyor'))
            <a href="{{ route('surveys.mobile.form') }}" class="btn btn-primary">
                <i class="bi bi-camera me-2"></i>Mulai Survey
            </a>
            @else
            <a href="{{ route('surveys.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Jadwalkan Survey
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards for Surveyor -->
    @if(Auth::user()->hasRole('surveyor'))
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-warning mb-0">{{ $surveys->where('status', 'pending')->count() }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-info mb-0">{{ $surveys->where('status', 'in_progress')->count() }}</h4>
                    <small class="text-muted">In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-success mb-0">{{ $surveys->where('status', 'completed')->count() }}</h4>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-0">{{ $surveys->total() }}</h4>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('surveys.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari project atau klien..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date"
                           name="date"
                           class="form-control"
                           value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
                @if(request('search') || request('status') || request('date'))
                <div class="col-md-2">
                    <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Surveys Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Klien</th>
                            <th>Jadwal</th>
                            <th>Surveyor</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Lokasi</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surveys as $survey)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $survey->project->name }}</div>
                                <small class="text-muted">{{ $survey->project->code }}</small>
                            </td>
                            <td>{{ $survey->project->client->name }}</td>
                            <td>
                                <div>{{ $survey->scheduled_date->format('d M Y') }}</div>
                                <small class="text-muted">{{ $survey->scheduled_date->format('H:i') }}</small>
                            </td>
                            <td>{{ $survey->surveyor->name }}</td>
                            <td>
                                <span class="badge bg-{{ $survey->status_badge }}">
                                    {{ ucfirst($survey->status) }}
                                </span>
                            </td>
                            <td>
                                @if($survey->photos->count() > 0)
                                <span class="badge bg-secondary">
                                    <i class="bi bi-camera"></i> {{ $survey->photos->count() }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($survey->latitude && $survey->longitude)
                                <a href="https://maps.google.com/?q={{ $survey->latitude }},{{ $survey->longitude }}"
                                   target="_blank"
                                   class="text-primary">
                                    <i class="bi bi-geo-alt"></i> View
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('surveys.show', $survey) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($survey->status == 'pending' && Auth::user()->hasRole('surveyor') && $survey->surveyor_id == Auth::id())
                                    <a href="{{ route('surveys.mobile.form', ['survey_id' => $survey->id]) }}"
                                       class="btn btn-sm btn-outline-success"
                                       data-bs-toggle="tooltip"
                                       title="Start Survey">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                    @endif

                                    @if($survey->status == 'pending' && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('marketing')))
                                    <a href="{{ route('surveys.edit', $survey) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       data-bs-toggle="tooltip"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data survey
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($surveys->hasPages())
        <div class="card-footer">
            {{ $surveys->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Assign Survey -->
<div class="modal fade" id="assignSurveyModal" tabindex="-1" aria-labelledby="assignSurveyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignSurveyModalLabel">Assign Survey ke Surveyor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('surveys.assign', ['survey' => '_surveyId_', 'user' => '_userId_']) }}" method="POST" id="assignSurveyForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="surveySelect" class="form-label">Pilih Survey</label>
                        <select class="form-select" id="surveySelect" required>
                            <option value="">-- Pilih Survey --</option>
                            @foreach($surveys->where('status', 'pending') as $pendingSurvey)
                                <option value="{{ $pendingSurvey->id }}">{{ $pendingSurvey->project->name }} ({{ $pendingSurvey->scheduled_date->format('d M Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="surveyorSelect" class="form-label">Pilih Surveyor</label>
                        <select class="form-select" id="surveyorSelect" required>
                            <option value="">-- Pilih Surveyor --</option>
                            @foreach($surveyors ?? [] as $surveyor)
                                <option value="{{ $surveyor->id }}">{{ $surveyor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="surveyNote" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="surveyNote" name="note" rows="3" placeholder="Tambahkan catatan untuk surveyor"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Handle survey assignment form
document.addEventListener('DOMContentLoaded', function() {
    const surveySelect = document.getElementById('surveySelect');
    const surveyorSelect = document.getElementById('surveyorSelect');
    const assignForm = document.getElementById('assignSurveyForm');

    // Update form action when selections change
    function updateFormAction() {
        const selectedSurveyId = surveySelect ? surveySelect.value : '';
        const selectedSurveyorId = surveyorSelect ? surveyorSelect.value : '';

        if (selectedSurveyId && selectedSurveyorId) {
            const newAction = assignForm.action
                .replace('_surveyId_', selectedSurveyId)
                .replace('_userId_', selectedSurveyorId);
            assignForm.action = newAction;
        }
    }

    if (surveySelect) surveySelect.addEventListener('change', updateFormAction);
    if (surveyorSelect) surveyorSelect.addEventListener('change', updateFormAction);
});
</script>
@endpush
