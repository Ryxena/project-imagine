<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VerifikasiPembayaranController extends Controller
{
    /**
     * GET /admin/verifikasipembayaran
     *
     * Filter status verifikasi:
     * /admin/verifikasipembayaran?status=pending
     * Search nama penghuni:
     * /admin/verifikasipembayaran?search=andi
     */
    public function index(Request $request)
    {
        $request->user()->update(['last_read_verifikasi' => now()]);

        $pembayarans = Pembayaran::query()
            ->with([
                'tagihan.penghunian.user:id,name,email,no_hp,image',
                'tagihan.penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
            ])
            ->latest()
            ->get();

        return view('admin.pembayaraan.VerifikasiPembayaraan', compact('pembayarans'));
    }

    /**
     * GET /admin/verifikasipembayaran/{pembayaran}
     */
    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load([
            'tagihan.penghunian.user:id,name,email,no_hp,image',
            'tagihan.penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail pembayaran berhasil diambil.',
            'data' => $pembayaran,
        ]);
    }

    /**
     * PUT/PATCH /admin/verifikasipembayaran/{pembayaran}
     *
     * Verifikasi pembayaran:
     * - status_verifikasi: success|failed
     * - alasan_penolakan wajib jika ditolak (failed)
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        if ($pembayaran->status_verifikasi !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran ini sudah diverifikasi.',
            ], 422);
        }

        $validated = $request->validate([
            'status_verifikasi' => ['required', Rule::in(['success', 'failed'])],
            'alasan_penolakan' => [
                'nullable',
                'string',
                'max:255',
                'required_if:status_verifikasi,failed',
            ],
        ]);

        $pembayaran->update([
            'status_verifikasi' => $validated['status_verifikasi'],
            'alasan_penolakan' => $validated['alasan_penolakan'] ?? null,
        ]);

        $pembayaran->load('tagihan.penghunian.user');

        $penerima = $pembayaran->tagihan->penghunian->user ?? null;

        if ($penerima) {
            if ($validated['status_verifikasi'] === 'success') {
                Notification::create([
                    'user_id' => $penerima->id,
                    'judul' => 'Pembayaran Diterima',
                    'pesan' => sprintf(
                        'Pembayaran tagihan bulan %s telah diverifikasi. Terima kasih.',
                        $pembayaran->tagihan->bulan_tagihan
                    ),
                    'tipe' => 'pembayaran',
                ]);
            } else {
                Notification::create([
                    'user_id' => $penerima->id,
                    'judul' => 'Pembayaran Ditolak',
                    'pesan' => sprintf(
                        'Pembayaran tagihan bulan %s ditolak. Alasan: %s',
                        $pembayaran->tagihan->bulan_tagihan,
                        $validated['alasan_penolakan']
                    ),
                    'tipe' => 'pembayaran',
                ]);
            }
        }

        $pembayaran->load([
            'tagihan.penghunian.user:id,name,email,no_hp,image',
            'tagihan.penghunian.kamar:id,nomor_kamar,tipe_kamar,harga',
        ]);

        return response()->json([
            'success' => true,
            'message' => $validated['status_verifikasi'] === 'success'
                ? 'Pembayaran berhasil diverifikasi.'
                : 'Pembayaran berhasil ditolak.',
            'data' => $pembayaran,
        ]);
    }

    /**
     * DELETE /admin/verifikasipembayaran/{pembayaran}
     */
    public function destroy(Pembayaran $pembayaran)
    {
        if ($pembayaran->bukti_pembayaran) {
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $pembayaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dihapus.',
        ]);
    }
}
