<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\{JadwalPakan, Kolam};
use App\Services\MqttService;
use Illuminate\Http\Request;
 
class JadwalPakanController extends Controller
{
    public function __construct(private MqttService $mqtt) {}
 
    public function index()
    {
        $jadwal = JadwalPakan::with('kolam', 'creator')->latest()->paginate(10);
        $kolam  = Kolam::where('status', 'aktif')->get();
        return view('admin.jadwal-pakan.index', compact('jadwal', 'kolam'));
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kolam_id'     => 'required|exists:kolam,id',
            'nama_jadwal'  => 'nullable|string|max:100',
            'waktu_pakan'  => 'required|date_format:H:i',
            'jumlah_pakan' => 'nullable|numeric|min:0',
            'satuan'       => 'in:gram,kg',
            'jenis_pakan'  => 'nullable|string',
            'frekuensi'    => 'in:harian,mingguan,custom',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['hari_aktif'] = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
        JadwalPakan::create($validated);
        return redirect()->route('admin.jadwal-pakan.index')->with('success', 'Jadwal pakan ditambahkan.');
    }
 
    public function update(Request $request, JadwalPakan $jadwal)
    {
        $validated = $request->validate([
            'nama_jadwal'  => 'nullable|string|max:100',
            'waktu_pakan'  => 'required|date_format:H:i',
            'jumlah_pakan' => 'nullable|numeric',
            'status'       => 'in:aktif,nonaktif',
        ]);
        $jadwal->update($validated);
        return redirect()->route('admin.jadwal-pakan.index')->with('success', 'Jadwal diperbarui.');
    }
 
    public function destroy(JadwalPakan $jadwal)
    {
        $jadwal->update(['status' => 'nonaktif']);
        return redirect()->route('admin.jadwal-pakan.index')->with('success', 'Jadwal dinonaktifkan.');
    }
 
    public function execute(JadwalPakan $jadwal)
    {
        $ok = $this->mqtt->publishPakanCommand($jadwal->kolam_id, $jadwal->toArray());
        return back()->with($ok ? 'success' : 'error', $ok ? 'Perintah pakan dikirim ke IoT.' : 'Gagal kirim perintah IoT.');
    }
}