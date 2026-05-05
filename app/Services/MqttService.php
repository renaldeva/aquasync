<?php

namespace App\Services;

use App\Models\IotDevice;
use App\Models\KualitasAir;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    private ?MqttClient $mqtt = null;

    /*
    |--------------------------------------------------------------------------
    | CONNECT
    |--------------------------------------------------------------------------
    */
    public function connect(): bool
    {
        try {
            $this->mqtt = new MqttClient(
                env('MQTT_HOST'),
                (int) env('MQTT_PORT', 8883),
                env('MQTT_CLIENT_ID', 'laravel-client-' . uniqid())
            );

            $settings = (new ConnectionSettings)
                ->setUsername(env('MQTT_USERNAME'))
                ->setPassword(env('MQTT_PASSWORD'))
                ->setUseTls(true)
                ->setTlsSelfSignedAllowed(true)
                ->setTlsVerifyPeer(false)
                ->setKeepAliveInterval(60);

            $this->mqtt->connect($settings);

            Log::info('MQTT CONNECTED');

            return true;

        } catch (\Throwable $e) {
            Log::error('MQTT CONNECT ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUBSCRIBE
    |--------------------------------------------------------------------------
    */
    public function subscribeAll(): void
    {
        $this->mqtt->subscribe('aquasync/+/sensor/+', function ($topic, $message) {
            $this->prosesPesan($topic, $message);
        }, 0);

        Log::info('SUBSCRIBE: aquasync/+/sensor/+');
    }

    /*
    |--------------------------------------------------------------------------
    | LOOP
    |--------------------------------------------------------------------------
    */
    public function loop(): void
    {
        $this->mqtt->loop(true);
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS MESSAGE
    |--------------------------------------------------------------------------
    */
    public function prosesPesan(string $topic, string $payload): void
    {
        try {
            Log::info("MQTT MASUK: {$topic} => {$payload}");

            $parts = explode('/', $topic);

            if (count($parts) < 4) return;

            $deviceId = $parts[1];
            $sensor   = $parts[3];

            $data = json_decode($payload, true);
            if (!$data) return;

            /*
            |--------------------------------------------------------------------------
            | AMBIL DEVICE
            |--------------------------------------------------------------------------
            */
            $device = IotDevice::where('device_id', $deviceId)->first();

            if (!$device) {
                Log::warning("Device tidak ditemukan: {$deviceId}");
                return;
            }

            $kolam = $device->kolam;

            if (!$kolam) {
                Log::warning("Kolam tidak ditemukan untuk device: {$deviceId}");
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | RECORD TERBARU (WINDOW 5 MENIT)
            |--------------------------------------------------------------------------
            */
            $record = KualitasAir::where('kolam_id', $kolam->id)
                ->where('recorded_at', '>=', now()->subMinutes(5))
                ->latest('recorded_at')
                ->first();

            if (!$record) {
                $record = KualitasAir::create([
                    'kolam_id'    => $kolam->id,
                    'device_id'   => $deviceId,
                    'recorded_at' => now(),
                    'ph_value'    => 7.0, // default aman
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | HANDLE SENSOR
            |--------------------------------------------------------------------------
            */
            match ($sensor) {
                'ph' => $this->handlePh($record, $data),
                'kekeruhan' => $this->handleTurbidity($record, $data),
                'tinggi_air' => $this->handleWaterLevel($record, $data),
                default => Log::info("Sensor tidak dikenali: {$sensor}")
            };

        } catch (\Throwable $e) {
            Log::error('MQTT ERROR: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SENSOR HANDLER
    |--------------------------------------------------------------------------
    */

    private function handlePh(KualitasAir $record, array $data): void
    {
        if (!isset($data['ph'])) return;

        $ph = (float) $data['ph'];

        if ($ph < 0 || $ph > 14) return;

        $record->update([
            'ph_value'  => $ph,
            'status_ph' => $this->getStatusPh($ph),
        ]);
    }

    private function handleTurbidity(KualitasAir $record, array $data): void
    {
        if (!isset($data['ntu'])) return;

        $ntu = (float) $data['ntu'];

        $record->update([
            'turbidity_value'  => $ntu,
            'status_turbidity' => $this->getStatusTurbidity($ntu),
        ]);
    }

    private function handleWaterLevel(KualitasAir $record, array $data): void
    {
        if (!isset($data['tinggi_cm'])) return;

        $record->update([
            'water_level' => (float) $data['tinggi_cm'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS LOGIC
    |--------------------------------------------------------------------------
    */

    private function getStatusPh(float $ph): string
    {
        return match (true) {
            $ph < 5.0 => 'kritis',
            $ph < 6.5 => 'asam',
            $ph > 9.0 => 'kritis',
            $ph > 8.5 => 'basa',
            default   => 'normal',
        };
    }

    private function getStatusTurbidity(float $ntu): string
    {
        return match (true) {
            $ntu < 25 => 'jernih',
            $ntu < 50 => 'keruh',
            default   => 'sangat_keruh',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISH (FIX ERROR KAMU)
    |--------------------------------------------------------------------------
    */
    public function publish(string $deviceId, string $type, array $payload): void
    {
        if (!$this->mqtt) {
            Log::error('MQTT BELUM CONNECT');
            return;
        }

        $topic = "aquasync/{$deviceId}/cmd/{$type}";

        $this->mqtt->publish($topic, json_encode($payload), 0);

        Log::info("MQTT PUBLISH: {$topic}");
    }
}