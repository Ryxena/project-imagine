<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // TODO: ganti semua data di bawah ini dengan query Eloquent asli
        // setelah model relationship (Kamar, Penghunian, Tagihan, dst) dikonfirmasi.
        // Struktur array ini SENGAJA dibuat sesuai field yang disebut di spec
        // requirement Dashboard, supaya Blade-nya nggak perlu diubah nanti.

        $kpi = [
            'kamar_terisi' => 18,
            'kamar_total' => 20,
            'penghuni_aktif' => 22,
            'penghuni_bulan_ini' => 2,
            'tagihan_lunas' => 15,
            'tagihan_total' => 22,
        ];

        $perluTindakan = [
            ['type' => 'verifikasi', 'label' => 'Verifikasi Pembayaran', 'meta' => '2 bukti transfer baru diunggah.', 'count' => 2, 'route' => 'admin.verifikasipembayaran.index'],
            ['type' => 'telat', 'label' => 'Penghuni Telat Bayar', 'meta' => '3 penghuni melewati jatuh tempo (>3 hari).', 'route' => 'admin.tagihan.index'],
            ['type' => 'keluhan', 'label' => 'Keluhan Aktif', 'meta' => '1 keluhan baru: "AC Kamar 104 Bocor".', 'route' => 'admin.keluhan.index'],
            ['type' => 'kamar_kosong', 'label' => 'Kamar Kosong', 'meta' => '2 kamar siap disewakan.', 'route' => 'admin.kamar.index'],
        ];

        $aktivitas = [
            ['text' => 'Andi (Kamar 102) mengunggah bukti transfer.', 'waktu' => '2 jam yang lalu'],
            ['text' => 'Penghuni baru Siti terdaftar di Kamar 205.', 'waktu' => 'Kemarin, 14:30'],
            ['text' => 'Keluhan "Lampu mati" di Kamar 101 ditandai Selesai.', 'waktu' => 'Kemarin, 09:00'],
        ];

        return view('admin.dashboard.index', compact('kpi', 'perluTindakan', 'aktivitas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
