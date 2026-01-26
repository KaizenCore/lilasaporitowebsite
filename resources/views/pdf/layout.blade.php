<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'FrizzBoss Report' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #7c3aed;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #7c3aed;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .header .date {
            color: #999;
            font-size: 11px;
            margin-top: 10px;
        }
        .content {
            padding: 0 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f3e8ff;
            color: #7c3aed;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #faf5ff;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #f3e8ff;
            border-right: 2px solid #fff;
        }
        .stat-box:last-child {
            border-right: none;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #7c3aed;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .section-title {
            font-size: 16px;
            color: #7c3aed;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e9d5ff;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-purple { color: #7c3aed; }
        .font-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .page-break { page-break-after: always; }
        .check-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #666;
            margin-right: 8px;
            vertical-align: middle;
        }
        .attendance-row {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .attendance-row:nth-child(even) {
            background-color: #faf5ff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FrizzBoss</h1>
        <div class="subtitle">{{ $title ?? 'Report' }}</div>
        <div class="date">Generated: {{ now()->format('F j, Y \a\t g:i A') }}</div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        FrizzBoss Art Classes | Generated {{ now()->format('M j, Y') }}
    </div>
</body>
</html>
