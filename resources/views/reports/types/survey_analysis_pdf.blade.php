<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        h1 {
            font-size: 20px;
            margin: 5px 0;
            color: #1a73e8;
        }
        .period {
            font-size: 14px;
            margin-top: 5px;
            color: #666;
        }
        .summary-box {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            width: 24%;
            text-align: center;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" class="logo">
        <h1>{{ $title }}</h1>
        <div class="period">Period: {{ $dateFrom->format('d M Y') }} to {{ $dateTo->format('d M Y') }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-value">{{ $data['total_surveys'] }}</div>
            <div class="summary-label">Total Surveys</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $data['completed_surveys'] }}</div>
            <div class="summary-label">Completed</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $data['total_photos'] }}</div>
            <div class="summary-label">Photos</div>
        </div>
        <div class="summary-item">
            @php
                $hours = floor($data['avg_completion_time'] / 60);
                $minutes = $data['avg_completion_time'] % 60;
                $timeFormat = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
            @endphp
            <div class="summary-value">{{ $timeFormat }}</div>
            <div class="summary-label">Avg Completion Time</div>
        </div>
    </div>

    <div class="section-title">Status Distribution</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Photos</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['by_status'] as $status => $statusData)
            <tr>
                <td>{{ ucfirst($status) }}</td>
                <td>{{ $statusData['count'] }}</td>
                <td>{{ $statusData['photo_count'] }}</td>
                <td>{{ round(($statusData['count'] / $data['total_surveys']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Surveyor Performance</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Surveyor</th>
                <th>Total Surveys</th>
                <th>Completed</th>
                <th>Completion Rate</th>
                <th>Photos</th>
                <th>Avg Photos/Survey</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 1; @endphp
            @foreach($data['by_surveyor'] as $surveyorData)
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $surveyorData['surveyor']->name }}</td>
                <td>{{ $surveyorData['count'] }}</td>
                <td>{{ $surveyorData['completed'] }}</td>
                <td>
                    @php
                        $completionRate = $surveyorData['count'] > 0 
                            ? ($surveyorData['completed'] / $surveyorData['count']) * 100 
                            : 0;
                    @endphp
                    {{ round($completionRate, 1) }}%
                </td>
                <td>{{ $surveyorData['photo_count'] }}</td>
                <td>
                    {{ $surveyorData['completed'] > 0 
                        ? round($surveyorData['photo_count'] / $surveyorData['completed'], 1) 
                        : 0 }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Surveys List</div>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Surveyor</th>
                <th>Scheduled Date</th>
                <th>Actual Date</th>
                <th>Status</th>
                <th>Photos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['surveys'] as $survey)
            <tr>
                <td>{{ $survey->project->name ?? 'N/A' }}</td>
                <td>{{ $survey->surveyor->name }}</td>
                <td>{{ $survey->scheduled_date->format('d M Y H:i') }}</td>
                <td>
                    {{ $survey->actual_date ? $survey->actual_date->format('d M Y H:i') : '-' }}
                </td>
                <td>{{ ucfirst($survey->status) }}</td>
                <td>{{ $survey->photos->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Generated on {{ now()->format('d M Y H:i') }}</div>
        <div>© {{ now()->format('Y') }} Your Company Name. All rights reserved.</div>
    </div>
</body>
</html>