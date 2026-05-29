<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::post('/update-status', [DashboardController::class, 'updateStatus'])->name('update-status');
    Route::get('/api/division-chart', [DashboardController::class, 'getDivisionChartData'])->name('api.division-chart');
    Route::get('/api/status-chart', [DashboardController::class, 'getStatusChartData'])->name('api.status-chart');
    Route::get('/belum-ditindak-count', [DashboardController::class, 'getBelumDitindakCount'])->name('belum-ditindak-count');
    Route::get('/belum-ditindak', [DashboardController::class, 'belumDitindak'])->name('belum-ditindak');
    Route::get('/api/belum-ditindak/{id}', [DashboardController::class, 'showBelumDitindak'])->name('api.belum-ditindak.show');
    Route::post('/belum-ditindak/{id}/reminder', [DashboardController::class, 'sendReminder'])->name('belum-ditindak.reminder');
    Route::post('/belum-ditindak/tindak', [DashboardController::class, 'tindakSurat'])->name('belum-ditindak.tindak');
    Route::get('/dashboard/export/excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');
    Route::get('/api/system-status', [DashboardController::class, 'getRealtimeSystemStatus'])->name('api.system-status');
});
Route::get('/api/performance-summary', [DashboardController::class, 'getPerformanceSummary'])->name('api.performance-summary');

// Surat Masuk routes
Route::middleware(['auth'])->group(function () {
    Route::resource('surat-masuk', SuratMasukController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('/api/surat-masuk/{id}', [SuratMasukController::class, 'show'])->name('api.surat-masuk.show');
    Route::post('/surat-masuk/{id}/update-status', [SuratMasukController::class, 'updateStatus'])->name('surat-masuk.update-status');
    Route::get('/welcome', [SuratMasukController::class, 'welcome'])->name('welcome');
});

// Surat Keluar routes
Route::middleware(['auth'])->group(function () {
    Route::resource('surat-keluar', SuratKeluarController::class)->except(['create', 'edit']);
});

// Arsip resource routes
Route::middleware(['auth'])->group(function () {
    Route::resource('arsip', ArsipController::class);
});

// User import route
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');

// User resource routes
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});

// Laporan resource routes
Route::middleware(['auth'])->group(function () {
    Route::resource('laporan', LaporanController::class);
    Route::post('/laporan/storeManual', [LaporanController::class, 'storeManual'])->name('laporan.storeManual');
});

require __DIR__.'/auth.php';
