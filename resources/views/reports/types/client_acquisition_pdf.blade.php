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
            <div class="summary-value">{{ $data['total_clients'] }}</div>
            <div class="summary-label">New Clients</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $data['total_projects'] }}</div>
            <div class="summary-label">Projects</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($data['total_project_value']/1000000, 1) }}M</div>
            <div class="summary-label">Project Value</div>
        </div>
        <div class="summary-item">
            @php
                $avgValue = $data['total_clients'] > 0 
                    ? $data['total_project_value'] / $data['total_clients'] 
                    : 0;
            @endphp
            <div class="summary-value">Rp {{ number_format($avgValue/1000000, 1) }}M</div>
            <div class="summary-label">Avg Value/Client</div>
        </div>
    </div>

    <div class="section-title">Client Source Distribution</div>
    <table>
        <thead>
            <tr>
                <th>Source</th>
                <th>Client Count</th>
                <th>Project Count</th>
                <th>Project Value</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['by_source'] as $source => $sourceData)
            <tr>
                <td>{{ ucfirst($source) }}</td>
                <td>{{ $sourceData['count'] }}</td>
                <td>{{ $sourceData['project_count'] }}</td>
                <td>Rp {{ number_format($sourceData['project_value'], 0, ',', '.') }}</td>
                <td>{{ round(($sourceData['count'] / $data['total_clients']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Monthly Acquisition Trend</div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Client Count</th>
                <th>Project Count</th>
                <th>Project Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['by_month'] as $month => $monthData)
            <tr>
                <td>{{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</td>
                <td>{{ $monthData['count'] }}</td>
                <td>{{ $monthData['project_count'] }}</td>
                <td>Rp {{ number_format($monthData['project_value'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">New Clients List</div>
    <table>
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Source</th>
                <th>Status</th>
                <th>Projects</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['clients'] as $client)
            <tr>
                <td>{{ $client->name }}</td>
                <td>{{ $client->email ?: '-' }}</td>
                <td>{{ $client->phone ?: '-' }}</td>
                <td>{{ ucfirst($client->source) }}</td>
                <td>{{ ucfirst($client->status) }}</td>
                <td>{{ $client->projects->count() }}</td>
                <td>{{ $client->created_at->format('d M Y') }}</td>
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