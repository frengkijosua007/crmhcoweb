@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Report Generator</h4>
            <p class="text-muted mb-0">Generate custom reports for business analysis</p>
        </div>
    </div>

    <!-- Report Generator Card -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Generate Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.generate') }}" method="GET" id="reportForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label required">Report Type</label>
                        <select name="report_type" class="form-select" id="reportType" required>
                            <option value="">-- Select Report Type --</option>
                            @foreach($reportTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label required">From Date</label>
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ now()->subMonths(1)->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label required">To Date</label>
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label required">Format</label>
                        <select name="format" class="form-select" required>
                            <option value="html">View in Browser</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic Parameters Section -->
                <div id="parametersSection" class="mt-4" style="display: none;">
                    <h6 class="mb-3">Additional Parameters</h6>
                    
                    <!-- Project Summary Parameters -->
                    <div class="report-params" id="project_summary_params" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status Filter</label>
                                <select name="parameters[status]" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="lead">Lead</option>
                                    <option value="survey">Survey</option>
                                    <option value="penawaran">Penawaran</option>
                                    <option value="negosiasi">Negosiasi</option>
                                    <option value="deal">Deal</option>
                                    <option value="eksekusi">Eksekusi</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="batal">Batal</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Project Type</label>
                                <select name="parameters[type]" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="kantor">Kantor</option>
                                    <option value="showroom">Showroom</option>
                                    <option value="kafe">Kafe</option>
                                    <option value="restoran">Restoran</option>
                                    <option value="outlet">Outlet</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sales Performance Parameters -->
                    <div class="report-params" id="sales_performance_params" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Include Details</label>
                                <select name="parameters[include_details]" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Survey Analysis Parameters -->
                    <div class="report-params" id="survey_analysis_params" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status Filter</label>
                                <select name="parameters[status]" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Forecast Parameters -->
                    <div class="report-params" id="revenue_forecast_params" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Forecast Period</label>
                                <select name="parameters[forecast_period]" class="form-select">
                                    <option value="3">3 Months</option>
                                    <option value="6" selected>6 Months</option>
                                    <option value="12">12 Months</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Reports -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 me-3">
                            <i class="bi bi-building text-primary"></i>
                        </div>
                        <h5 class="mb-0">Project Summary</h5>
                    </div>
                    <p class="text-muted">Overview of all projects, status distribution, and values across different project types.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" 
                            onclick="setReportType('project_summary')">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 me-3">
                            <i class="bi bi-graph-up-arrow text-success"></i>
                        </div>
                        <h5 class="mb-0">Sales Performance</h5>
                    </div>
                    <p class="text-muted">Analyze sales team performance, conversion rates, and deal values by PIC.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" 
                            onclick="setReportType('sales_performance')">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning bg-opacity-10 me-3">
                            <i class="bi bi-people text-warning"></i>
                        </div>
                        <h5 class="mb-0">Client Acquisition</h5>
                    </div>
                    <p class="text-muted">Track new client acquisition by source, timeline, and resulting project values.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" 
                            onclick="setReportType('client_acquisition')">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info bg-opacity-10 me-3">
                            <i class="bi bi-clipboard-check text-info"></i>
                        </div>
                        <h5 class="mb-0">Survey Analysis</h5>
                    </div>
                    <p class="text-muted">Analyze survey completion rates, surveyor performance, and photo documentation statistics.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" 
                            onclick="setReportType('survey_analysis')">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 me-3">
                            <i class="bi bi-calendar-check text-danger"></i>
                        </div>
                        <h5 class="mb-0">Revenue Forecast</h5>
                    </div>
                    <p class="text-muted">Forecast revenue based on current pipeline, probability factors, and project timelines.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" 
                            onclick="setReportType('revenue_forecast')">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show parameters section based on report type selection
    const reportType = document.getElementById('reportType');
    reportType.addEventListener('change', function() {
        toggleParametersSection(this.value);
    });
});

function toggleParametersSection(reportType) {
    // Hide all parameter sections first
    document.querySelectorAll('.report-params').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show parameters section if we have parameters for this report type
    const paramsSection = document.getElementById('parametersSection');
    const specificParams = document.getElementById(reportType + '_params');
    
    if (specificParams) {
        paramsSection.style.display = 'block';
        specificParams.style.display = 'block';
    } else {
        paramsSection.style.display = 'none';
    }
}

function setReportType(type) {
    const reportType = document.getElementById('reportType');
    reportType.value = type;
    toggleParametersSection(type);
    
    // Scroll to form
    document.getElementById('reportForm').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush

