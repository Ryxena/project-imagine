<?php

use App\Http\Controllers\admin\ManajemenKamarController;
use App\Http\Controllers\admin\ManajemenKeluhanController;
use App\Http\Controllers\admin\ManajemenPenghuniController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

    Route::post('penghuni', [ManajemenPenghuniController::class, 'store'])->name('penghuni.store');
