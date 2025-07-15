<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Survey;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Authenticate]
#[RoleMiddleware('admin|manager|marketing')]
class ReportController extends Controller
{

    /**
     * Display report generator page
     */
    public function index()
    {
        $reportTypes = [
            'project_summary' => 'Project Summary Report',
            'sales_performance' => 'Sales Performance Report',
            'client_acquisition' => 'Client Acquisition Report',
            'survey_analysis' => 'Survey Analysis Report',
            'revenue_forecast' => 'Revenue Forecast Report',
        ];

        return view('reports.index', compact('reportTypes'));
    }

    /**
     * Generate the requested report
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:html,pdf,excel',
            'parameters' => 'nullable|array'
        ]);

        // Get date range
        $dateFrom = Carbon::parse($validated['date_from']);
        $dateTo = Carbon::parse($validated['date_to']);

        // Generate report data based on type
        switch ($validated['report_type']) {
            case 'project_summary':
                $data = $this->generateProjectSummaryReport($dateFrom, $dateTo, $validated['parameters'] ?? []);
                $title = 'Project Summary Report';
                break;

            case 'sales_performance':
                $data = $this->generateSalesPerformanceReport($dateFrom, $dateTo, $validated['parameters'] ?? []);
                $title = 'Sales Performance Report';
                break;

            case 'client_acquisition':
                $data = $this->generateClientAcquisitionReport($dateFrom, $dateTo, $validated['parameters'] ?? []);
                $title = 'Client Acquisition Report';
                break;

            case 'survey_analysis':
                $data = $this->generateSurveyAnalysisReport($dateFrom, $dateTo, $validated['parameters'] ?? []);
                $title = 'Survey Analysis Report';
                break;

            case 'revenue_forecast':
                $data = $this->generateRevenueForecastReport($dateFrom, $dateTo, $validated['parameters'] ?? []);
                $title = 'Revenue Forecast Report';
                break;

            default:
                return back()->with('error', 'Invalid report type');
        }

        // Format and return report based on requested format
        switch ($validated['format']) {
            case 'html':
                $view = 'reports.types.' . $validated['report_type'];
                return view($view, [
                    'data' => $data,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'title' => $title,
                    'parameters' => $validated['parameters'] ?? []
                ]);

            case 'pdf':
                $view = 'reports.types.' . $validated['report_type'] . '_pdf';
                $pdf = PDF::loadView($view, [
                    'data' => $data,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'title' => $title,
                    'parameters' => $validated['parameters'] ?? []
                ]);

                $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Ymd') . '.pdf';
                return $pdf->download($filename);

            case 'excel':
                return $this->generateExcelReport($data, $title, $dateFrom, $dateTo, $validated['report_type']);

            default:
                return back()->with('error', 'Invalid format');
        }
    }

    /**
     * Generate Project Summary Report
     */
    private function generateProjectSummaryReport($dateFrom, $dateTo, $parameters)
    {
        // Base query with date filter
        $query = Project::whereBetween('created_at', [$dateFrom, $dateTo])
                        ->with(['client', 'pic']);

        // Apply status filter if provided
        if (isset($parameters['status']) && $parameters['status']) {
            $query->where('status', $parameters['status']);
        }

        // Get projects
        $projects = $query->get();

        // Prepare summary data
        $summary = [
            'total_projects' => $projects->count(),
            'total_value' => $projects->sum('project_value'),
            'total_deal_value' => $projects->sum('deal_value'),
            'status_distribution' => $projects->groupBy('status')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'value' => $items->sum('project_value')
                    ];
                }),
            'type_distribution' => $projects->groupBy('type')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'value' => $items->sum('project_value')
                    ];
                }),
            'projects' => $projects
        ];

        return $summary;
    }

    /**
     * Generate Sales Performance Report
     */
    private function generateSalesPerformanceReport($dateFrom, $dateTo, $parameters)
    {
        // Get all marketing users (PIC)
        $marketingUsers = User::role('marketing')->get();

        // Initialize performance data
        $performanceData = [];

        foreach ($marketingUsers as $user) {
            // Get projects where user is PIC
            $projects = Project::where('pic_id', $user->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();

            // Calculate metrics
            $totalProjects = $projects->count();
            $totalValue = $projects->sum('project_value');
            $dealValue = $projects->whereIn('status', ['deal', 'eksekusi', 'selesai'])->sum('deal_value');
            $wonProjects = $projects->whereIn('status', ['deal', 'eksekusi', 'selesai'])->count();

            // Calculate conversion rate
            $conversionRate = $totalProjects > 0 ? ($wonProjects / $totalProjects) * 100 : 0;

            // Add to performance data
            $performanceData[] = [
                'user' => $user,
                'total_projects' => $totalProjects,
                'total_value' => $totalValue,
                'deal_value' => $dealValue,
                'won_projects' => $wonProjects,
                'conversion_rate' => $conversionRate,
                'projects' => $projects
            ];
        }

        // Sort by deal value (descending)
        usort($performanceData, function ($a, $b) {
            return $b['deal_value'] <=> $a['deal_value'];
        });

        return $performanceData;
    }

    /**
     * Generate Client Acquisition Report
     */
    private function generateClientAcquisitionReport($dateFrom, $dateTo, $parameters)
    {
        // Get clients created in date range
        $clients = Client::whereBetween('created_at', [$dateFrom, $dateTo])
                        ->with(['pic', 'projects'])
                        ->get();

        // Group by source
        $bySource = $clients->groupBy('source')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'project_count' => $items->flatMap->projects->count(),
                    'project_value' => $items->flatMap->projects->sum('project_value')
                ];
            });

        // Group by month
        $byMonth = $clients->groupBy(function ($client) {
            return $client->created_at->format('Y-m');
        })->map(function ($items) {
            return [
                'count' => $items->count(),
                'project_count' => $items->flatMap->projects->count(),
                'project_value' => $items->flatMap->projects->sum('project_value')
            ];
        });

        // Prepare summary data
        $summary = [
            'total_clients' => $clients->count(),
            'total_projects' => $clients->flatMap->projects->count(),
            'total_project_value' => $clients->flatMap->projects->sum('project_value'),
            'by_source' => $bySource,
            'by_month' => $byMonth,
            'clients' => $clients
        ];

        return $summary;
    }

    /**
     * Generate Survey Analysis Report
     */
    private function generateSurveyAnalysisReport($dateFrom, $dateTo, $parameters)
    {
        // Get surveys in date range
        $surveys = Survey::whereBetween('created_at', [$dateFrom, $dateTo])
                        ->with(['project', 'surveyor', 'photos'])
                        ->get();

        // Group by status
        $byStatus = $surveys->groupBy('status')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'photo_count' => $items->sum(function ($survey) {
                        return $survey->photos->count();
                    })
                ];
            });

        // Group by surveyor
        $bySurveyor = $surveys->groupBy('surveyor_id')
            ->map(function ($items) {
                return [
                    'surveyor' => $items->first()->surveyor,
                    'count' => $items->count(),
                    'completed' => $items->where('status', 'completed')->count(),
                    'photo_count' => $items->sum(function ($survey) {
                        return $survey->photos->count();
                    })
                ];
            });

        // Calculate average time between scheduled and actual
        $completedSurveys = $surveys->where('status', 'completed')
            ->where('actual_date', '!=', null);

        $avgCompletionTime = 0;
        if ($completedSurveys->count() > 0) {
            $totalMinutes = 0;
            foreach ($completedSurveys as $survey) {
                $totalMinutes += $survey->scheduled_date->diffInMinutes($survey->actual_date);
            }
            $avgCompletionTime = $totalMinutes / $completedSurveys->count();
        }

        // Prepare summary data
        $summary = [
            'total_surveys' => $surveys->count(),
            'completed_surveys' => $surveys->where('status', 'completed')->count(),
            'pending_surveys' => $surveys->where('status', 'pending')->count(),
            'cancelled_surveys' => $surveys->where('status', 'cancelled')->count(),
            'total_photos' => $surveys->sum(function ($survey) {
                return $survey->photos->count();
            }),
            'avg_completion_time' => $avgCompletionTime,
            'by_status' => $byStatus,
            'by_surveyor' => $bySurveyor,
            'surveys' => $surveys
        ];

        return $summary;
    }

    /**
     * Generate Revenue Forecast Report
     */
    private function generateRevenueForecastReport($dateFrom, $dateTo, $parameters)
    {
        // Get all projects in pipeline
        $projects = Project::whereIn('status', ['lead', 'survey', 'penawaran', 'negosiasi'])
                          ->with(['client', 'pic'])
                          ->get();

        // Define probability by status
        $probabilities = [
            'lead' => 0.1,
            'survey' => 0.3,
            'penawaran' => 0.5,
            'negosiasi' => 0.8,
        ];

        // Calculate weighted revenue
        $projects->map(function ($project) use ($probabilities) {
            $project->probability = $probabilities[$project->status] ?? 0;
            $project->weighted_value = $project->project_value * $project->probability;
            return $project;
        });

        // Group by month (expected close date based on created_at + average days in pipeline)
        $avgDaysInPipeline = 60; // Default 60 days

        $byMonth = [];
        $today = Carbon::today();

        for ($i = 0; $i < 6; $i++) {
            $month = $today->copy()->addMonths($i);
            $monthKey = $month->format('Y-m');

            $monthProjects = $projects->filter(function ($project) use ($month, $avgDaysInPipeline) {
                $expectedCloseDate = $project->created_at->addDays($avgDaysInPipeline);
                return $expectedCloseDate->year == $month->year && $expectedCloseDate->month == $month->month;
            });

            $byMonth[$monthKey] = [
                'month' => $month->format('M Y'),
                'total_projects' => $monthProjects->count(),
                'total_value' => $monthProjects->sum('project_value'),
                'weighted_value' => $monthProjects->sum('weighted_value'),
                'projects' => $monthProjects
            ];
        }

        // Prepare summary data
        $summary = [
            'total_pipeline_projects' => $projects->count(),
            'total_pipeline_value' => $projects->sum('project_value'),
            'total_weighted_value' => $projects->sum('weighted_value'),
            'by_status' => $projects->groupBy('status')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'value' => $items->sum('project_value'),
                        'weighted_value' => $items->sum('weighted_value')
                    ];
                }),
            'by_month' => $byMonth,
            'projects' => $projects
        ];

        return $summary;
    }

    /**
     * Generate Excel Report
     */
    private function generateExcelReport($data, $title, $dateFrom, $dateTo, $reportType)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set report title and date range
        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', 'Period: ' . $dateFrom->format('d M Y') . ' to ' . $dateTo->format('d M Y'));

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setSize(12);

        // Set column headers and data based on report type
        switch ($reportType) {
            case 'project_summary':
                $this->setProjectSummaryExcel($sheet, $data);
                break;

            case 'sales_performance':
                $this->setSalesPerformanceExcel($sheet, $data);
                break;

            case 'client_acquisition':
                $this->setClientAcquisitionExcel($sheet, $data);
                break;

            case 'survey_analysis':
                $this->setSurveyAnalysisExcel($sheet, $data);
                break;

            case 'revenue_forecast':
                $this->setRevenueForecastExcel($sheet, $data);
                break;
        }

        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Ymd') . '.xlsx';
        $filepath = storage_path('app/public/reports/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/reports'))) {
            mkdir(storage_path('app/public/reports'), 0777, true);
        }

        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    /**
     * Set Project Summary Excel Data
     */
    private function setProjectSummaryExcel($sheet, $data)
    {
        // Add Summary Section
        $sheet->setCellValue('A4', 'Summary');
        $sheet->setCellValue('A5', 'Total Projects:');
        $sheet->setCellValue('B5', $data['total_projects']);
        $sheet->setCellValue('A6', 'Total Project Value:');
        $sheet->setCellValue('B6', 'Rp ' . number_format($data['total_value'], 0, ',', '.'));
        $sheet->setCellValue('A7', 'Total Deal Value:');
        $sheet->setCellValue('B7', 'Rp ' . number_format($data['total_deal_value'], 0, ',', '.'));

        // Add Projects List
        $sheet->setCellValue('A9', 'Projects List');

        // Headers
        $sheet->setCellValue('A10', 'No');
        $sheet->setCellValue('B10', 'Project Code');
        $sheet->setCellValue('C10', 'Project Name');
        $sheet->setCellValue('D10', 'Client');
        $sheet->setCellValue('E10', 'Type');
        $sheet->setCellValue('F10', 'Status');
        $sheet->setCellValue('G10', 'PIC');
        $sheet->setCellValue('H10', 'Value');
        $sheet->setCellValue('I10', 'Created Date');

        // Style header row
        $sheet->getStyle('A10:I10')->getFont()->setBold(true);
        $sheet->getStyle('A10:I10')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        // Add data rows
        $row = 11;
        foreach ($data['projects'] as $index => $project) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $project->code);
            $sheet->setCellValue('C' . $row, $project->name);
            $sheet->setCellValue('D' . $row, $project->client->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, ucfirst($project->type));
            $sheet->setCellValue('F' . $row, ucfirst($project->status));
            $sheet->setCellValue('G' . $row, $project->pic->name ?? 'N/A');
            $sheet->setCellValue('H' . $row, 'Rp ' . number_format($project->project_value, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, $project->created_at->format('d M Y'));
            $row++;
        }
    }

    /**
     * Set Sales Performance Excel Data
     */
    private function setSalesPerformanceExcel($sheet, $data)
    {
        // Add Summary Section
        $sheet->setCellValue('A4', 'Sales Performance Summary');

        // Headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'PIC Name');
        $sheet->setCellValue('C5', 'Total Projects');
        $sheet->setCellValue('D5', 'Won Projects');
        $sheet->setCellValue('E5', 'Conversion Rate');
        $sheet->setCellValue('F5', 'Total Value');
        $sheet->setCellValue('G5', 'Deal Value');

        // Style header row
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        // Add data rows
        $row = 6;
        foreach ($data as $index => $salesData) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $salesData['user']->name);
            $sheet->setCellValue('C' . $row, $salesData['total_projects']);
            $sheet->setCellValue('D' . $row, $salesData['won_projects']);
            $sheet->setCellValue('E' . $row, round($salesData['conversion_rate'], 1) . '%');
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($salesData['total_value'], 0, ',', '.'));
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($salesData['deal_value'], 0, ',', '.'));
            $row++;
        }

        // Add Projects by PIC sections
        $row += 2;
        foreach ($data as $salesData) {
            $sheet->setCellValue('A' . $row, 'Projects by ' . $salesData['user']->name);
            $row++;

            // Project headers
            $sheet->setCellValue('A' . $row, 'No');
            $sheet->setCellValue('B' . $row, 'Project Code');
            $sheet->setCellValue('C' . $row, 'Project Name');
            $sheet->setCellValue('D' . $row, 'Client');
            $sheet->setCellValue('E' . $row, 'Status');
            $sheet->setCellValue('F' . $row, 'Value');
            $sheet->setCellValue('G' . $row, 'Created Date');

            // Style header row
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('DDDDDD');

            $row++;

            // Project data
            foreach ($salesData['projects'] as $index => $project) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $project->code);
                $sheet->setCellValue('C' . $row, $project->name);
                $sheet->setCellValue('D' . $row, $project->client->name ?? 'N/A');
                $sheet->setCellValue('E' . $row, ucfirst($project->status));
                $sheet->setCellValue('F' . $row, 'Rp ' . number_format($project->project_value, 0, ',', '.'));
                $sheet->setCellValue('G' . $row, $project->created_at->format('d M Y'));
                $row++;
            }

            $row += 2;
        }
    }

    /**
     * Set Client Acquisition Excel Data
     */
    private function setClientAcquisitionExcel($sheet, $data)
    {
        // Add Summary Section
        $sheet->setCellValue('A4', 'Client Acquisition Summary');
        $sheet->setCellValue('A5', 'Total New Clients:');
        $sheet->setCellValue('B5', $data['total_clients']);
        $sheet->setCellValue('A6', 'Total Projects from New Clients:');
        $sheet->setCellValue('B6', $data['total_projects']);
        $sheet->setCellValue('A7', 'Total Project Value:');
        $sheet->setCellValue('B7', 'Rp ' . number_format($data['total_project_value'], 0, ',', '.'));

        // Add Source Distribution
        $sheet->setCellValue('A9', 'Source Distribution');

        // Headers
        $sheet->setCellValue('A10', 'Source');
        $sheet->setCellValue('B10', 'Client Count');
        $sheet->setCellValue('C10', 'Project Count');
        $sheet->setCellValue('D10', 'Project Value');

        // Style header row
        $sheet->getStyle('A10:D10')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        // Add data rows
        $row = 11;
        foreach ($data['by_source'] as $source => $sourceData) {
            $sheet->setCellValue('A' . $row, ucfirst($source));
            $sheet->setCellValue('B' . $row, $sourceData['count']);
            $sheet->setCellValue('C' . $row, $sourceData['project_count']);
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($sourceData['project_value'], 0, ',', '.'));
            $row++;
        }

        // Add Monthly Distribution
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Monthly Distribution');
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'Month');
        $sheet->setCellValue('B' . $row, 'Client Count');
        $sheet->setCellValue('C' . $row, 'Project Count');
        $sheet->setCellValue('D' . $row, 'Project Value');

        // Style header row
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        $row++;

        // Add data rows
        foreach ($data['by_month'] as $month => $monthData) {
            $sheet->setCellValue('A' . $row, Carbon::createFromFormat('Y-m', $month)->format('M Y'));
            $sheet->setCellValue('B' . $row, $monthData['count']);
            $sheet->setCellValue('C' . $row, $monthData['project_count']);
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($monthData['project_value'], 0, ',', '.'));
            $row++;
        }

        // Add Clients List
        $row += 2;
        $sheet->setCellValue('A' . $row, 'New Clients List');
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Client Name');
        $sheet->setCellValue('C' . $row, 'Email');
        $sheet->setCellValue('D' . $row, 'Phone');
        $sheet->setCellValue('E' . $row, 'Source');
        $sheet->setCellValue('F' . $row, 'Status');
        $sheet->setCellValue('G' . $row, 'Project Count');
        $sheet->setCellValue('H' . $row, 'Created Date');

        // Style header row
        $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        $row++;

        // Add data rows
        foreach ($data['clients'] as $index => $client) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $client->name);
            $sheet->setCellValue('C' . $row, $client->email);
            $sheet->setCellValue('D' . $row, $client->phone);
            $sheet->setCellValue('E' . $row, ucfirst($client->source));
            $sheet->setCellValue('F' . $row, ucfirst($client->status));
            $sheet->setCellValue('G' . $row, $client->projects->count());
            $sheet->setCellValue('H' . $row, $client->created_at->format('d M Y'));
            $row++;
        }
    }

    /**
     * Set Survey Analysis Excel Data
     */
    private function setSurveyAnalysisExcel($sheet, $data)
    {
        // Add Summary Section
        $sheet->setCellValue('A4', 'Survey Analysis Summary');
        $sheet->setCellValue('A5', 'Total Surveys:');
        $sheet->setCellValue('B5', $data['total_surveys']);
        $sheet->setCellValue('A6', 'Completed Surveys:');
        $sheet->setCellValue('B6', $data['completed_surveys']);
        $sheet->setCellValue('A7', 'Pending Surveys:');
        $sheet->setCellValue('B7', $data['pending_surveys']);
        $sheet->setCellValue('A8', 'Total Photos:');
        $sheet->setCellValue('B8', $data['total_photos']);

        // Add Status Distribution
        $sheet->setCellValue('A10', 'Status Distribution');

        // Headers
        $sheet->setCellValue('A11', 'Status');
        $sheet->setCellValue('B11', 'Count');
        $sheet->setCellValue('C11', 'Photo Count');

        // Style header row
        $sheet->getStyle('A11:C11')->getFont()->setBold(true);
        $sheet->getStyle('A11:C11')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        // Add data rows
        $row = 12;
        foreach ($data['by_status'] as $status => $statusData) {
            $sheet->setCellValue('A' . $row, ucfirst($status));
            $sheet->setCellValue('B' . $row, $statusData['count']);
            $sheet->setCellValue('C' . $row, $statusData['photo_count']);
            $row++;
        }

        // Add Surveyor Performance
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Surveyor Performance');
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Surveyor Name');
        $sheet->setCellValue('C' . $row, 'Total Surveys');
        $sheet->setCellValue('D' . $row, 'Completed');
        $sheet->setCellValue('E' . $row, 'Completion Rate');
        $sheet->setCellValue('F' . $row, 'Photo Count');

        // Style header row
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        $row++;

        // Add data rows
        $index = 1;
        foreach ($data['by_surveyor'] as $surveyorData) {
            $completionRate = $surveyorData['count'] > 0
                ? round(($surveyorData['completed'] / $surveyorData['count']) * 100, 1)
                : 0;

            $sheet->setCellValue('A' . $row, $index);
            $sheet->setCellValue('B' . $row, $surveyorData['surveyor']->name);
            $sheet->setCellValue('C' . $row, $surveyorData['count']);
            $sheet->setCellValue('D' . $row, $surveyorData['completed']);
            $sheet->setCellValue('E' . $row, $completionRate . '%');
            $sheet->setCellValue('F' . $row, $surveyorData['photo_count']);
            $row++;
            $index++;
        }

        // Add Surveys List
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Surveys List');
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Project');
        $sheet->setCellValue('C' . $row, 'Surveyor');
        $sheet->setCellValue('D' . $row, 'Scheduled Date');
        $sheet->setCellValue('E' . $row, 'Actual Date');
        $sheet->setCellValue('F' . $row, 'Status');
        $sheet->setCellValue('G' . $row, 'Photos');

        // Style header row
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        $row++;

        // Add data rows
        foreach ($data['surveys'] as $index => $survey) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $survey->project->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $survey->surveyor->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $survey->scheduled_date->format('d M Y H:i'));
            $sheet->setCellValue('E' . $row, $survey->actual_date ? $survey->actual_date->format('d M Y H:i') : 'N/A');
            $sheet->setCellValue('F' . $row, ucfirst($survey->status));
            $sheet->setCellValue('G' . $row, $survey->photos->count());
            $row++;
        }
    }

    /**
     * Set Revenue Forecast Excel Data
     */
    private function setRevenueForecastExcel($sheet, $data)
    {
        // Add Summary Section
        $sheet->setCellValue('A4', 'Revenue Forecast Summary');
        $sheet->setCellValue('A5', 'Total Pipeline Projects:');
        $sheet->setCellValue('B5', $data['total_pipeline_projects']);
        $sheet->setCellValue('A6', 'Total Pipeline Value:');
        $sheet->setCellValue('B6', 'Rp ' . number_format($data['total_pipeline_value'], 0, ',', '.'));
        $sheet->setCellValue('A7', 'Total Weighted Value:');
        $sheet->setCellValue('B7', 'Rp ' . number_format($data['total_weighted_value'], 0, ',', '.'));

        // Add Monthly Forecast
        $sheet->setCellValue('A9', 'Monthly Forecast');

        // Headers
        $sheet->setCellValue('A10', 'Month');
        $sheet->setCellValue('B10', 'Projects');
        $sheet->setCellValue('C10', 'Total Value');
        $sheet->setCellValue('D10', 'Weighted Value');

        // Style header row
        $sheet->getStyle('A10:D10')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        // Add data rows
        $row = 11;
        foreach ($data['by_month'] as $month => $monthData) {
            $sheet->setCellValue('A' . $row, $monthData['month']);
            $sheet->setCellValue('B' . $row, $monthData['total_projects']);
            $sheet->setCellValue('C' . $row, 'Rp ' . number_format($monthData['total_value'], 0, ',', '.'));
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($monthData['weighted_value'], 0, ',', '.'));
            $row++;
        }

        // Add Status Distribution
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Status Distribution');
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'Status');
        $sheet->setCellValue('B' . $row, 'Projects');
        $sheet->setCellValue('C' . $row, 'Total Value');
        $sheet->setCellValue('D' . $row, 'Weighted Value');
        $sheet->setCellValue('E' . $row, 'Probability');

        // Style header row
        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        $row++;

        // Probability mapping
        $probabilities = [
            'lead' => '10%',
            'survey' => '30%',
            'penawaran' => '50%',
            'negosiasi' => '80%',
        ];

        // Add data rows
        foreach ($data['by_status'] as $status => $statusData) {
            $sheet->setCellValue('A' . $row, ucfirst($status));
            $sheet->setCellValue('B' . $row, $statusData['count']);
            $sheet->setCellValue('C' . $row, 'Rp ' . number_format($statusData['value'], 0, ',', '.'));
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($statusData['weighted_value'], 0, ',', '.'));
            $sheet->setCellValue('E' . $row, $probabilities[$status] ?? 'N/A');
            $row++;
        }

        // Add Projects List
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Pipeline Projects');
        $row++;

        // Headers
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Project Code');
        $sheet->setCellValue('C' . $row, 'Project Name');
        $sheet->setCellValue('D' . $row, 'Client');
        $sheet->setCellValue('E' . $row, 'Status');
        $sheet->setCellValue('F' . $row, 'Value');
        $sheet->setCellValue('G' . $row, 'Probability');
        $sheet->setCellValue('H' . $row, 'Weighted Value');
        $sheet->setCellValue('I' . $row, 'PIC');

        // Style header row
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');

        $row++;

        // Add data rows
        foreach ($data['projects'] as $index => $project) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $project->code);
            $sheet->setCellValue('C' . $row, $project->name);
            $sheet->setCellValue('D' . $row, $project->client->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, ucfirst($project->status));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($project->project_value, 0, ',', '.'));
            $sheet->setCellValue('G' . $row, ($project->probability * 100) . '%');
            $sheet->setCellValue('H' . $row, 'Rp ' . number_format($project->weighted_value, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, $project->pic->name ?? 'N/A');
            $row++;
        }
    }
}
