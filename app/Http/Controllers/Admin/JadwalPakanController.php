<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPakan;
use App\Models\Kolam;
use App\Services\MqttService;
use Illuminate\Http\Request;

class JadwalPakanController extends Controller
{
    protected MqttService $mqtt;

    public function __construct(MqttService $mqtt)
    {
        $this->mqtt = $mqtt;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $jadwal = JadwalPakan::with([
            'kolam',
            'creator'
        ])
        ->latest()
        ->paginate(10);

        $kolam = Kolam::where('status', 'aktif')->get();

        return view(
            'admin.jadwal-pakan.index',
            compact('jadwal', 'kolam')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kolam_id'     => 'required|exists:kolam,id',
            'nama_jadwal'  => 'nullable|string|max:100',
            'waktu_pakan'  => 'required|date_format:H:i',
            'jumlah_pakan' => 'nullable|numeric|min:0',
            'satuan'       => 'nullable|in:gram,kg',
            'jenis_pakan'  => 'nullable|string|max:100',
            'frekuensi'    => 'nullable|in:harian,mingguan,custom',
        ]);

        $validated['created_by'] = auth()->id();

        $validated['hari_aktif'] = [
            'senin',
            'selasa',
            'rabu',
            'kamis',
            'jumat',
            'sabtu',
            'minggu'
        ];

        $validated['status'] = 'aktif';

        JadwalPakan::create($validated);

        return redirect()
            ->route('admin.jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, JadwalPakan $jadwal)
    {
        $validated = $request->validate([
            'nama_jadwal'  => 'nullable|string|max:100',
            'waktu_pakan'  => 'required|date_format:H:i',
            'jumlah_pakan' => 'nullable|numeric|min:0',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        $jadwal->update($validated);

        return redirect()
            ->route('admin.jadwal-pakan.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(JadwalPakan $jadwal)
    {
        $jadwal->update([
            'status' => 'nonaktif'
        ]);

        return redirect()
            ->route('admin.jadwal-pakan.index')
            ->with('success', 'Jadwal berhasil dinonaktifkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | EXECUTE MQTT
    |--------------------------------------------------------------------------
    */
    public function execute(JadwalPakan $jadwal)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | LOAD RELATION
            |--------------------------------------------------------------------------
            */
            $jadwal->load('kolam.iotDevice');

            /*
            |--------------------------------------------------------------------------
            | VALIDASI KOLAM
            |--------------------------------------------------------------------------
            */
            if (!$jadwal->kolam) {

                return back()->with(
                    'error',
                    'Kolam tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DEVICE
            |--------------------------------------------------------------------------
            */
            if (!$jadwal->kolam->iotDevice) {

                return back()->with(
                    'error',
                    'Device IoT belum terhubung.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DEVICE ID
            |--------------------------------------------------------------------------
            */
            $deviceId = $jadwal->kolam->iotDevice->device_id;

            /*
            |--------------------------------------------------------------------------
            | PAYLOAD MQTT
            |--------------------------------------------------------------------------
            */
            $payload = [
                'status'        => 'ON',
                'jadwal_id'     => $jadwal->id,
                'nama_jadwal'   => $jadwal->nama_jadwal,
                'jumlah_pakan'  => $jadwal->jumlah_pakan,
                'satuan'        => $jadwal->satuan,
                'jenis_pakan'   => $jadwal->jenis_pakan,
                'waktu_pakan'   => $jadwal->waktu_pakan,
                'timestamp'     => now()->toDateTimeString(),
            ];

            /*
            |--------------------------------------------------------------------------
            | KIRIM MQTT
            |--------------------------------------------------------------------------
            */
            $ok = $this->mqtt->publish(
                $deviceId,
                'pakan',
                $payload
            );

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */
            return back()->with(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Perintah pakan berhasil dikirim ke IoT.'
                    : 'Gagal mengirim perintah MQTT.'
            );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }
}