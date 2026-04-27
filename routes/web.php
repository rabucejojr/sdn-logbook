<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientLogController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\PendingController;

// ─────────────────────────────────────────────────────────────────────────────
// Root redirect
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('logbook.index'));

// ─────────────────────────────────────────────────────────────────────────────
// Public: Client Visit Logbook Form
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');

// BUG FIX: throttle:30,1 — max 30 submissions per minute per IP.
// Prevents automated spam submissions to the public form.
Route::post('/logbook', [LogbookController::class, 'store'])
    ->name('logbook.store')
    ->middleware('throttle:30,1');

Route::get('/logbook/success', [LogbookController::class, 'success'])->name('logbook.success');

// ─────────────────────────────────────────────────────────────────────────────
// Authentication
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // BUG FIX: throttle:5,1 — max 5 login attempts per minute per IP.
    // Prevents brute-force attacks on the admin account.
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─────────────────────────────────────────────────────────────────────────────
// Admin: Protected Routes (requires authentication)
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {

    // Dashboard (analytics + data table)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Print-friendly log view — must be defined BEFORE the parameterised route
    Route::get('/logs/print', [ClientLogController::class, 'printView'])->name('logs.print');

    // Edit / update a single record
    Route::get('/logs/{clientLog}/edit', [ClientLogController::class, 'edit'])->name('logs.edit');
    Route::put('/logs/{clientLog}', [ClientLogController::class, 'update'])->name('logs.update');

    // Delete a single record
    Route::delete('/logs/{clientLog}', [ClientLogController::class, 'destroy'])->name('logs.destroy');

    // CSV Export (applies same filters as current dashboard view)
    Route::get('/export/csv', [ExportController::class, 'exportCsv'])->name('export.csv');

    // Pending approvals
    Route::get('/pending', [PendingController::class, 'index'])->name('pending.index');
    Route::delete('/pending/{clientLog}/reject', [PendingController::class, 'reject'])->name('pending.reject');
});
