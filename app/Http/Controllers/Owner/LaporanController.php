<?php

namespace App\Http\Controllers\Owner;
 
use App\Http\Controllers\Controller;
use App\Models\Laporan;
 
class LaporanController extends Controller
{
    public function index()
    {
        $laporan = Laporan::with('kolam','pembuat')
            ->whereIn('status', ['published', 'approved'])
            ->latest()->paginate(10);
        return view('owner.laporan.index', compact('laporan'));
    }
 
    public function show(Laporan $laporan)
    {
        abort_if(!in_array($laporan->status, ['published','approved']), 403, 'Laporan belum tersedia.');
        return view('owner.laporan.show', compact('laporan'));
    }
 
    public function download(Laporan $laporan)
    {
        abort_if(!in_array($laporan->status, ['published','approved']), 403);
        return redirect()->route('owner.laporan.show', $laporan)
            ->with('error', 'Fitur download PDF sedang dalam pengembangan.');
    }
}