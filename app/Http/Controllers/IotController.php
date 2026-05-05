<?php

namespace App\Http\Controllers;

use App\Models\Kolam;
use App\Models\KualitasAir;
use App\Models\JadwalPakan;
use App\Models\Pengurasan;
use App\Models\IotLog;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * IotController
 *
 * Menerima data dari perangkat IoT (ESP32) via HTTP REST API.
 * Semua endpoint tidak memerlukan autentikasi web, menggunakan API key saja.
 *
 * ESP32 mengirim data ke endpoint ini, dan controller akan:
 * 1. Memvalidasi device_id
 * 2. Menyimpan data ke database
 * 3. Mengirim notifikasi jika ada anomali
 */
class IotController extends Controller
{
    public function __construct(protected MqttService $mqttService) {}

    /**
     * Middleware: Verifikasi API key dari ESP32
     */
    protected function verifikasiDevice(Request $request): ?Kolam
    {
        $deviceId = $request->header('X-Device-ID') ?? $request->input('device_id');
        $apiKey   = $request->header('X-API-Key') ?? $request->input('api_key');

        if (!$deviceId) {
            return null;
        }

        // Untuk production: validasi API key dari database atau config
        $validKey = config('iot.api_key', 'aquasync-iot-secret-2026');
        if ($apiKey !== $validKey) {
            return null;
        }

        return Kolam::where('device_id', $deviceId)->first();
    }

    /**
     * Terima data sensor (pH, kekeruhan, tinggi air)
     * POST /api/iot/sensor
     *
     * Body: {
     *   "device_id": "ESP32-001",
     *   "api_key": "...",
     *   "ph": 7.2,
     *   "kekeruhan": 15.5,
     *   "tinggi_air": 80.0,
     *   "suhu": 28.5
     * }
     */
    public function terimaSensor(Request $request)
    {
        $kolam = $this->verifikasiDevice($request);
        if (!$kolam) {
            return response()->json(['success' => false, 'message' => 'Device tidak dikenal atau API key salah.'], 401);
        }

        $validated = $request->validate([
            'ph'         => 'nullable|numeric|min:0|max:14',
            'kekeruhan'  => 'nullable|numeric|min:0',
            'tinggi_air' => 'nullable|numeric|min:0',
            'suhu'       => 'nullable|numeric',
        ]);

        // Hapus null values
        $validated = array_filter($validated, fn($v) => !is_null($v));
        if (empty($validated)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data sensor yang valid.'], 422);
        }

        // Hitung status
        $statusPh        = isset($validated['ph']) ? KualitasAir::hitungStatusPh($validated['ph']) : 'normal';
        $statusKekeruhan = isset($validated['kekeruhan']) ? KualitasAir::hitungStatusKekeruhan($validated['kekeruhan']) : null;

        // Coba update record dalam 10 menit terakhir, atau buat baru
        $terkini = KualitasAir::byKolam($kolam->id)
            ->where('waktu_pengukuran', '>=', now()->subMinutes(10))
            ->latest('waktu_pengukuran')
            ->first();

        $dataUpdate = array_merge($validated, [
            'status_ph'       => $statusPh,
            'status_kekeruhan'=> $statusKekeruhan,
            'sumber'          => 'iot',
        ]);

        if ($terkini) {
            $terkini->update($dataUpdate);
            $kualitas = $terkini;
        } else {
            $kualitas = KualitasAir::create(array_merge($dataUpdate, [
                'kolam_id'         => $kolam->id,
                'ph'               => $validated['ph'] ?? 7.0,
                'waktu_pengukuran' => now(),
            ]));
        }

        // Catat log
        IotLog::create([
            'device_id'    => $request->header('X-Device-ID') ?? $request->input('device_id'),
            'kolam_id'     => $kolam->id,
            'topik'        => 'sensor/gabungan',
            'payload'      => $validated,
            'tipe'         => 'sensor',
            'diterima_pada'=> now(),
        ]);

        // Proses notifikasi jika ada anomali
        if (isset($validated['ph']) && $statusPh !== 'normal') {
            $this->mqttService->prosesPesan(
                "aquasync/{$kolam->device_id}/sensor/ph",
                json_encode(['ph' => $validated['ph']])
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data sensor berhasil disimpan.',
            'data'    => [
                'kolam'          => $kolam->nama_kolam,
                'ph'             => $kualitas->ph,
                'status_ph'      => $kualitas->status_ph,
                'kekeruhan'      => $kualitas->kekeruhan,
                'tinggi_air'     => $kualitas->tinggi_air,
                'suhu'           => $kualitas->suhu,
                'waktu'          => $kualitas->waktu_pengukuran->toISOString(),
            ],
        ]);
    }

    /**
     * Laporan status aktuator (pakan/pengurasan)
     * POST /api/iot/aktuator
     *
     * Body: {
     *   "device_id": "ESP32-001",
     *   "api_key": "...",
     *   "tipe": "pakan" | "pengurasan",
     *   "status": "mulai" | "selesai" | "gagal",
     *   "jadwal_id": 1,   // untuk pakan
     *   "volume": 150.0,  // untuk pengurasan
     *   "alasan": "terjadwal"
     * }
     */
    public function terimaAktuator(Request $request)
    {
        $kolam = $this->verifikasiDevice($request);
        if (!$kolam) {
            return response()->json(['success' => false, 'message' => 'Device tidak dikenal.'], 401);
        }

        $validated = $request->validate([
            'tipe'      => 'required|in:pakan,pengurasan',
            'status'    => 'required|in:mulai,selesai,gagal',
            'jadwal_id' => 'nullable|integer|exists:jadwal_pakan,id',
            'volume'    => 'nullable|numeric|min:0',
            'alasan'    => 'nullable|string',
        ]);

        if ($validated['tipe'] === 'pakan') {
            $this->prosesAktuatorPakan($kolam, $validated);
        } else {
            $this->prosesAktuatorPengurasan($kolam, $validated);
        }

        IotLog::create([
            'device_id'    => $request->header('X-Device-ID') ?? $request->input('device_id'),
            'kolam_id'     => $kolam->id,
            'topik'        => "aktuator/{$validated['tipe']}",
            'payload'      => $validated,
            'tipe'         => 'aktuator',
            'diterima_pada'=> now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status aktuator berhasil diproses.']);
    }

    protected function prosesAktuatorPakan(Kolam $kolam, array $data): void
    {
        if (!isset($data['jadwal_id']) || $data['status'] !== 'selesai') return;

        $jadwal = JadwalPakan::find($data['jadwal_id']);
        if ($jadwal) {
            $jadwal->update([
                'sudah_diberikan'     => true,
                'terakhir_dijalankan' => now(),
            ]);
            Log::info("IoT: Pakan selesai diberikan - Jadwal #{$jadwal->id} - Kolam {$kolam->nama_kolam}");
        }
    }

    protected function prosesAktuatorPengurasan(Kolam $kolam, array $data): void
    {
        if ($data['status'] === 'mulai') {
            Pengurasan::create([
                'kolam_id'   => $kolam->id,
                'jenis'      => 'otomatis',
                'alasan'     => $data['alasan'] ?? 'terjadwal',
                'waktu_mulai'=> now(),
                'status'     => 'berlangsung',
            ]);
        } elseif (in_array($data['status'], ['selesai', 'gagal'])) {
            $aktif = Pengurasan::where('kolam_id', $kolam->id)->berlangsung()->latest()->first();
            if ($aktif) {
                $aktif->update([
                    'status'        => $data['status'],
                    'waktu_selesai' => now(),
                    'volume_dikuras'=> $data['volume'] ?? null,
                ]);
            }
        }
    }

    /**
     * Ambil jadwal pakan aktif untuk device (ESP32 polling)
     * GET /api/iot/jadwal/{device_id}
     */
    public function getJadwal(Request $request, string $deviceId)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');
        if ($apiKey !== config('iot.api_key', 'aquasync-iot-secret-2026')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $kolam = Kolam::where('device_id', $deviceId)->first();
        if (!$kolam) {
            return response()->json(['success' => false, 'message' => 'Device tidak ditemukan.'], 404);
        }

        $jadwal = JadwalPakan::byKolam($kolam->id ?? 0)
            ->aktif()
            ->get(['id', 'nama_jadwal', 'waktu_pakan', 'jumlah_pakan', 'jenis_pakan', 'hari_aktif'])
            ->map(fn($j) => [
                'id'          => $j->id,
                'nama'        => $j->nama_jadwal,
                'waktu'       => $j->waktu_pakan,
                'jumlah_kg'   => $j->jumlah_pakan,
                'jenis'       => $j->jenis_pakan,
                'hari_aktif'  => $j->hari_aktif ?? ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'],
            ]);

        return response()->json([
            'success'     => true,
            'kolam'       => $kolam->nama_kolam,
            'device_id'   => $deviceId,
            'jadwal'      => $jadwal,
            'server_time' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Health check endpoint untuk ESP32
     * GET /api/iot/ping
     */
    public function ping()
    {
        return response()->json([
            'success'     => true,
            'message'     => 'AquaSync IoT Server OK',
            'server_time' => now()->toISOString(),
            'version'     => '1.0',
        ]);
    }
}