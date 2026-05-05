<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\{Panen, HasilPanen, Kolam};
use Illuminate\Http\Request;
 
class PanenController extends Controller
{
    public function index()
    {
        $jadwalPanen = Panen::with('kolam','creator','hasilPanen')->latest()->paginate(10);
        return view('admin.panen.index', compact('jadwalPanen'));
    }
 
    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'kolam_id'        => 'required|exists:kolam,id',
            'tanggal_rencana' => 'required|date|after:today',
            'estimasi_berat'  => 'nullable|numeric|min:0',
            'estimasi_jumlah' => 'nullable|integer|min:0',
            'catatan'         => 'nullable|string',
        ]);
        $validated['created_by'] = auth()->id();
        Panen::create($validated);
        return redirect()->route('admin.panen.index')->with('success', 'Jadwal panen ditambahkan.');
    }
 
    public function updateJadwal(Request $request, Panen $jadwal)
    {
        $validated = $request->validate([
            'tanggal_rencana' => 'required|date',
            'estimasi_berat'  => 'nullable|numeric',
            'estimasi_jumlah' => 'nullable|integer',
            'status'          => 'in:pending,approved,rejected,selesai,ditunda',
            'catatan'         => 'nullable|string',
        ]);
        $jadwal->update($validated);
        return redirect()->route('admin.panen.index')->with('success', 'Jadwal panen diperbarui.');
    }
 
    public function destroyJadwal(Panen $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.panen.index')->with('success', 'Jadwal panen dihapus.');
    }
 
    public function storeHasil(Request $request)
    {
        $validated = $request->validate([
            'jadwal_panen_id' => 'required|exists:jadwal_panen,id',
            'kolam_id'        => 'required|exists:kolam,id',
            'tanggal_panen'   => 'required|date',
            'total_berat'     => 'required|numeric|min:0',
            'total_jumlah'    => 'nullable|integer|min:0',
            'harga_per_kg'    => 'nullable|numeric|min:0',
            'kualitas'        => 'in:baik,sedang,buruk',
            'catatan'         => 'nullable|string',
        ]);
        $validated['created_by'] = auth()->id();
 
        if (isset($validated['total_jumlah']) && $validated['total_jumlah'] > 0) {
            $validated['rata_rata_berat'] = $validated['total_berat'] / $validated['total_jumlah'];
        }
        if (isset($validated['harga_per_kg'])) {
            $validated['total_nilai'] = $validated['total_berat'] * $validated['harga_per_kg'];
        }
 
        HasilPanen::create($validated);
        Panen::find($validated['jadwal_panen_id'])?->update(['status' => 'selesai']);
 
        return redirect()->route('admin.panen.index')->with('success', 'Hasil panen dicatat.');
    }
}