<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato de Horas</title>
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
        <h1>Extrato de Horas</h1>
        <div class="subtitle">Relatório de Registro de Tempo</div>
    </div>

    @if(!empty($filters['start_date']) || !empty($filters['end_date']) || !empty($filters['project_id']))
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if(!empty($filters['start_date']))
            <p><strong>Data Inicial:</strong> {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</p>
        @endif
        @if(!empty($filters['end_date']))
            <p><strong>Data Final:</strong> {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</p>
        @endif
        @if(!empty($filters['project_id']))
            <p><strong>Projeto:</strong> {{ $entries->first()['project_name'] ?? 'N/A' }}</p>
        @endif
    </div>
    @endif

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Faturado</div>
            <div class="summary-value">R$ {{ number_format($totalEarnings, 2, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Tempo Total Trabalhado</div>
            <div class="summary-value time">{{ $totalTime }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Projeto</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Duração</th>
                <th>Descrição</th>
                <th class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
            <tr>
                <td>{{ $entry['date'] }}</td>
                <td>
                    <span class="project-badge">{{ $entry['project_name'] }}</span>
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
                        {{ $entry['description'] ?: 'Sem descrição' }}
                    </div>
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
                <td colspan="7" style="text-align: center; padding: 20px; color: #9CA3AF;">
                    Nenhum registro encontrado
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($entries->count() > 0)
        <tfoot>
            <tr style="background: #F3F4F6; border-top: 2px solid #4F46E5;">
                <td colspan="6" style="padding: 12px 5px; font-weight: bold; font-size: 11px; text-align: right; color: #1F2937;">
                    TOTAL GERAL:
                </td>
                <td class="text-right" style="padding: 12px 5px; font-weight: bold; font-size: 12px; color: #10B981;">
                    R$ {{ number_format($totalEarnings, 2, ',', '.') }}
                </td>
            </tr>
            <tr style="background: #F3F4F6;">
                <td colspan="6" style="padding: 8px 5px; font-weight: bold; font-size: 10px; text-align: right; color: #1F2937;">
                    TEMPO TOTAL:
                </td>
                <td class="text-right" style="padding: 8px 5px; font-weight: bold; font-size: 11px; color: #4F46E5;">
                    {{ $totalTime }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Relatório gerado em {{ $generatedAt }}</p>
        <p>Este documento foi gerado automaticamente pelo sistema PontoWeb</p>
    </div>
</body>
</html>

