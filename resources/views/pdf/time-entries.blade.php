<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Entries Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #666;
        }

        .filters {
            background: #F3F4F6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .filters h3 {
            font-size: 12px;
            margin-bottom: 5px;
            color: #4F46E5;
        }

        .filters p {
            font-size: 9px;
            color: #666;
            margin: 2px 0;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-item {
            display: table-cell;
            width: 50%;
            padding: 15px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }

        .summary-item:first-child {
            border-right: none;
        }

        .summary-label {
            font-size: 9px;
            color: #6B7280;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #10B981;
        }

        .summary-value.time {
            color: #4F46E5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background: #4F46E5;
            color: white;
        }

        thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #E5E7EB;
        }

        tbody tr:nth-child(even) {
            background: #F9FAFB;
        }

        tbody td {
            padding: 8px 5px;
            font-size: 9px;
        }

        .project-badge {
            background: #DBEAFE;
            color: #1E40AF;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .status-active {
            color: #DC2626;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 8px;
            color: #9CA3AF;
        }

        .description {
            font-size: 8px;
            color: #6B7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Time Entries Report</h1>
        <div class="subtitle">Work Time Tracking Report</div>
    </div>

    @if(!empty($filters['start_date']) || !empty($filters['end_date']) || !empty($filters['project_id']))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(!empty($filters['start_date']))
            <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</p>
        @endif
        @if(!empty($filters['end_date']))
            <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</p>
        @endif
        @if(!empty($filters['project_id']))
            <p><strong>Project:</strong> {{ $entries->first()['project_name'] ?? 'N/A' }}</p>
        @endif
    </div>
    @endif

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Earnings</div>
            <div class="summary-value">R$ {{ number_format($totalEarnings, 2, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Time Worked</div>
            <div class="summary-value time">{{ $totalTime }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Project</th>
                <th>Type</th>
                <th>Start</th>
                <th>End</th>
                <th>Duration</th>
                <th>Description</th>
                <th>Attachments</th>
                <th class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
            <tr>
                <td>{{ $entry['date'] }}</td>
                <td>
                    <span class="project-badge">{{ $entry['project_name'] }}</span>
                </td>
                <td>
                    @php
                        $typeLabels = [
                            'development' => 'Development',
                            'maintenance' => 'Maintenance',
                            'meeting' => 'Meeting',
                            'research' => 'Research',
                            'documentation' => 'Documentation',
                            'review' => 'Review',
                            'support' => 'Support',
                            'planning' => 'Planning'
                        ];
                        $type = $entry['activity_type'] ?? 'development';
                    @endphp
                    <span style="font-size: 8px; color: #666;">{{ $typeLabels[$type] ?? 'Development' }}</span>
                </td>
                <td>{{ $entry['start_time'] }}</td>
                <td>
                    @if($entry['is_active'])
                        <span class="status-active">...</span>
                    @else
                        {{ $entry['end_time'] }}
                    @endif
                </td>
                <td class="font-mono">{{ $entry['duration_formatted'] }}</td>
                <td>
                    <div class="description">
                        {{ $entry['description'] ?: 'No description' }}
                    </div>
                </td>
                <td>
                    @php
                        $attachments = $entry['attachments'] ?? [];
                    @endphp
                    @if(count($attachments) > 0)
                        <span style="font-size: 7px; color: #6B7280;">
                            {{ count($attachments) }} file(s)
                        </span>
                    @else
                        <span style="font-size: 7px; color: #9CA3AF;">-</span>
                    @endif
                </td>
                <td class="text-right">
                    @if(!$entry['is_active'])
                        <strong>R$ {{ number_format($entry['earnings'], 2, ',', '.') }}</strong>
                    @else
                        <span style="color: #9CA3AF;">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px; color: #9CA3AF;">
                    No records found
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($entries->count() > 0)
        <tfoot>
            <tr style="background: #F3F4F6; border-top: 2px solid #4F46E5;">
                <td colspan="8" style="padding: 12px 5px; font-weight: bold; font-size: 11px; text-align: right; color: #1F2937;">
                    GRAND TOTAL:
                </td>
                <td class="text-right" style="padding: 12px 5px; font-weight: bold; font-size: 12px; color: #10B981;">
                    R$ {{ number_format($totalEarnings, 2, ',', '.') }}
                </td>
            </tr>
            <tr style="background: #F3F4F6;">
                <td colspan="6" style="padding: 8px 5px; font-weight: bold; font-size: 10px; text-align: right; color: #1F2937;">
                    TOTAL TIME:
                </td>
                <td colspan="3" class="text-right" style="padding: 8px 5px; font-weight: bold; font-size: 11px; color: #4F46E5;">
                    {{ $totalTime }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if($activitySummaries->count() > 1)
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <h3 style="font-size: 14px; color: #4F46E5; margin-bottom: 15px; border-bottom: 1px solid #E5E7EB; padding-bottom: 5px;">
            Summary by Activity Type
        </h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #F3F4F6;">
                <tr>
                    <th style="padding: 8px; text-align: left; font-size: 9px; color: #374151; font-weight: bold;">Type</th>
                    <th style="padding: 8px; text-align: center; font-size: 9px; color: #374151; font-weight: bold;">Records</th>
                    <th style="padding: 8px; text-align: center; font-size: 9px; color: #374151; font-weight: bold;">Total Time</th>
                    <th style="padding: 8px; text-align: right; font-size: 9px; color: #374151; font-weight: bold;">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activitySummaries as $summary)
                <tr style="border-bottom: 1px solid #E5E7EB;">
                    <td style="padding: 10px 8px; font-size: 10px; color: #1F2937;">
                        <strong>{{ $summary['label'] }}</strong>
                    </td>
                    <td style="padding: 10px 8px; text-align: center; font-size: 10px; color: #6B7280;">
                        {{ $summary['count'] }}
                    </td>
                    <td style="padding: 10px 8px; text-align: center; font-size: 10px; font-family: 'Courier New', monospace; color: #4F46E5; font-weight: bold;">
                        {{ $summary['total_time'] }}
                    </td>
                    <td style="padding: 10px 8px; text-align: right; font-size: 11px; color: #10B981; font-weight: bold;">
                        R$ {{ number_format($summary['total_earnings'], 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Report generated on {{ $generatedAt }}</p>
        <p>This document was automatically generated by PontoWeb system</p>
    </div>
</body>
</html>

