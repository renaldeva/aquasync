<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Kolam;
use App\Services\KolamService;
use Illuminate\Http\Request;
 
class KolamController extends Controller
{
    public function __construct(private KolamService $kolamService) {}
 
    public function index()
    {
        $kolam = Kolam::with('latestKualitasAir', 'iotDevice')
        ->latest()
        ->paginate(15);
        return view('admin.kolam.index', compact('kolam'));
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kolam'      => 'required|string|max:100',
            'jenis'           => 'required|in:pembesaran,pendederan,induk',
            'kapasitas_liter' => 'nullable|numeric|min:0',
            'jumlah_ikan'     => 'nullable|integer|min:0',
            'tanggal_tebar'   => 'nullable|date',
            'keterangan'      => 'nullable|string',
        ]);
        $this->kolamService->store($validated);
        return redirect()->route('admin.kolam.index')->with('success', 'Kolam berhasil ditambahkan.');
    }
 
    public function update(Request $request, Kolam $kolam)
    {
        $validated = $request->validate([
            'nama_kolam'      => 'required|string|max:100',
            'jenis'           => 'required|in:pembesaran,pendederan,induk',
            'kapasitas_liter' => 'nullable|numeric',
            'jumlah_ikan'     => 'nullable|integer',
            'tanggal_tebar'   => 'nullable|date',
            'status'          => 'required|in:aktif,kosong,maintenance',
            'keterangan'      => 'nullable|string',
        ]);
        $this->kolamService->update($kolam, $validated);
        return redirect()->route('admin.kolam.index')->with('success', 'Data kolam diperbarui.');
    }
 
    public function destroy(Kolam $kolam)
    {
        $this->kolamService->nonaktifkan($kolam);
        return redirect()->route('admin.kolam.index')->with('success', 'Kolam dinonaktifkan.');
    }
}