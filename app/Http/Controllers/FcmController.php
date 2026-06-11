<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string']
        ]);

        FcmToken::firstOrCreate(
            [
                'token' => $request->token
            ],
            [
                'user_id' => auth()->id()
            ]
        );

        return response()->json([
            'success' => true
        ]);
    }
}