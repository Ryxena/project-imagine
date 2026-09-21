<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PenghuniTagihanController extends Controller
{
    /**
     * GET /penghuni/tagihan
     *
     * Menampilkan daftar tagihan milik user yang sedang login.
     *
     * Filter status pembayaran:
     * /penghuni/tagihan?status=belum_bayar
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $tagihans = Tagihan::query()
            ->whereHas('penghunian', fn ($q) => $q->where('user_id', $userId))
            ->with(['pembayaran', 'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga'])
            ->latest()
            ->get();

        $aktif = $tagihans
            ->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi', 'ditolak'])
            ->sortBy('bulan_tagihan')
            ->values();

        $riwayatSemua = $tagihans
            ->where('status_pembayaran', 'lunas')
            ->values();

        $page = $request->query('page', 1);
        $perPage = 5;
        $riwayat = new LengthAwarePaginator(
            $riwayatSemua->forPage($page, $perPage),
            $riwayatSemua->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('Penghuni.tagihan.Tagihan', compact('aktif', 'riwayat'));
    }

    /**
     * GET /penghuni/tagihan/{tagihan}
     *
     * Menampilkan detail tagihan milik user yang sedang login.
     */
    public function show(Request $request, string $id)
    {
        $tagihan = Tagihan::query()
            ->whereHas('penghunian', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with([
                'pembayaran',
                'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
            ])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => $tagihan,
        ]);
    }
}
