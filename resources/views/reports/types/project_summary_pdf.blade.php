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
        .chart-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
        }
        .badge-primary { background-color: #007bff; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
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
            <div class="summary-value">{{ $data['total_projects'] }}</div>
            <div class="summary-label">Total Projects</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($data['total_value']/1000000, 1) }}M</div>
            <div class="summary-label">Total Value</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($data['total_deal_value']/1000000, 1) }}M</div>
            <div class="summary-label">Deal Value</div>
        </div>
        <div class="summary-item">
            @php
                $avgValue = $data['total_projects'] > 0 
                    ? $data['total_value'] / $data['total_projects'] 
                    : 0;
            @endphp
            <div class="summary-value">Rp {{ number_format($avgValue/1000000, 1) }}M</div>
            <div class="summary-label">Avg Project Value</div>
        </div>
    </div>

    <div class="chart-container">
        <div class="chart-title">Projects by Status</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Value</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['status_distribution'] as $status => $statusData)
                <tr>
                    <td>{{ ucfirst($status) }}</td>
                    <td>{{ $statusData['count'] }}</td>
                    <td>Rp {{ number_format($statusData['value'], 0, ',', '.') }}</td>
                    <td>{{ round(($statusData['count'] / $data['total_projects']) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="chart-container">
        <div class="chart-title">Projects by Type</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Count</th>
                    <th>Value</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['type_distribution'] as $type => $typeData)
                <tr>
                    <td>{{ ucfirst($type) }}</td>
                    <td>{{ $typeData['count'] }}</td>
                    <td>Rp {{ number_format($typeData['value'], 0, ',', '.') }}</td>
                    <td>{{ round(($typeData['count'] / $data['total_projects']) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="chart-title">Projects List</div>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Client</th>
                <th>Type</th>
                <th>Status</th>
                <th>PIC</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['projects'] as $project)
            <tr>
                <td>{{ $project->code }}</td>
                <td>{{ $project->name }}</td>
                <td>{{ $project->client->name ?? 'N/A' }}</td>
                <td>{{ ucfirst($project->type) }}</td>
                <td>{{ ucfirst($project->status) }}</td>
                <td>{{ $project->pic->name ?? 'N/A' }}</td>
                <td>
                    @if($project->deal_value)
                    Rp {{ number_format($project->deal_value, 0, ',', '.') }}
                    @else
                    Rp {{ number_format($project->project_value, 0, ',', '.') }}
                    @endif
                </td>
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