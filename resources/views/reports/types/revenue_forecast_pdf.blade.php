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
            width: 32%;
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
            <div class="summary-value">{{ $data['total_pipeline_projects'] }}</div>
            <div class="summary-label">Pipeline Projects</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($data['total_pipeline_value']/1000000, 1) }}M</div>
            <div class="summary-label">Pipeline Value</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($data['total_weighted_value']/1000000, 1) }}M</div>
            <div class="summary-label">Weighted Value</div>
        </div>
    </div>

    <div class="section-title">Monthly Forecast</div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Projects</th>
                <th>Total Value</th>
                <th>Weighted Value</th>
                <th>Likelihood</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['by_month'] as $month => $monthData)
            <tr>
                <td>{{ $monthData['month'] }}</td>
                <td>{{ $monthData['total_projects'] }}</td>
                <td>Rp {{ number_format($monthData['total_value'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($monthData['weighted_value'], 0, ',', '.') }}</td>
                <td>
                    @php
                        $likelihood = $monthData['total_value'] > 0 
                            ? ($monthData['weighted_value'] / $monthData['total_value']) * 100 
                            : 0;
                    @endphp
                    {{ round($likelihood, 1) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Pipeline by Status</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Projects</th>
                <th>Total Value</th>
                <th>Weighted Value</th>
                <th>Probability</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['by_status'] as $status => $statusData)
            <tr>
                <td>{{ ucfirst($status) }}</td>
                <td>{{ $statusData['count'] }}</td>
                <td>Rp {{ number_format($statusData['value'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($statusData['weighted_value'], 0, ',', '.') }}</td>
                <td>
                    @php
                        $probabilities = [
                            'lead' => 10,
                            'survey' => 30,
                            'penawaran' => 50,
                            'negosiasi' => 80
                        ];
                        $probability = $probabilities[$status] ?? 0;
                    @endphp
                    {{ $probability }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Pipeline Projects</div>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Client</th>
                <th>Status</th>
                <th>PIC</th>
                <th>Value</th>
                <th>Probability</th>
                <th>Weighted Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['projects'] as $project)
            <tr>
                <td>
                    {{ $project->name }}
                    <br><small>{{ $project->code }}</small>
                </td>
                <td>{{ $project->client->name ?? 'N/A' }}</td>
                <td>{{ ucfirst($project->status) }}</td>
                <td>{{ $project->pic->name ?? 'N/A' }}</td>
                <td>Rp {{ number_format($project->project_value, 0, ',', '.') }}</td>
                <td>{{ $project->probability * 100 }}%</td>
                <td>Rp {{ number_format($project->weighted_value, 0, ',', '.') }}</td>
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