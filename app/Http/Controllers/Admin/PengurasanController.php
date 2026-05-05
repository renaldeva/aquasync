<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\{Pengurasan, Kolam};
use App\Services\MqttService;
 
class PengurasanController extends Controller
{
    public function __construct(private MqttService $mqtt) {}
 
    public function index()
    {
        $pengurasan = Pengurasan::with('kolam')->latest()->paginate(15);
        $kolam      = Kolam::where('status','aktif')->get();
        return view('admin.pengurasan.index', compact('pengurasan', 'kolam'));
    }
 
    public function start(Kolam $kolam)
    {
        $ok = $this->mqtt->publishPengurasanCommand($kolam->id, 'start');
        return back()->with($ok ? 'success' : 'error', $ok ? 'Pengurasan dimulai.' : 'Gagal kirim perintah.');
    }
 
    public function stop(Kolam $kolam)
    {
        $ok = $this->mqtt->publishPengurasanCommand($kolam->id, 'stop');
        return back()->with($ok ? 'success' : 'error', $ok ? 'Pengurasan dihentikan.' : 'Gagal kirim perintah.');
    }
}