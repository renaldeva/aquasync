<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $admins = User::where('role', 'admin')
                      ->orderBy('name')
                      ->get();

        return view('owner.akun.index', compact('user', 'admins'));
    }

    // ── Ganti password milik Owner sendiri ──────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                function ($attr, $val, $fail) {
                    if (!Hash::check($val, auth()->user()->password)) {
                        $fail('Password saat ini tidak sesuai.');
                    }
                },
            ],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password Anda berhasil diubah.');
    }

    // ── Reset password milik Admin (hak akses Owner) ─────────────────────────
    public function updatePasswordAdmin(Request $request, User $user)
    {
        // Pastikan target adalah akun dengan role admin
        if ($user->role !== 'admin') {
            abort(403, 'Hanya password akun Admin yang dapat direset.');
        }

        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password admin {$user->name} berhasil direset.");
    }
}