<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientLogController;
use App\Http\Controllers\Admin\ExportController;

// ─────────────────────────────────────────────────────────────────────────────
// Root redirect
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('logbook.index'));

// ─────────────────────────────────────────────────────────────────────────────
// Public: Client Visit Logbook Form
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
Route::get('/logbook/success', [LogbookController::class, 'success'])->name('logbook.success');

// ─────────────────────────────────────────────────────────────────────────────
// Authentication
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
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

    // Delete a single record
    Route::delete('/logs/{clientLog}', [ClientLogController::class, 'destroy'])->name('logs.destroy');

    // CSV Export (applies same filters as current dashboard view)
    Route::get('/export/csv', [ExportController::class, 'exportCsv'])->name('export.csv');
});
