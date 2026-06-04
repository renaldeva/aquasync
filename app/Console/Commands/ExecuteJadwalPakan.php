<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalPakan;
use App\Services\MqttService;
use Carbon\Carbon;

class ExecuteJadwalPakan extends Command
{
    protected $signature = 'pakan:execute';

    protected $description = 'Eksekusi jadwal pakan otomatis';

    public function handle(MqttService $mqtt)
    {
        $now = Carbon::now();

        $jamSekarang = $now->format('H:i');

        $hariMap = [
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
            'Sunday'    => 'minggu',
        ];

        $hariIni = $hariMap[$now->format('l')];

        $jadwalList = JadwalPakan::with('kolam.iotDevice')
            ->where('status', 'aktif')
            ->whereTime('waktu_pakan', $jamSekarang)
            ->get();

        $this->info("Jam sekarang: " . $jamSekarang);
        $this->info("Jumlah jadwal: " . $jadwalList->count());

        foreach ($jadwalList as $jadwal) {

            $this->info("Jadwal ID: " . $jadwal->id);

            // Hindari double execute dalam menit yang sama
            if (
                $jadwal->last_executed_at &&
                $jadwal->last_executed_at->format('Y-m-d H:i')
                    === now()->format('Y-m-d H:i')
            ) {
                $this->warn("SKIP SUDAH DIEKSEKUSI MENIT INI");
                continue;
            }

            // Validasi hari untuk jadwal mingguan
            if (
                $jadwal->frekuensi === 'mingguan' &&
                !in_array($hariIni, $jadwal->hari_aktif ?? [])
            ) {
                $this->warn("SKIP HARI TIDAK COCOK");
                continue;
            }

            // Validasi kolam
            if (!$jadwal->kolam) {
                $this->warn("SKIP KOLOM TIDAK DITEMUKAN");
                continue;
            }

            // Validasi device
            if (!$jadwal->kolam->iotDevice) {
                $this->warn("SKIP DEVICE TIDAK ADA");
                continue;
            }

            $deviceId = $jadwal->kolam->iotDevice->device_id;

            $this->info("Device: " . $deviceId);

            $result = $mqtt->publishPakanCommand(
                $deviceId,
                'ON',
                (float) ($jadwal->jumlah_pakan ?? 100),
                $jadwal->id
            );

            $this->info(
                "Publish MQTT: " .
                ($result ? 'BERHASIL' : 'GAGAL')
            );

            if ($result) {
                $jadwal->update([
                    'last_executed_at' => now()
                ]);

                $this->info(
                    "Eksekusi jadwal ID: " .
                    $jadwal->id
                );
            }
        }

        return Command::SUCCESS;
    }
}