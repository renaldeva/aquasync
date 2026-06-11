<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmController;

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/owner.php';

Route::middleware('auth')->group(function () {

    Route::post('/fcm/token', [FcmController::class, 'store'])
        ->name('fcm.token');

});

Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
        };
    }

    return redirect()->route('login');
});