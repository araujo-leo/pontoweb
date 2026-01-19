<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()
                    ->with('timeEntries')
                    ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $request->user()->projects()->create($validated);

        return redirect()->back();
    }
}
