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

            $attachments = collect($entry->attachments ?? [])->map(function ($attachment, $index) use ($entry) {
                return [
                    'filename' => $attachment['filename'],
                    'uploaded_at' => $attachment['uploaded_at'],
                    'index' => $index,
                    'download_url' => route('time-entries.download-attachment', ['timeEntry' => $entry->id, 'index' => $index]),
                ];
            })->toArray();

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
                'activity_type' => $entry->activity_type ?? 'development',
                'attachments' => $attachments,
                'manually_edited' => $entry->manually_edited ?? false,
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

        $activitySummaries = $processedEntries->groupBy('activity_type')->map(function ($group, $type) {
            $totalMinutes = $group->sum('duration_minutes');
            $h = floor($totalMinutes / 60);
            $m = $totalMinutes % 60;

            $labels = [
                'development' => 'Development',
                'maintenance' => 'Maintenance',
                'meeting' => 'Meeting',
                'research' => 'Research',
                'documentation' => 'Documentation',
                'review' => 'Review',
                'support' => 'Support',
                'planning' => 'Planning'
            ];

            return [
                'type' => $type,
                'label' => $labels[$type] ?? 'Development',
                'total_earnings' => $group->sum('earnings'),
                'total_time' => sprintf('%dh %02dm', $h, $m),
                'count' => $group->count(),
            ];
        })->values();

        $projects = Project::where('user_id', auth()->id())
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return \Inertia\Inertia::render('TimeEntries/Index', [
            'entries' => $processedEntries,
            'projectSummaries' => $projectSummaries,
            'activitySummaries' => $activitySummaries,
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
            'activity_type' => 'required|in:development,maintenance,meeting,research,documentation,review,support,planning',
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
            'activity_type' => $request->activity_type,
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
                'Tipo',
                'Hora Inicial',
                'Hora Final',
                'Duração',
                'Descrição',
                'Anexos',
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

                $activityTypeLabels = [
                    'development' => 'Development',
                    'maintenance' => 'Maintenance',
                    'meeting' => 'Meeting',
                    'research' => 'Research',
                    'documentation' => 'Documentation',
                    'review' => 'Review',
                    'support' => 'Support',
                    'planning' => 'Planning'
                ];
                $activityType = $activityTypeLabels[$entry->activity_type ?? 'development'] ?? 'Development';

                $attachments = $entry->attachments ?? [];
                $attachmentText = '';
                if (count($attachments) > 0) {
                    $attachmentNames = array_map(function($att) {
                        return $att['filename'] ?? 'attachment';
                    }, $attachments);
                    $attachmentText = implode(', ', $attachmentNames);
                }

                fputcsv($file, [
                    $start->format('d/m/Y'),
                    $entry->project->name,
                    $activityType,
                    $start->format('H:i'),
                    $end ? $end->format('H:i') : '...',
                    $duration,
                    $entry->description ?? '',
                    $attachmentText,
                    number_format($entry->project->hourly_rate, 2, ',', '.'),
                    $end ? number_format($earnings, 2, ',', '.') : '-',
                ], ';');
            }

            if ($entries->count() > 0) {
                $totalHours = floor($totalMinutes / 60);
                $totalMins = $totalMinutes % 60;
                $totalDuration = sprintf('%02dh %02dm', $totalHours, $totalMins);

                fputcsv($file, [], ';');

                // Total row
                fputcsv($file, [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $totalDuration,
                    'TOTAL GERAL',
                    '',
                    number_format($totalEarnings, 2, ',', '.')
                ], ';');

                $activityGroups = $entries->groupBy('activity_type');
                if ($activityGroups->count() > 1) {
                    fputcsv($file, [], ';');

                    fputcsv($file, ['RESUMO POR TIPO DE ATIVIDADE'], ';');
                    fputcsv($file, [], ';');

                    $activityLabels = [
                        'development' => 'Development',
                        'maintenance' => 'Maintenance',
                        'meeting' => 'Meeting',
                        'research' => 'Research',
                        'documentation' => 'Documentation',
                        'review' => 'Review',
                        'support' => 'Support',
                        'planning' => 'Planning'
                    ];

                    foreach ($activityGroups as $type => $group) {
                        $activityMinutes = 0;
                        $activityEarnings = 0;

                        foreach ($group as $entry) {
                            $start = Carbon::parse($entry->start_time);
                            $end = $entry->end_time ? Carbon::parse($entry->end_time) : null;

                            if ($end) {
                                $minutes = abs($end->diffInMinutes($start));
                                $activityMinutes += $minutes;
                                $activityEarnings += ($minutes / 60) * $entry->project->hourly_rate;
                            }
                        }

                        $activityHours = floor($activityMinutes / 60);
                        $activityMins = $activityMinutes % 60;
                        $activityDuration = sprintf('%02dh %02dm', $activityHours, $activityMins);

                        $label = $activityLabels[$type] ?? 'Development';
                        fputcsv($file, [
                            '',
                            '',
                            $label,
                            '',
                            '',
                            $activityDuration,
                            $group->count() . ' registro(s)',
                            '',
                            number_format($activityEarnings, 2, ',', '.')
                        ], ';');
                    }
                }
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
                'activity_type' => $entry->activity_type ?? 'development',
                'start_time' => $start->format('H:i'),
                'end_time' => $end ? $end->format('H:i') : '...',
                'duration_formatted' => $end ? $end->diff($start)->format('%H:%I:%S') : '-',
                'duration_minutes' => $durationInMinutes,
                'earnings' => $earnings,
                'is_active' => is_null($entry->end_time),
                'description' => $entry->description,
                'attachments' => $entry->attachments ?? [],
                'hourly_rate' => $entry->project->hourly_rate,
            ];
        });

        $totalEarnings = $processedEntries->sum('earnings');
        $totalMinutes = $processedEntries->sum('duration_minutes');
        $totalHours = floor($totalMinutes / 60);
        $totalMins = $totalMinutes % 60;
        $totalTime = sprintf('%dh %02dm', $totalHours, $totalMins);

        // Activity type summaries
        $activitySummaries = $processedEntries->groupBy('activity_type')->map(function ($group, $type) {
            $totalMinutes = $group->sum('duration_minutes');
            $h = floor($totalMinutes / 60);
            $m = $totalMinutes % 60;

            $labels = [
                'development' => 'Development',
                'maintenance' => 'Maintenance',
                'meeting' => 'Meeting',
                'research' => 'Research',
                'documentation' => 'Documentation',
                'review' => 'Review',
                'support' => 'Support',
                'planning' => 'Planning'
            ];

            return [
                'type' => $type,
                'label' => $labels[$type] ?? 'Development',
                'total_earnings' => $group->sum('earnings'),
                'total_time' => sprintf('%dh %02dm', $h, $m),
                'count' => $group->count(),
            ];
        })->values();

        $data = [
            'entries' => $processedEntries,
            'totalEarnings' => $totalEarnings,
            'totalTime' => $totalTime,
            'activitySummaries' => $activitySummaries,
            'filters' => $request->only(['start_date', 'end_date', 'project_id']),
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.time-entries', $data);
        $filename = 'extrato_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function uploadAttachment(Request $request, TimeEntry $timeEntry)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
        ]);

        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store in private storage (not publicly accessible)
        $path = $file->storeAs('private/attachments', $filename, 'local');

        $attachments = $timeEntry->attachments ?? [];
        $attachments[] = [
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'uploaded_at' => now()->toISOString(),
        ];

        $timeEntry->attachments = $attachments;
        $timeEntry->save();

        return redirect()->back()->with('success', 'Anexo adicionado com sucesso!');
    }

    public function deleteAttachment(Request $request, TimeEntry $timeEntry)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $attachments = $timeEntry->attachments ?? [];

        if (isset($attachments[$request->index])) {
            // Delete from private storage
            \Storage::disk('local')->delete($attachments[$request->index]['path']);

            array_splice($attachments, $request->index, 1);

            $timeEntry->attachments = array_values($attachments);
            $timeEntry->save();
        }

        return redirect()->back()->with('success', 'Anexo removido com sucesso!');
    }

    public function manualEdit(Request $request, TimeEntry $timeEntry)
    {
        $request->validate([
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
            'description' => 'nullable|string|max:1000',
            'activity_type' => 'required|in:development,maintenance,meeting,research,documentation,review,support,planning',
        ]);

        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $timeEntry->start_time = $request->start_time;
        $timeEntry->end_time = $request->end_time;
        $timeEntry->description = $request->description;
        $timeEntry->activity_type = $request->activity_type;
        $timeEntry->manually_edited = true;
        $timeEntry->save();

        return redirect()->back()->with('success', 'Registro atualizado com sucesso!');
    }

    public function downloadAttachment(TimeEntry $timeEntry, $index)
    {
        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $attachments = $timeEntry->attachments ?? [];

        if (!isset($attachments[$index])) {
            abort(404, 'Anexo não encontrado');
        }

        $attachment = $attachments[$index];
        $path = $attachment['path'];

        if (!\Storage::disk('local')->exists($path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return \Storage::disk('local')->download($path, $attachment['filename']);
    }
}
