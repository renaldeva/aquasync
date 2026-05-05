<?php

namespace App\Http\Controllers\Owner;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
 
class AkunController extends Controller
{
    public function index()
    {
        return view('owner.akun.index', ['user' => auth()->user()]);
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