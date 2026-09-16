<?php

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminProfileController;
use App\Http\Controllers\admin\ManajemenKamarController;
use App\Http\Controllers\admin\ManajemenKeluhanController;
use App\Http\Controllers\admin\ManajemenPenghuniController;
use App\Http\Controllers\admin\ManajemenPengumumanController;
use App\Http\Controllers\admin\ManajemenTagihanController;
use App\Http\Controllers\admin\VerifikasiPembayaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\penghuni\PenghuniDashboardController;
use App\Http\Controllers\penghuni\PenghuniKeluhanController;
use App\Http\Controllers\penghuni\PenghuniPembayaraanController;
use App\Http\Controllers\penghuni\PenghuniPengumumanController;
use App\Http\Controllers\penghuni\PenghuniProfileController;
use App\Http\Controllers\penghuni\PenghuniTagihanController;
use App\Http\Controllers\UserNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.UserLogin');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth')->name('me');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin'])->group(function () {
    // dashboard route buat landingpage admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // profile
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

    // Tagihan
    Route::get('tagihan', [ManajemenTagihanController::class, 'index'])->name('tagihan.index');
    Route::post('tagihan', [ManajemenTagihanController::class, 'store'])->name('tagihan.store');
    Route::get('tagihan/daftarpenghuni', [ManajemenTagihanController::class, 'showPenghuni'])->name('tagihan.daftarpenghuni');
    Route::get('tagihan/{tagihan}', [ManajemenTagihanController::class, 'show'])->name('tagihan.show');
    Route::put('tagihan/{tagihan}', [ManajemenTagihanController::class, 'update'])->name('tagihan.update');
    Route::delete('tagihan/{tagihan}', [ManajemenTagihanController::class, 'destroy'])->name('tagihan.destroy');

    // Verifikasi Pembayaran
    Route::get('verifikasipembayaran', [VerifikasiPembayaranController::class, 'index'])->name('verifikasipembayaran.index');
    Route::get('verifikasipembayaran/{pembayaran}', [VerifikasiPembayaranController::class, 'show'])->name('verifikasipembayaran.show');
    Route::put('verifikasipembayaran/{pembayaran}', [VerifikasiPembayaranController::class, 'update'])->name('verifikasipembayaran.update');
    Route::delete('verifikasipembayaran/{pembayaran}', [VerifikasiPembayaranController::class, 'destroy'])->name('verifikasipembayaran.destroy');

    // Notifikasi
    Route::get('notifikasi', [UserNotificationController::class, 'index'])->name('notifikasi.index');
    Route::put('notifikasi', [UserNotificationController::class, 'markAllRead'])->name('notifikasi.markallread');
    Route::put('notifikasi/{notifikasi}', [UserNotificationController::class, 'markRead'])->name('notifikasi.markread');
    Route::delete('notifikasi/{notifikasi}', [UserNotificationController::class, 'destroy'])->name('notifikasi.destroy');

    // pengumuman
    Route::get('pengumuman', [ManajemenPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('pengumuman', [ManajemenPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::get('pengumuman/{pengumuman}', [ManajemenPengumumanController::class, 'show'])->name('pengumuman.show');
    Route::put('pengumuman/{pengumuman}', [ManajemenPengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('pengumuman/{pengumuman}', [ManajemenPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
});

Route::prefix('penghuni')->name('penghuni.')->middleware(['auth', 'role:user'])->group(function () {
    // dashboard route buat landingpage penghuni
    // Route::get('/', [PenghuniDashboardController::class, 'index'])->name('dashboard');

    // profile
    Route::get('profile', [PenghuniProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [PenghuniProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [PenghuniProfileController::class, 'destroy'])->name('profile.destroy');

    // keluhan
    Route::get('keluhan', [PenghuniKeluhanController::class, 'index'])->name('keluhan.index');
    Route::post('keluhan', [PenghuniKeluhanController::class, 'store'])->name('keluhan.store');
    Route::get('keluhan/{keluhan}', [PenghuniKeluhanController::class, 'show'])->name('keluhan.show');
    Route::put('keluhan/{keluhan}', [PenghuniKeluhanController::class, 'update'])->name('keluhan.update');
    Route::delete('keluhan/{keluhan}', [PenghuniKeluhanController::class, 'destroy'])->name('keluhan.destroy');

    // tagihan
    Route::get('tagihan', [PenghuniTagihanController::class, 'index'])->name('tagihan.index');
    Route::get('tagihan/{tagihan}', [PenghuniTagihanController::class, 'show'])->name('tagihan.show');

    // pembayaran
    Route::get('pembayaran', [PenghuniPembayaraanController::class, 'index'])->name('pembayaran.index');
    Route::post('pembayaran', [PenghuniPembayaraanController::class, 'store'])->name('pembayaran.store');
    Route::get('pembayaran/{pembayaran}', [PenghuniPembayaraanController::class, 'show'])->name('pembayaran.show');
    Route::delete('pembayaran/{pembayaran}', [PenghuniPembayaraanController::class, 'destroy'])->name('pembayaran.destroy');

    // notifikasi
    Route::get('notifikasi', [UserNotificationController::class, 'index'])->name('notifikasi.index');
    Route::put('notifikasi', [UserNotificationController::class, 'markAllRead'])->name('notifikasi.markallread');
    Route::put('notifikasi/{notifikasi}', [UserNotificationController::class, 'markRead'])->name('notifikasi.markread');
    Route::delete('notifikasi/{notifikasi}', [UserNotificationController::class, 'destroy'])->name('notifikasi.destroy');

    // pengumuman
    Route::get('pengumuman', [PenghuniPengumumanController::class, 'index'])->name('pengumuman.index');
});
