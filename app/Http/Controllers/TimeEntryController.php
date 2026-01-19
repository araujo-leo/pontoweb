<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeEntryController extends Controller
{
    public function index()
    {
        // 1. Buscar dados brutos - apenas do usuário logado
        $rawEntries = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('start_time', 'desc')
            ->get();

        // 2. Processar cada entrada (calcular valores individuais)
        $processedEntries = $rawEntries->map(function ($entry) {
            $start = \Carbon\Carbon::parse($entry->start_time);
            $end = $entry->end_time ? \Carbon\Carbon::parse($entry->end_time) : null;

            $durationInMinutes = 0;
            $earnings = 0;

            if ($end) {
                // abs() garante que não tenhamos números negativos se a hora estiver invertida
                $durationInMinutes = abs($end->diffInMinutes($start));
                $earnings = ($durationInMinutes / 60) * $entry->project->hourly_rate;
            }

            return [
                'id' => $entry->id,
                'project_id' => $entry->project_id, // Importante para o agrupamento
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

        // 3. Criar o Resumo por Projeto (Agrupamento)
        $projectSummaries = $processedEntries->groupBy('project_id')->map(function ($group) {
            $totalMinutes = $group->sum('duration_minutes');

            // Formatar horas totais do projeto (ex: 12h 30m)
            $h = floor($totalMinutes / 60);
            $m = $totalMinutes % 60;

            return [
                'name' => $group->first()['project_name'],
                'total_earnings' => $group->sum('earnings'),
                'total_time' => sprintf('%dh %02dm', $h, $m),
                'count' => $group->count(),
                'hourly_rate' => $group->first()['hourly_rate']
            ];
        })->values(); // Remove as chaves de ID para ficar um array limpo pro Vue

        // 4. Totais Gerais
        $grandTotalEarnings = $processedEntries->sum('earnings');
        $grandTotalMinutes = $processedEntries->sum('duration_minutes');
        $grandHours = floor($grandTotalMinutes / 60);
        $grandMinutes = $grandTotalMinutes % 60;

        return \Inertia\Inertia::render('TimeEntries/Index', [
            'entries' => $processedEntries, // Lista detalhada
            'projectSummaries' => $projectSummaries, // Novo resumo por projeto
            'totalEarnings' => $grandTotalEarnings,
            'totalTime' => sprintf('%dh %02dm', $grandHours, $grandMinutes),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string|max:1000',
        ]);

        // Verificar se o projeto pertence ao usuário logado
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

        // Verificar se a entrada pertence a um projeto do usuário logado
        $timeEntry = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('id', $timeEntry->id)
            ->firstOrFail();

        $timeEntry->end_time = now();

        // Atualizar descrição se fornecida
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

        // Verificar se a entrada pertence a um projeto do usuário logado
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
}
