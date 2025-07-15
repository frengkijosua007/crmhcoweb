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

    <div class="section-title">Sales Team Performance</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>PIC</th>
                <th>Total Projects</th>
                <th>Won Projects</th>
                <th>Conversion Rate</th>
                <th>Pipeline Value</th>
                <th>Deal Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $performance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $performance['user']->name }}</td>
                <td>{{ $performance['total_projects'] }}</td>
                <td>{{ $performance['won_projects'] }}</td>
                <td>{{ round($performance['conversion_rate'], 1) }}%</td>
                <td>Rp {{ number_format($performance['total_value'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($performance['deal_value'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Project Details (if requested) -->
    @if(isset($parameters['include_details']) && $parameters['include_details'])
        @foreach($data as $performance)
            <div class="section-title">Projects by {{ $performance['user']->name }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Project Code</th>
                        <th>Project Name</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Value</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($performance['projects'] as $project)
                    <tr>
                        <td>{{ $project->code }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->client->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($project->status) }}</td>
                        <td>
                            @if($project->deal_value)
                            Rp {{ number_format($project->deal_value, 0, ',', '.') }}
                            @else
                            Rp {{ number_format($project->project_value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td>{{ $project->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif

    <div class="footer">
        <div>Generated on {{ now()->format('d M Y H:i') }}</div>
        <div>© {{ now()->format('Y') }} Your Company Name. All rights reserved.</div>
    </div>
</body>
</html>