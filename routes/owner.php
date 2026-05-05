<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Owner\{
    DashboardController,
    AkunController,
    MonitoringController,
    LaporanController,
    KomentarController,
    NotifikasiController,
};

Route::prefix('owner')
    ->name('owner.')
    ->middleware(['auth', 'role:owner'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Akun ────────────────────────────────────────────────────────────
        Route::get('/akun',                              [AkunController::class, 'index'])               ->name('akun.index');
        Route::patch('/akun/password',                   [AkunController::class, 'updatePassword'])      ->name('akun.password');
        Route::patch('/akun/admin/{user}/password',      [AkunController::class, 'updatePasswordAdmin']) ->name('akun.admin.password');

        // ── Monitoring ──────────────────────────────────────────────────────
        Route::get('/monitoring/kualitas-air',         [MonitoringController::class, 'kualitasAir'])  ->name('monitoring.kualitas-air');
        Route::get('/monitoring/kualitas-air/{kolam}', [MonitoringController::class, 'detailAir'])    ->name('monitoring.kualitas-air.detail');
        Route::get('/monitoring/jadwal',               [MonitoringController::class, 'jadwal'])       ->name('monitoring.jadwal');
        Route::get('/monitoring/pengurasan',           [MonitoringController::class, 'pengurasan'])   ->name('monitoring.pengurasan');
        Route::get('/monitoring/panen',                [MonitoringController::class, 'panen'])        ->name('monitoring.panen');

        // ── Laporan (read-only) ──────────────────────────────────────────────
        Route::get('/laporan',                    [LaporanController::class, 'index'])   ->name('laporan.index');
        Route::get('/laporan/{laporan}',          [LaporanController::class, 'show'])    ->name('laporan.show');
        Route::get('/laporan/{laporan}/download', [LaporanController::class, 'download'])->name('laporan.download');

        // ── Komentar & Flag ──────────────────────────────────────────────────
        Route::get('/komentar',        [KomentarController::class, 'index'])->name('komentar.index');
        Route::post('/komentar',       [KomentarController::class, 'store'])->name('komentar.store');
        Route::get('/komentar/{flag}', [KomentarController::class, 'show']) ->name('komentar.show');

        // ── Notifikasi ───────────────────────────────────────────────────────
        Route::get('/notifikasi',            [NotifikasiController::class, 'index'])   ->name('notifikasi.index');
        Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all',  [NotifikasiController::class, 'readAll']) ->name('notifikasi.read-all');
        Route::get('/notifikasi/count',      [NotifikasiController::class, 'count'])   ->name('notifikasi.count');
    });