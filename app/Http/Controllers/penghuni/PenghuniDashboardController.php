<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use App\Models\Pengumuman;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class PenghuniDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $tagihanAktifList = Tagihan::query()
            ->whereHas('penghunian', fn ($q) => $q->where('user_id', $userId))
            ->with(['pembayaran', 'penghunian.kamar:id,nomor_kamar'])
            ->get()
            ->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi', 'ditolak'])
            ->sortBy('bulan_tagihan')
            ->values();

        $tagihanHero = $tagihanAktifList->first();

        $tagihanDitolakLain = $tagihanAktifList
            ->where('status_pembayaran', 'ditolak')
            ->reject(fn ($t) => $tagihanHero && $t->id === $tagihanHero->id)
            ->values();

        $keluhanAktifCount = Keluhan::where('user_id', $userId)
            ->whereIn('status', ['pending', 'process'])
            ->count();

        $pengumumanTerbaru = Pengumuman::latest()->first();

        return view('Penghuni.dashboard.index', compact(
            'tagihanHero',
            'tagihanDitolakLain',
            'keluhanAktifCount',
            'pengumumanTerbaru'
        ));
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
