<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');


    Route::post('/time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::patch('/time-entries/{timeEntry}', [TimeEntryController::class, 'update'])->name('time-entries.update');
    Route::patch('/time-entries/{timeEntry}/description', [TimeEntryController::class, 'updateDescription'])->name('time-entries.update-description');
    Route::post('/time-entries/{timeEntry}/attachment', [TimeEntryController::class, 'uploadAttachment'])->name('time-entries.upload-attachment');
    Route::delete('/time-entries/{timeEntry}/attachment', [TimeEntryController::class, 'deleteAttachment'])->name('time-entries.delete-attachment');
    Route::get('/time-entries/{timeEntry}/attachment/{index}', [TimeEntryController::class, 'downloadAttachment'])->name('time-entries.download-attachment');
    Route::patch('/time-entries/{timeEntry}/manual-edit', [TimeEntryController::class, 'manualEdit'])->name('time-entries.manual-edit');
    Route::get('/time-report', [TimeEntryController::class, 'index'])->name('time-entries.index');
    Route::get('/time-report/export-csv', [TimeEntryController::class, 'exportCsv'])->name('time-entries.export-csv');
    Route::get('/time-report/export-pdf', [TimeEntryController::class, 'exportPdf'])->name('time-entries.export-pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
