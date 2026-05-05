<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Services\LaporanService;
use Illuminate\Http\Request;
 
class LaporanController extends Controller
{
    public function __construct(private LaporanService $laporanService) {}
 
    public function index()
    {
        $laporan = Laporan::with('kolam','pembuat')->latest()->paginate(10);
        return view('admin.laporan.index', compact('laporan'));
    }
 
    public function show(Laporan $laporan)
    {
        return view('admin.laporan.show', compact('laporan'));
    }
 
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'tipe'            => 'required|in:kualitas_air,pakan,pengurasan,panen,bulanan,mingguan',
            'periode_mulai'   => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'kolam_id'        => 'nullable|exists:kolam,id',
        ]);
        $laporan = $this->laporanService->generate($validated);
        return redirect()->route('admin.laporan.show', $laporan)->with('success', 'Laporan berhasil dibuat.');
    }
 
    public function download(Laporan $laporan)
    {
        // Implementasi dengan barryvdh/laravel-dompdf
        // $pdf = \PDF::loadView('admin.laporan.pdf', compact('laporan'));
        // return $pdf->download(Str::slug($laporan->judul) . '.pdf');
        return redirect()->route('admin.laporan.show', $laporan)
            ->with('error', 'Fitur download PDF sedang dalam pengembangan.');
    }
}