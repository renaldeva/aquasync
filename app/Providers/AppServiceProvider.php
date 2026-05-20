<?php
// =============================================
// app/Providers/AppServiceProvider.php
// =============================================
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\KomentarFlag;
use App\Observers\KomentarFlagObserver;
use App\Services\MqttService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Daftarkan MqttService sebagai singleton
        // agar koneksi MQTT tidak dibuat ulang tiap request
        $this->app->singleton(MqttService::class);
    }

    public function boot(): void
    {
        // Daftarkan observer — notifikasi otomatis saat flag dibuat/diupdate
        KomentarFlag::observe(KomentarFlagObserver::class);
    }
}