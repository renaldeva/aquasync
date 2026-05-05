<?php

namespace App\Http\Controllers\Owner;
 
use App\Http\Controllers\Controller;
use App\Models\{Kolam, KualitasAir, JadwalPakan, Pengurasan, Panen};
use Illuminate\Http\Request;
 
class MonitoringController extends Controller
{
    // GET /owner/monitoring/kualitas-air
    public function kualitasAir()
    {
        $kolam = Kolam::with('latestKualitasAir')->where('status', 'aktif')->get();
        return view('owner.monitoring.kualitas-air', compact('kolam'));
    }
 
    // GET /owner/monitoring/kualitas-air/{kolam}
    public function detailAir(Request $request, Kolam $kolam)
    {
        $hours = (int) $request->get('hours', 24);
        $data  = KualitasAir::where('kolam_id', $kolam->id)
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at')->get();
 
        if ($request->wantsJson()) return response()->json($data);
 
        return view('owner.monitoring.detail-air', compact('kolam', 'data', 'hours'));
    }
 
    // GET /owner/monitoring/jadwal
    public function jadwal()
    {
        $jadwalPakan = JadwalPakan::with('kolam')->where('status', 'aktif')->latest()->get();
        return view('owner.monitoring.jadwal', compact('jadwalPakan'));
    }
 
    // GET /owner/monitoring/pengurasan
    public function pengurasan()
    {
        $pengurasan = Pengurasan::with('kolam')->latest()->paginate(15);
        return view('owner.monitoring.pengurasan', compact('pengurasan'));
    }
 
    // GET /owner/monitoring/panen
    public function panen()
    {
        $jadwalPanen = Panen::with('kolam','hasilPanen')->latest()->paginate(10);
        return view('owner.monitoring.panen', compact('jadwalPanen'));
    }
}