<?php

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminProfileController;
use App\Http\Controllers\admin\ManajemenKamarController;
use App\Http\Controllers\admin\ManajemenKeluhanController;
use App\Http\Controllers\admin\ManajemenPenghuniController;
use App\Http\Controllers\admin\ManajemenPengumumanController;
use App\Http\Controllers\admin\ManajemenUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\penghuni\PenghuniDashboardController;
use App\Http\Controllers\penghuni\PenghuniKeluhanController;
use App\Http\Controllers\penghuni\PenghuniPengumumanController;
use App\Http\Controllers\penghuni\PenghuniProfileController;
use Illuminate\Support\Facades\Route;




Route::get('/login', function () {return view('auth.UserLogin');})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth')->name('me');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin'])->group(function () {
    //dashboard route buat landingpage admin
    // Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    //profile
    Route::get('profile', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

    // Penghuni
    Route::get('penghuni', [ManajemenPenghuniController::class, 'index'])->name('penghuni.index');
    Route::post('penghuni', [ManajemenPenghuniController::class, 'store'])->name('penghuni.store');
    Route::get('penghuni/daftarkamar', [ManajemenPenghuniController::class, 'showKamar'])->name('penghuni.daftarkamar');
    // Route::get('penghuni/{penghunian}', [ManajemenPenghuniController::class, 'show'])->name('penghuni.show');
    Route::put('penghuni/{penghunian}', [ManajemenPenghuniController::class, 'update'])->name('penghuni.update');
    Route::delete('penghuni/{penghunian}', [ManajemenPenghuniController::class, 'destroy'])->name('penghuni.destroy');
    Route::put('penghuni/assignkamar/{penghunian}', [ManajemenPenghuniController::class, 'assignKamar'])->name('penghuni.assignkamar');
    Route::put('penghuni/checkoutkamar/{penghunian}', [ManajemenPenghuniController::class, 'checkoutKamar'])->name('penghuni.checkoutkamar');

    // Kamar
    Route::get('kamar', [ManajemenKamarController::class, 'index'])->name('kamar.index');
    Route::post('kamar', [ManajemenKamarController::class, 'store'])->name('kamar.store');
    Route::put('kamar/{kamar}', [ManajemenKamarController::class, 'update'])->name('kamar.update');
    Route::delete('kamar/{kamar}', [ManajemenKamarController::class, 'destroy'])->name('kamar.destroy');

    // Keluhan
    Route::get('keluhan', [ManajemenKeluhanController::class, 'index'])->name('keluhan.index');
    Route::put('keluhan/{keluhan}', [ManajemenKeluhanController::class, 'update'])->name('keluhan.update');

    //pengumuman
    Route::get('pengumuman', [\App\Http\Controllers\admin\ManajemenPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('pengumuman', [\App\Http\Controllers\admin\ManajemenPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::get('pengumuman/{pengumuman}', [\App\Http\Controllers\admin\ManajemenPengumumanController::class, 'show'])->name('pengumuman.show');
    Route::put('pengumuman/{pengumuman}', [\App\Http\Controllers\admin\ManajemenPengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('pengumuman/{pengumuman}', [\App\Http\Controllers\admin\ManajemenPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
});

Route::prefix('penghuni')->name('penghuni.')->middleware(['auth', 'role:user'])->group(function () {
    //dashboard route buat landingpage penghuni
    // Route::get('/', [PenghuniDashboardController::class, 'index'])->name('dashboard');

    //profile
    Route::get('profile',[PenghuniProfileController::class, 'show'])->name('profile.show');
    Route::put('profile',[PenghuniProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile',[PenghuniProfileController::class, 'destroy'])->name('profile.destroy');   
    
    //keluhan
    Route::get('keluhan',[PenghuniKeluhanController::class, 'index'])->name('keluhan.index');
    Route::post('keluhan',[PenghuniKeluhanController::class, 'store'])->name('keluhan.store');
    Route::get('keluhan/{keluhan}',[PenghuniKeluhanController::class, 'show'])->name('keluhan.show');
    Route::put('keluhan/{keluhan}',[PenghuniKeluhanController::class, 'update'])->name('keluhan.update');
    Route::delete('keluhan/{keluhan}',[PenghuniKeluhanController::class, 'destroy'])->name('keluhan.destroy');

    //pengumuman
    Route::get('pengumuman', [PenghuniPengumumanController::class, 'index'])->name('pengumuman.index');
});