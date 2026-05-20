<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttService;
use App\Models\MqttLog;

class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Start MQTT Subscriber';

    public function __construct(private MqttService $mqttService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('START MQTT...');

        if (!$this->mqttService->connect()) {
            $this->error('GAGAL CONNECT');
            return self::FAILURE;
        }

        $this->info('CONNECTED');
        $this->info('SUBSCRIBE...');
        $this->mqttService->subscribeAll();

        $this->info('LISTENING...');

        // Catat waktu heartbeat terakhir
        $lastHeartbeat = time();

        // Kirim heartbeat pertama langsung
        $this->sendHeartbeat();

        // Loop manual agar bisa kirim heartbeat berkala
        while (true) {
            try {

                // Proses pesan MQTT (non-blocking, 1 detik timeout)
                $this->mqttService->loopOnce();

                // Kirim heartbeat setiap 60 detik
                if (time() - $lastHeartbeat >= 60) {
                    $this->sendHeartbeat();
                    $lastHeartbeat = time();
                }

            } catch (\Throwable $e) {

                $this->error('LOOP ERROR: ' . $e->getMessage());

                // Coba reconnect
                $this->info('Mencoba reconnect...');
                sleep(5);

                if ($this->mqttService->reconnect()) {
                    $this->info('RECONNECTED');
                    $this->mqttService->subscribeAll();
                    $lastHeartbeat = time();
                    $this->sendHeartbeat();
                } else {
                    $this->error('RECONNECT GAGAL, coba lagi 10 detik...');
                    sleep(10);
                }
            }
        }

        return self::SUCCESS;
    }

    private function sendHeartbeat(): void
    {
        MqttLog::create([
            'topic'      => 'system/heartbeat',
            'payload'    => ['status' => 'alive', 'time' => now()->toDateTimeString()],
            'direction'  => 'incoming',
            'qos'        => 0,
            'processed'  => true,
            'device_id'  => 'laravel-server',
            'created_at' => now(),
        ]);

        $this->line('[' . now()->format('H:i:s') . '] Heartbeat sent');
    }
}