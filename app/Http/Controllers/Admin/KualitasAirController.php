<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\{Kolam, KualitasAir};
use Illuminate\Http\Request;
 
class KualitasAirController extends Controller
{
    public function index()
    {
        $kolam = Kolam::with('latestKualitasAir')->where('status', 'aktif')->get();
        return view('admin.kualitas-air.index', compact('kolam'));
    }
 
    public function show(Request $request, Kolam $kolam)
    {
        $hours = (int) $request->get('hours', 24);
        $data  = KualitasAir::where('kolam_id', $kolam->id)
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at')->get();
 
        return view('admin.kualitas-air.show', compact('kolam', 'data', 'hours'));
    }
 
    public function json(Request $request, Kolam $kolam)
    {
        $hours = (int) $request->get('hours', 24);
        $data  = KualitasAir::where('kolam_id', $kolam->id)
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at')
            ->get(['ph_value', 'turbidity_value', 'water_level', 'status_ph', 'recorded_at']);
        return response()->json($data);
    }
}