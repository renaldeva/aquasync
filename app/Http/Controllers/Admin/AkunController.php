<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
 
class AkunController extends Controller
{
    public function index()
    {
        return view('admin.akun.index', ['user' => auth()->user()]);
    }
 
    public function updateProfil(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);
        auth()->user()->update($validated);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
 
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attr, $val, $fail) {
                if (!Hash::check($val, auth()->user()->password)) $fail('Password lama salah.');
            }],
            'password' => 'required|min:8|confirmed',
        ]);
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }
}