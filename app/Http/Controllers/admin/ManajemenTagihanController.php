<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Penghunian;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class ManajemenTagihanController extends Controller
{
    /**
     * GET /admin/tagihan
     *
     * Search:
     * /admin/tagihan?search=andi
     * Filter status pembayaran:
     * /admin/tagihan?status=lunas
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $tagihans = Tagihan::query()
            ->with([
                'pembayaran',
                'penghunian.user:id,name,email,no_hp,image',
                'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('bulan_tagihan', 'like', "%{$search}%")
                        ->orWhereHas('penghunian.user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->get()
            ->when($status, function ($tagihans) use ($status) {
                return $tagihans->where('status_pembayaran', $status)->values();
            });

        return response()->json([
            'success' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => $tagihans,
        ]);
    }

    /**
     * GET /admin/tagihan/daftarpenghuni
     *
     * Daftar penghuni aktif (sudah punya kamar, belum checkout)
     * untuk form pembuatan tagihan.
     */
    public function showPenghuni()
    {
        $penghunians = Penghunian::query()
            ->whereNotNull('kamar_id')
            ->whereNull('tanggal_checkout')
            ->with([
                'user:id,name,email,no_hp,image',
                'kamar:id,nomor_kamar,tipe_kamar,harga',
            ])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data penghuni aktif berhasil diambil.',
            'data' => $penghunians,
        ]);
    }

    /**
     * POST /admin/tagihan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'penghunian_id' => ['required', 'exists:penghunians,id'],
            'bulan_tagihan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
        ]);

        $sudahAda = Tagihan::query()
            ->where('penghunian_id', $validated['penghunian_id'])
            ->where('bulan_tagihan', $validated['bulan_tagihan'])
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan untuk bulan tersebut sudah ada.',
            ], 422);
        }

        $tagihan = Tagihan::create($validated);

        $penghunian = Penghunian::query()->with('user')->find($validated['penghunian_id']);

        if ($penghunian?->user) {
            Notification::create([
                'user_id' => $penghunian->user->id,
                'judul' => 'Tagihan Baru',
                'pesan' => sprintf(
                    'Tagihan untuk bulan %s sebesar Rp %s telah dibuat.',
                    $tagihan->bulan_tagihan,
                    number_format((float) $tagihan->jumlah, 0, ',', '.')
                ),
                'tipe' => 'tagihan',
            ]);
        }

        $tagihan->load([
            'penghunian.user:id,name,email,no_hp,image',
            'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
            'pembayaran',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil ditambahkan.',
            'data' => $tagihan,
        ], 201);
    }

    /**
     * GET /admin/tagihan/{tagihan}
     */
    public function show(Tagihan $tagihan)
    {
        $tagihan->load([
            'pembayaran',
            'penghunian.user:id,name,email,no_hp,image',
            'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => $tagihan,
        ]);
    }

    /**
     * PUT/PATCH /admin/tagihan/{tagihan}
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $validated = $request->validate([
            'bulan_tagihan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0'],
        ]);

        $sudahAda = Tagihan::query()
            ->where('penghunian_id', $tagihan->penghunian_id)
            ->where('bulan_tagihan', $validated['bulan_tagihan'])
            ->where('id', '!=', $tagihan->id)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan untuk bulan tersebut sudah ada.',
            ], 422);
        }

        $tagihan->update($validated);

        $tagihan->load([
            'penghunian.user:id,name,email,no_hp,image',
            'penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
            'pembayaran',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil diperbarui.',
            'data' => $tagihan,
        ]);
    }

    /**
     * DELETE /admin/tagihan/{tagihan}
     */
    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dihapus.',
        ]);
    }
}
