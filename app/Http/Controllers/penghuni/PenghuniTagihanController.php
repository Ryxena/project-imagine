<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Penghunian;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PenghuniTagihanController extends Controller
{
    public function index(Request $request)
    {

        $request->user()->update(['last_read_tagihan_penghuni' => now()]);

        $userId = $request->user()->id;

        $latestPenghunian = Penghunian::where('user_id', $userId)->latest()->first();
        $tenantStatus = match (true) {
            ! $latestPenghunian => 'no_record',
            ! is_null($latestPenghunian->tanggal_checkout) => 'checked_out',
            is_null($latestPenghunian->kamar_id) => 'unassigned',
            default => 'active',
        };

        if (in_array($tenantStatus, ['unassigned', 'no_record'])) {
            $aktif = collect();
            $hasPaidHistory = false;
            $riwayat = new LengthAwarePaginator(collect(), 0, 5, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('Penghuni.tagihan.Tagihan', compact('aktif', 'riwayat', 'tenantStatus', 'latestPenghunian', 'hasPaidHistory'));
        }

        $tagihans = Tagihan::query()
            ->whereHas('penghunian', fn ($q) => $q->where('user_id', $userId))
            ->with(['pembayaran', 'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga'])
            ->latest()
            ->get();

        $aktif = $tagihans->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi', 'ditolak'])->sortBy('bulan_tagihan')->values();
        $riwayatSemua = $tagihans->where('status_pembayaran', 'lunas')->values();

        $hasPaidHistory = $riwayatSemua->count() > 0;

        $page = $request->query('page', 1);
        $perPage = 5;
        $riwayat = new LengthAwarePaginator(
            $riwayatSemua->forPage($page, $perPage), $riwayatSemua->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('Penghuni.tagihan.Tagihan', compact('aktif', 'riwayat', 'tenantStatus', 'latestPenghunian', 'hasPaidHistory'));
    }

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
