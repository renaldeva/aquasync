<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\KomentarFlag;
use App\Observers\KomentarFlagObserver;
use App\Services\MqttService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Daftarkan MqttService sebagai singleton
        $this->app->singleton(MqttService::class);
    }

    public function boot(): void
    {
        // Paksa semua URL menggunakan HTTPS di production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Observer komentar
        KomentarFlag::observe(KomentarFlagObserver::class);
    }
}