<?php

namespace App\Services;

use App\Models\IotDevice;
use App\Models\KualitasAir;
use App\Models\MqttLog;
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
            if ($this->mqtt !== null) {
                return true;
            }

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
    | RECONNECT
    |--------------------------------------------------------------------------
    */
    public function reconnect(): bool
    {
        $this->mqtt = null;
        return $this->connect();
    }

    /*
    |--------------------------------------------------------------------------
    | DISCONNECT
    |--------------------------------------------------------------------------
    */
    public function disconnect(): void
    {
        try {
            if ($this->mqtt !== null) {
                $this->mqtt->disconnect();
                $this->mqtt = null;
                Log::info('MQTT DISCONNECTED');
            }
        } catch (\Throwable $e) {
            Log::error('MQTT DISCONNECT ERROR: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUBSCRIBE ALL
    |
    | Topic ESP32 firmware v3.0:
    |   aquasync/kolam/{kolam_id}/sensor/ph
    |   aquasync/kolam/{kolam_id}/sensor/turbidity
    |   aquasync/kolam/{kolam_id}/sensor/level
    |   aquasync/kolam/{kolam_id}/pakan/status
    |   aquasync/device/{device_id}/ping
    |   aquasync/device/{device_id}/status
    |--------------------------------------------------------------------------
    */
    public function subscribeAll(): void
    {
        if (!$this->mqtt) {
            Log::error('MQTT BELUM CONNECT');
            return;
        }

        // Subscribe data sensor
        $this->mqtt->subscribe(
            'aquasync/kolam/+/sensor/+',
            function (string $topic, string $message) {
                $this->prosesSensor($topic, $message);
            },
            0
        );
        Log::info('SUBSCRIBE: aquasync/kolam/+/sensor/+');

        // Subscribe ping dari device
        $this->mqtt->subscribe(
            'aquasync/device/+/ping',
            function (string $topic, string $message) {
                $this->prosesPing($topic, $message);
            },
            0
        );
        Log::info('SUBSCRIBE: aquasync/device/+/ping');

        // Subscribe status device (LWT)
        $this->mqtt->subscribe(
            'aquasync/device/+/status',
            function (string $topic, string $message) {
                $this->prosesStatusDevice($topic, $message);
            },
            0
        );
        Log::info('SUBSCRIBE: aquasync/device/+/status');

        // Subscribe status pakan dari ESP32
        $this->mqtt->subscribe(
            'aquasync/kolam/+/pakan/status',
            function (string $topic, string $message) {
                $this->prosesPakanStatus($topic, $message);
            },
            0
        );
        Log::info('SUBSCRIBE: aquasync/kolam/+/pakan/status');
    }

    /*
    |--------------------------------------------------------------------------
    | LOOP ONCE — non-blocking
    | php-mqtt/client v2.x: loop(bool $allowSleep, bool $exitWhenQueuesEmpty)
    | Kita pakai allowSleep=false agar tidak block, lalu usleep manual
    |--------------------------------------------------------------------------
    */
    public function loopOnce(): void
    {
        if (!$this->mqtt) {
            return;
        }

        // allowSleep=false → non-blocking, langsung return setelah proses antrian
        $this->mqtt->loop(false, true);
        usleep(100000); // 100ms jeda
    }

    /*
    |--------------------------------------------------------------------------
    | LOOP — blocking
    |--------------------------------------------------------------------------
    */
    public function loop(): void
    {
        if (!$this->mqtt) {
            return;
        }

        $this->mqtt->loop(true);
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES SENSOR
    | Topic: aquasync/kolam/{kolam_id}/sensor/{tipe}
    | Payload: {"device_id":"ESP32-KLM001","kolam_id":1,"ph":7.2,...}
    |--------------------------------------------------------------------------
    */
    public function prosesSensor(string $topic, string $payload): void
    {
        $parts    = explode('/', $topic); // [aquasync,kolam,{id},sensor,{tipe}]
        $kolamId  = $parts[2] ?? null;
        $tipe     = $parts[4] ?? null;
        $data     = json_decode($payload, true);
        $deviceId = $data['device_id'] ?? null;

        MqttLog::create([
            'topic'      => $topic,
            'payload'    => $data ?? ['raw' => $payload],
            'direction'  => 'incoming',
            'qos'        => 0,
            'processed'  => false,
            'device_id'  => $deviceId,
            'created_at' => now(),
        ]);

        try {
            Log::info("MQTT SENSOR: {$topic} => {$payload}");

            if (!$data || !$kolamId || !$tipe) {
                return;
            }

            // Cari device
            $device = $deviceId
                ? IotDevice::where('device_id', $deviceId)->first()
                : null;

            if (!$device) {
                $device = IotDevice::whereHas('kolam', function ($q) use ($kolamId) {
                    $q->where('id', $kolamId);
                })->first();
            }

            if (!$device) {
                Log::warning("Device tidak ditemukan untuk topic: {$topic}");
                return;
            }

            // Update last_ping & status dari data sensor
            $device->update([
                'last_ping'        => now(),
                'status'           => 'online',
                'ip_address'       => $data['ip']       ?? $device->ip_address,
                'firmware_version' => $data['firmware'] ?? $device->firmware_version,
            ]);

            $kolam = $device->kolam;
            if (!$kolam) {
                Log::warning("Kolam tidak ditemukan untuk device: {$device->device_id}");
                return;
            }

            // Ambil atau buat record kualitas air (window 5 menit)
            $record = KualitasAir::where('kolam_id', $kolam->id)
                ->where('recorded_at', '>=', now()->subMinutes(5))
                ->latest('recorded_at')
                ->first();

            if (!$record) {
                $record = KualitasAir::create([
                    'kolam_id'    => $kolam->id,
                    'device_id'   => $device->device_id,
                    'recorded_at' => now(),
                ]);
            }

            match ($tipe) {
                'ph'        => $this->handlePh($record, $data),
                'turbidity' => $this->handleTurbidity($record, $data),
                'level'     => $this->handleWaterLevel($record, $data),
                default     => Log::info("Sensor tidak dikenali: {$tipe}")
            };

            MqttLog::where('topic', $topic)
                ->where('direction', 'incoming')
                ->latest('created_at')
                ->first()
                ?->update(['processed' => true]);

        } catch (\Throwable $e) {
            Log::error('MQTT SENSOR ERROR: ' . $e->getMessage());
            MqttLog::where('topic', $topic)
                ->where('direction', 'incoming')
                ->latest('created_at')
                ->first()
                ?->update([
                    'processed'     => false,
                    'error_message' => $e->getMessage(),
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES PING
    | Topic: aquasync/device/{device_id}/ping
    | Payload: {"device_id":"ESP32-KLM001","ip":"x.x.x.x","firmware":"3.0.0",...}
    |--------------------------------------------------------------------------
    */
    public function prosesPing(string $topic, string $payload): void
    {
        $parts    = explode('/', $topic); // [aquasync,device,{device_id},ping]
        $deviceId = $parts[2] ?? null;
        $data     = json_decode($payload, true);

        MqttLog::create([
            'topic'      => $topic,
            'payload'    => $data ?? ['raw' => $payload],
            'direction'  => 'incoming',
            'qos'        => 0,
            'processed'  => true,
            'device_id'  => $deviceId,
            'created_at' => now(),
        ]);

        if (!$deviceId) {
            return;
        }

        try {
            $updated = IotDevice::where('device_id', $deviceId)->update([
                'last_ping'        => now(),
                'status'           => 'online',
                'ip_address'       => $data['ip']       ?? null,
                'firmware_version' => $data['firmware'] ?? null,
            ]);

            if (!$updated) {
                // Daftarkan device baru otomatis
                IotDevice::create([
                    'device_id'        => $deviceId,
                    'nama_device'      => 'ESP32 ' . $deviceId,
                    'last_ping'        => now(),
                    'status'           => 'online',
                    'ip_address'       => $data['ip']       ?? null,
                    'firmware_version' => $data['firmware'] ?? null,
                ]);
                Log::info("Device baru terdaftar: {$deviceId}");
            }

            Log::info("PING dari {$deviceId} — online");

        } catch (\Throwable $e) {
            Log::error('MQTT PING ERROR: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES STATUS DEVICE (LWT)
    | Topic: aquasync/device/{device_id}/status
    | Payload: {"status":"offline","device_id":"ESP32-KLM001"}
    |--------------------------------------------------------------------------
    */
    public function prosesStatusDevice(string $topic, string $payload): void
    {
        $data     = json_decode($payload, true);
        $deviceId = $data['device_id'] ?? explode('/', $topic)[2] ?? null;
        $status   = $data['status']    ?? 'offline';

        if (!$deviceId) {
            return;
        }

        try {
            IotDevice::where('device_id', $deviceId)
                ->update(['status' => $status]);

            Log::info("STATUS device {$deviceId}: {$status}");

        } catch (\Throwable $e) {
            Log::error('MQTT STATUS ERROR: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES STATUS PAKAN
    | Topic: aquasync/kolam/{kolam_id}/pakan/status
    | Payload: {"device_id":"...","jadwal_id":1,"jumlah":100,"status":"sukses"}
    |--------------------------------------------------------------------------
    */
    public function prosesPakanStatus(string $topic, string $payload): void
    {
        $data = json_decode($payload, true);

        MqttLog::create([
            'topic'      => $topic,
            'payload'    => $data ?? ['raw' => $payload],
            'direction'  => 'incoming',
            'qos'        => 0,
            'processed'  => true,
            'device_id'  => $data['device_id'] ?? null,
            'created_at' => now(),
        ]);

        try {
            if (!$data) {
                return;
            }

            $jadwalId = $data['jadwal_id'] ?? null;
            $status   = $data['status']    ?? 'sukses';

            if ($jadwalId) {
                \App\Models\RiwayatPakan::where('jadwal_id', $jadwalId)
                    ->latest('waktu_eksekusi')
                    ->first()
                    ?->update(['status' => $status]);
            }

            Log::info("PAKAN status: jadwal_id={$jadwalId} status={$status}");

        } catch (\Throwable $e) {
            Log::error('MQTT PAKAN STATUS ERROR: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE PH
    |--------------------------------------------------------------------------
    */
    private function handlePh(KualitasAir $record, array $data): void
    {
        if (!isset($data['ph'])) {
            return;
        }

        $ph = (float) $data['ph'];

        if ($ph < 0 || $ph > 14) {
            return;
        }

        $record->update([
            'ph_value'  => $ph,
            'status_ph' => $this->getStatusPh($ph),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE TURBIDITY
    |--------------------------------------------------------------------------
    */
    private function handleTurbidity(KualitasAir $record, array $data): void
    {
        $ntu = $data['turbidity'] ?? $data['ntu'] ?? null;

        if ($ntu === null) {
            return;
        }

        $ntu = (float) $ntu;

        $record->update([
            'turbidity_value'  => $ntu,
            'status_turbidity' => $this->getStatusTurbidity($ntu),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE WATER LEVEL
    |--------------------------------------------------------------------------
    */
    private function handleWaterLevel(KualitasAir $record, array $data): void
    {
        $level = $data['level'] ?? $data['tinggi_cm'] ?? null;

        if ($level === null) {
            return;
        }

        $record->update([
            'water_level' => (float) $level,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS PH
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

    /*
    |--------------------------------------------------------------------------
    | STATUS TURBIDITY
    |--------------------------------------------------------------------------
    */
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
    | PUBLISH
    | Topic ke ESP32: aquasync/kolam/{kolam_id}/{type}/command
    |--------------------------------------------------------------------------
    */
    public function publish(string $deviceId, string $type, array $payload): bool
    {
        try {
            if (!$this->connect()) {
                return false;
            }

            $device  = IotDevice::where('device_id', $deviceId)->first();
            $kolamId = $device?->kolam?->id ?? 1;

            $topic = "aquasync/kolam/{$kolamId}/{$type}/command";

            $this->mqtt->publish($topic, json_encode($payload), 0);

            Log::info("MQTT PUBLISH: {$topic}");

            MqttLog::create([
                'topic'      => $topic,
                'payload'    => $payload,
                'direction'  => 'outgoing',
                'qos'        => 0,
                'processed'  => true,
                'device_id'  => $deviceId,
                'created_at' => now(),
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error('MQTT PUBLISH ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISH PAKAN COMMAND
    | Topic: aquasync/kolam/{kolam_id}/pakan/command
    | Payload: {"action":"feed","jumlah":100,"jadwal_id":1}
    |--------------------------------------------------------------------------
    */
    public function publishPakanCommand(
        string $deviceId,
        string $status   = 'ON',
        float  $jumlah   = 100,
        int    $jadwalId = 0
    ): bool {
        return $this->publish(
            $deviceId,
            'pakan',
            [
                'action'    => 'feed',
                'jumlah'    => $jumlah,
                'jadwal_id' => $jadwalId,
                'time'      => now()->toDateTimeString(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISH KURAS COMMAND
    | Topic: aquasync/kolam/{kolam_id}/pengurasan/command
    | Payload: {"action":"start"} atau {"action":"stop"}
    |--------------------------------------------------------------------------
    */
    public function publishKurasCommand(string $deviceId, string $action = 'start'): bool
    {
        return $this->publish(
            $deviceId,
            'pengurasan',
            [
                'action' => $action,
                'time'   => now()->toDateTimeString(),
            ]
        );
    }
}