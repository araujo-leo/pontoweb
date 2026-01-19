<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            });

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $period = $request->input('period', 'this_month');

        switch ($period) {
            case 'today':
                $query->whereDate('start_time', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('start_time', now()->month)
                    ->whereYear('start_time', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('start_time', now()->subMonth()->month)
                    ->whereYear('start_time', now()->subMonth()->year);
                break;
        }

        $entries = $query->orderBy('start_time', 'desc')->get();

        $totalEarnings = 0;
        $totalMinutes = 0;
        $projectsActiveCount = $entries->pluck('project_id')->unique()->count();

        foreach ($entries as $entry) {
            if ($entry->end_time) {
                $start = Carbon::parse($entry->start_time);
                $end = Carbon::parse($entry->end_time);

                $minutes = abs($end->diffInMinutes($start));
                $totalMinutes += $minutes;
                $totalEarnings += ($minutes / 60) * $entry->project->hourly_rate;
            }
        }

        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;
        $totalTimeFormatted = sprintf('%dh %02dm', $h, $m);

        $recentActivity = $entries->take(5)->map(function ($entry) {
            $start = Carbon::parse($entry->start_time);
            $end = $entry->end_time ? Carbon::parse($entry->end_time) : null;
            $earnings = 0;
            $duration = 'Em andamento';

            if ($end) {
                $mins = abs($end->diffInMinutes($start));
                $earnings = ($mins / 60) * $entry->project->hourly_rate;
                $duration = $end->diff($start)->format('%H:%I');
            }

            return [
                'id' => $entry->id,
                'project_name' => $entry->project->name,
                'date' => $start->format('d/m'),
                'duration' => $duration,
                'earnings' => $earnings,
            ];
        });

        $chartData = $this->getChartData($period);

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalEarnings' => $totalEarnings,
                'totalTime' => $totalTimeFormatted,
                'activeProjects' => $projectsActiveCount,
                'entriesCount' => $entries->count(),
            ],
            'recentActivity' => $recentActivity,
            'chartData' => $chartData,
            'filters' => $request->only(['project_id', 'period']),
            'projects' => Project::where('user_id', auth()->id())->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function getChartData($period)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $entries = TimeEntry::with('project')
            ->whereHas('project', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->whereNotNull('end_time')
            ->get();

        $weeks = [];
        for ($i = 0; $i < 4; $i++) {
            $weekStart = $startOfMonth->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();

            $weekEarnings = $entries->filter(function ($entry) use ($weekStart, $weekEnd) {
                $entryDate = Carbon::parse($entry->start_time);
                return $entryDate->between($weekStart, $weekEnd);
            })->sum(function ($entry) {
                $start = Carbon::parse($entry->start_time);
                $end = Carbon::parse($entry->end_time);
                $minutes = abs($end->diffInMinutes($start));
                return ($minutes / 60) * $entry->project->hourly_rate;
            });

            $weeks[] = round($weekEarnings, 2);
        }

        return [
            'labels' => ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
            'values' => $weeks,
        ];
    }
}
