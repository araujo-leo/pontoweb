<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $rawEntries = $query->orderBy('start_time', 'desc')->get();

        $processedEntries = $rawEntries->map(function ($entry) {
            $start = \Carbon\Carbon::parse($entry->start_time);
            $end = $entry->end_time ? \Carbon\Carbon::parse($entry->end_time) : null;

            $durationInMinutes = 0;
            $earnings = 0;

            if ($end) {
                $durationInMinutes = abs($end->diffInMinutes($start));
                $earnings = ($durationInMinutes / 60) * $entry->project->hourly_rate;
            }

            return [
                'id' => $entry->id,
                'project_id' => $entry->project_id,
                'project_name' => $entry->project->name,
                'hourly_rate' => $entry->project->hourly_rate,
                'date' => $start->format('d/m/Y'),
                'start_time' => $start->format('H:i'),
                'end_time' => $end ? $end->format('H:i') : '...',
                'duration_formatted' => $end ? $end->diff($start)->format('%H:%I:%S') : '-',
                'duration_minutes' => $durationInMinutes,
                'earnings' => $earnings,
                'is_active' => is_null($entry->end_time),
                'description' => $entry->description,
            ];
        });

        $projectSummaries = $processedEntries->groupBy('project_id')->map(function ($group) {
            $totalMinutes = $group->sum('duration_minutes');
            $h = floor($totalMinutes / 60);
            $m = $totalMinutes % 60;

            return [
                'name' => $group->first()['project_name'],
                'total_earnings' => $group->sum('earnings'),
                'total_time' => sprintf('%dh %02dm', $h, $m),
                'count' => $group->count(),
                'hourly_rate' => $group->first()['hourly_rate']
            ];
        })->values();

        $grandTotalEarnings = $processedEntries->sum('earnings');
        $grandTotalMinutes = $processedEntries->sum('duration_minutes');
        $grandHours = floor($grandTotalMinutes / 60);
        $grandMinutes = $grandTotalMinutes % 60;

        // Get user projects for filter
        $projects = Project::where('user_id', auth()->id())
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return \Inertia\Inertia::render('TimeEntries/Index', [
            'entries' => $processedEntries,
            'projectSummaries' => $projectSummaries,
            'totalEarnings' => $grandTotalEarnings,
            'totalTime' => sprintf('%dh %02dm', $grandHours, $grandMinutes),
            'projects' => $projects,
            'filters' => $request->only(['start_date', 'end_date', 'project_id']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $project = \App\Models\Project::where('id', $request->project_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$project) {
            return redirect()->back()->withErrors(['timer' => 'Projeto não encontrado.']);
        }

        $hasRunningTimer = TimeEntry::where('project_id', $request->project_id)
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->whereNull('end_time')
            ->exists();

        if ($hasRunningTimer) {
            return redirect()->back()->withErrors(['timer' => 'Já existe um timer em execução para este projeto.']);
        }


        TimeEntry::create([
            'project_id' => $request->project_id,
            'start_time' => now(),
            'description' => $request->description,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, TimeEntry $timeEntry)
    {
        $request->validate([
            'description' => 'nullable|string|max:1000',
        ]);

        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $timeEntry->end_time = now();

        if ($request->filled('description')) {
            $timeEntry->description = $request->description;
        }

        $timeEntry->save();

        return redirect()->back()->with('success', 'Tarefa finalizada!');
    }

    public function updateDescription(Request $request, TimeEntry $timeEntry)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $timeEntry->description = $request->description;
        $timeEntry->save();

        return redirect()->back()->with('success', 'Descrição atualizada!');
    }

    public function exportCsv(Request $request)
    {
        $query = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $entries = $query->orderBy('start_time', 'desc')->get();

        $filename = 'extrato_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($entries) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'Data',
                'Projeto',
                'Hora Inicial',
                'Hora Final',
                'Duração',
                'Descrição',
                'Valor/Hora',
                'Valor Gerado'
            ], ';');

            $totalEarnings = 0;
            $totalMinutes = 0;

            // Data
            foreach ($entries as $entry) {
                $start = Carbon::parse($entry->start_time);
                $end = $entry->end_time ? Carbon::parse($entry->end_time) : null;

                $durationInMinutes = 0;
                $earnings = 0;
                $duration = '-';

                if ($end) {
                    $durationInMinutes = abs($end->diffInMinutes($start));
                    $earnings = ($durationInMinutes / 60) * $entry->project->hourly_rate;
                    $duration = $end->diff($start)->format('%H:%I:%S');

                    $totalEarnings += $earnings;
                    $totalMinutes += $durationInMinutes;
                }

                fputcsv($file, [
                    $start->format('d/m/Y'),
                    $entry->project->name,
                    $start->format('H:i'),
                    $end ? $end->format('H:i') : '...',
                    $duration,
                    $entry->description ?? '',
                    number_format($entry->project->hourly_rate, 2, ',', '.'),
                    $end ? number_format($earnings, 2, ',', '.') : '-',
                ], ';');
            }

            // Total row
            if ($entries->count() > 0) {
                $totalHours = floor($totalMinutes / 60);
                $totalMins = $totalMinutes % 60;
                $totalDuration = sprintf('%02dh %02dm', $totalHours, $totalMins);

                // Empty line
                fputcsv($file, [], ';');

                // Total row
                fputcsv($file, [
                    '',
                    '',
                    '',
                    '',
                    $totalDuration,
                    'TOTAL GERAL',
                    '',
                    number_format($totalEarnings, 2, ',', '.')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $rawEntries = $query->orderBy('start_time', 'desc')->get();

        $processedEntries = $rawEntries->map(function ($entry) {
            $start = Carbon::parse($entry->start_time);
            $end = $entry->end_time ? Carbon::parse($entry->end_time) : null;

            $durationInMinutes = 0;
            $earnings = 0;

            if ($end) {
                $durationInMinutes = abs($end->diffInMinutes($start));
                $earnings = ($durationInMinutes / 60) * $entry->project->hourly_rate;
            }

            return [
                'date' => $start->format('d/m/Y'),
                'project_name' => $entry->project->name,
                'start_time' => $start->format('H:i'),
                'end_time' => $end ? $end->format('H:i') : '...',
                'duration_formatted' => $end ? $end->diff($start)->format('%H:%I:%S') : '-',
                'duration_minutes' => $durationInMinutes,
                'earnings' => $earnings,
                'is_active' => is_null($entry->end_time),
                'description' => $entry->description,
                'hourly_rate' => $entry->project->hourly_rate,
            ];
        });

        $totalEarnings = $processedEntries->sum('earnings');
        $totalMinutes = $processedEntries->sum('duration_minutes');
        $totalHours = floor($totalMinutes / 60);
        $totalMins = $totalMinutes % 60;
        $totalTime = sprintf('%dh %02dm', $totalHours, $totalMins);

        $data = [
            'entries' => $processedEntries,
            'totalEarnings' => $totalEarnings,
            'totalTime' => $totalTime,
            'filters' => $request->only(['start_date', 'end_date', 'project_id']),
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.time-entries', $data);
        $filename = 'extrato_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }
}
