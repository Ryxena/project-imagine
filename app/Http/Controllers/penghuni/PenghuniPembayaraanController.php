<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenghuniPembayaraanController extends Controller
{
    /**
     * GET /penghuni/pembayaran
     *
     * Menampilkan daftar pembayaran milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $pembayarans = Pembayaran::query()
            ->whereHas('tagihan.penghunian', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('tagihan')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar pembayaran berhasil diambil.',
            'data' => $pembayarans,
        ]);
    }

    /**
     * POST /penghuni/pembayaran
     *
     * Penghuni mengunggah bukti pembayaran untuk tagihannya.
     * Pembayaran baru berstatus pending sampai diverifikasi admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tagihan_id' => ['required', 'exists:tagihans,id'],
            'bukti_pembayaran' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'tanggal_pembayaran' => ['required', 'date'],
        ]);

        $tagihan = Tagihan::query()
            ->whereHas('penghunian', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->findOrFail($validated['tagihan_id']);

        $sudahAdaPembayaran = Pembayaran::query()
            ->where('tagihan_id', $tagihan->id)
            ->whereIn('status_verifikasi', ['pending', 'success'])
            ->exists();

        if ($sudahAdaPembayaran) {
            return response()->json([
                'message' => 'Tagihan ini sudah memiliki pembayaran yang menunggu verifikasi atau sudah lunas.',
            ], 422);
        }

        $buktiPath = $validated['bukti_pembayaran']->store('pembayaran/bukti', 'public');

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'bukti_pembayaran' => $buktiPath,
            'status_verifikasi' => 'pending',
            'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
        ]);

        $admins = User::query()->where('role', 'super_admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'judul' => 'Pembayaran Baru',
                'pesan' => sprintf(
                    '%s mengirim bukti pembayaran untuk tagihan bulan %s. Menunggu verifikasi.',
                    $request->user()->name,
                    $tagihan->bulan_tagihan
                ),
                'tipe' => 'pembayaran',
            ]);
        }

        return response()->json([
            'message' => 'Bukti pembayaran berhasil dikirim, menunggu verifikasi.',
            'data' => $pembayaran,
        ], 201);
    }

    /**
     * GET /penghuni/pembayaran/{pembayaran}
     *
     * Menampilkan detail pembayaran milik user yang sedang login.
     */
    public function show(Request $request, string $id)
    {
        $pembayaran = Pembayaran::query()
            ->whereHas('tagihan.penghunian', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('tagihan')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail pembayaran berhasil diambil.',
            'data' => $pembayaran,
        ]);
    }

    /**
     * DELETE /penghuni/pembayaran/{pembayaran}
     *
     * Penghuni dapat membatalkan pembayarannya
     * selama belum diverifikasi admin (masih pending).
     */
    public function destroy(Request $request, string $id)
    {
        $pembayaran = Pembayaran::query()
            ->whereHas('tagihan.penghunian', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->findOrFail($id);

        if ($pembayaran->status_verifikasi !== 'pending') {
            return response()->json([
                'message' => 'Pembayaran sudah diverifikasi dan tidak dapat dibatalkan.',
            ], 422);
        }

        if ($pembayaran->bukti_pembayaran) {
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $pembayaran->delete();

        return response()->json([
            'message' => 'Pembayaran berhasil dibatalkan.',
        ]);
    }
}
