<?php

namespace App\Http\Controllers\Owner;
 
use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
 
class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())->latest('created_at')->paginate(15);
        return view('owner.notifikasi.index', compact('notifikasi'));
    }
 
    public function markRead(int $id)
    {
        $notif = Notifikasi::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $notif->markAsRead();
        return response()->json(['success' => true]);
    }
 
    public function readAll()
    {
        Notifikasi::where('user_id', auth()->id())->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
 
    public function count()
    {
        return response()->json([
            'count' => Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count(),
        ]);
    }
}