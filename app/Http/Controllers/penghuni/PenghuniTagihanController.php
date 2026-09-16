<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

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
        $status = $request->query('status');

        $tagihans = Tagihan::query()
            ->whereHas('penghunian', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with([
                'pembayaran',
                'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
            ])
            ->latest()
            ->get()
            ->when($status, function ($tagihans) use ($status) {
                return $tagihans->where('status_pembayaran', $status)->values();
            });

        return response()->json([
            'message' => 'Daftar tagihan berhasil diambil.',
            'data' => $tagihans,
        ]);
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
