<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    AkunController,
    KolamController,
    KualitasAirController,
    JadwalPakanController,
    PengurasanController,
    PanenController,
    LaporanController,
    KomentarFlagController,
    NotifikasiController,
};

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Akun
        Route::get('/akun',              [AkunController::class, 'index'])          ->name('akun.index');
        Route::patch('/akun/profil',     [AkunController::class, 'updateProfil'])   ->name('akun.profil');
        Route::patch('/akun/password',   [AkunController::class, 'updatePassword']) ->name('akun.password');

        // Kolam
        Route::get('/kolam',             [KolamController::class, 'index'])         ->name('kolam.index');
        Route::post('/kolam',            [KolamController::class, 'store'])         ->name('kolam.store');
        Route::patch('/kolam/{kolam}',   [KolamController::class, 'update'])        ->name('kolam.update');
        Route::delete('/kolam/{kolam}',  [KolamController::class, 'destroy'])       ->name('kolam.destroy');

        // Kualitas Air
        Route::get('/kualitas-air',              [KualitasAirController::class, 'index'])  ->name('kualitas-air.index');
        Route::get('/kualitas-air/{kolam}',      [KualitasAirController::class, 'show'])   ->name('kualitas-air.show');
        Route::get('/kualitas-air/{kolam}/json', [KualitasAirController::class, 'json'])   ->name('kualitas-air.json');

        // Jadwal Pakan
        Route::get('/jadwal-pakan',                    [JadwalPakanController::class, 'index'])  ->name('jadwal-pakan.index');
        Route::post('/jadwal-pakan',                   [JadwalPakanController::class, 'store'])  ->name('jadwal-pakan.store');
        Route::patch('/jadwal-pakan/{jadwal}',         [JadwalPakanController::class, 'update']) ->name('jadwal-pakan.update');
        Route::delete('/jadwal-pakan/{jadwal}',        [JadwalPakanController::class, 'destroy'])->name('jadwal-pakan.destroy');
        Route::post('/jadwal-pakan/{jadwal}/execute',  [JadwalPakanController::class, 'execute'])->name('jadwal-pakan.execute');
        Route::post('/jadwal-pakan/manual-feed',       [JadwalPakanController::class, 'manualFeed'])->name('jadwal-pakan.manual-feed');

        // Pengurasan Air
        Route::get('/pengurasan',                [PengurasanController::class, 'index'])  ->name('pengurasan.index');
        Route::post('/pengurasan/{kolam}/start', [PengurasanController::class, 'start'])  ->name('pengurasan.start');
        Route::post('/pengurasan/{kolam}/stop',  [PengurasanController::class, 'stop'])   ->name('pengurasan.stop');

        // Panen
        Route::get('/panen',              [PanenController::class, 'index'])         ->name('panen.index');
        Route::post('/panen/jadwal',      [PanenController::class, 'storeJadwal'])   ->name('panen.jadwal.store');
        Route::patch('/panen/jadwal/{jadwal}', [PanenController::class, 'updateJadwal'])->name('panen.jadwal.update');
        Route::delete('/panen/jadwal/{jadwal}',[PanenController::class, 'destroyJadwal'])->name('panen.jadwal.destroy');
        Route::post('/panen/hasil',       [PanenController::class, 'storeHasil'])    ->name('panen.hasil.store');

        // Laporan
        Route::get('/laporan',              [LaporanController::class, 'index'])    ->name('laporan.index');
        Route::post('/laporan/generate',    [LaporanController::class, 'generate']) ->name('laporan.generate');
        Route::get('/laporan/{laporan}',    [LaporanController::class, 'show'])     ->name('laporan.show');
        Route::get('/laporan/{laporan}/download', [LaporanController::class, 'download'])->name('laporan.download');

        // Komentar & Flag
        Route::get('/komentar-flag',                      [KomentarFlagController::class, 'index'])       ->name('komentar-flag.index');
        Route::post('/komentar-flag/{flag}/balas',        [KomentarFlagController::class, 'balas'])       ->name('komentar-flag.balas');
        Route::patch('/komentar-flag/{flag}/status',      [KomentarFlagController::class, 'updateStatus'])->name('komentar-flag.status');

        // Notifikasi
        Route::get('/notifikasi',              [NotifikasiController::class, 'index'])     ->name('notifikasi.index');
        Route::post('/notifikasi/{id}/read',   [NotifikasiController::class, 'markRead'])  ->name('notifikasi.read');
        Route::post('/notifikasi/read-all',    [NotifikasiController::class, 'readAll'])   ->name('notifikasi.read-all');
        Route::get('/notifikasi/count',        [NotifikasiController::class, 'count'])     ->name('notifikasi.count');
    });