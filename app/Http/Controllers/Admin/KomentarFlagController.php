<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\{KomentarFlag, Notifikasi};
use Illuminate\Http\Request;
 
class KomentarFlagController extends Controller
{
    public function index()
    {
        $flags = KomentarFlag::with('user','penjawab')->latest()->paginate(10);
        return view('admin.komentar-flag.index', compact('flags'));
    }
 
    public function balas(Request $request, KomentarFlag $flag)
    {
        $request->validate(['isi_balasan' => 'required|string|max:1000']);
 
        $flag->update([
            'isi_balasan'  => $request->isi_balasan,
            'dibalas_oleh' => auth()->id(),
            'dibalas_at'   => now(),
            'status'       => 'dibalas',
        ]);
 
        Notifikasi::kirim(
            $flag->user_id,
            'Admin membalas komentar Anda',
            $request->isi_balasan,
            'info',
            'komentar_flag',
            $flag->id
        );
 
        return back()->with('success', 'Balasan berhasil dikirim.');
    }
 
    public function updateStatus(Request $request, KomentarFlag $flag)
    {
        $request->validate(['status' => 'required|in:menunggu,dibalas,selesai,ditolak']);
        $flag->update(['status' => $request->status]);
        return back()->with('success', 'Status diperbarui.');
    }
}