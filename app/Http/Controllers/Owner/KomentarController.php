<?php

namespace App\Http\Controllers\Owner;
 
use App\Http\Controllers\Controller;
use App\Models\{KomentarFlag, Notifikasi, User};
use Illuminate\Http\Request;
 
class KomentarController extends Controller
{
    public function index()
    {
        $flags = KomentarFlag::with('penjawab')
            ->where('user_id', auth()->id())
            ->latest()->paginate(10);
        return view('owner.komentar.index', compact('flags'));
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_type'  => 'required|in:jadwal_pakan,jadwal_panen,pengurasan_air,kolam,laporan',
            'target_id'    => 'required|integer',
            'jenis'        => 'required|in:komentar,flag',
            'isi_komentar' => 'required|string|max:1000',
        ]);
        $validated['user_id'] = auth()->id();
 
        $flag = KomentarFlag::create($validated);
 
        // Notifikasi ke semua admin
        User::where('role', 'admin')->each(fn($admin) =>
            Notifikasi::kirim(
                $admin->id,
                ucfirst($flag->jenis) . ' baru dari ' . auth()->user()->name,
                $flag->isi_komentar,
                'flag',
                $flag->target_type,
                $flag->target_id
            )
        );
 
        return back()->with('success', ucfirst($validated['jenis']) . ' berhasil dikirim ke Admin.');
    }
 
    public function show(KomentarFlag $flag)
    {
        abort_if($flag->user_id !== auth()->id(), 403);
        return view('owner.komentar.show', compact('flag'));
    }
}