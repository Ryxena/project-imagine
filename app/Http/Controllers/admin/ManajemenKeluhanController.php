<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManajemenKeluhanController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->update(['last_read_keluhan_admin' => now()]);

        $keluhans = Keluhan::with([
            'user:id,name,image',
            'user.penghunian' => function ($query) {
                $query->select('id', 'user_id', 'kamar_id', 'tanggal_masuk')->latest();
            },
            'user.penghunian.kamar:id,nomor_kamar',
        ])->latest()->get();

        return view('admin.keluhan.ManajemenKeluhan', compact('keluhans'));
    }

    public function update(Request $request, Keluhan $keluhan)
    {
        $nextStatus = [
            'pending' => 'process',
            'process' => 'resolved',
            'resolved' => 'resolved',
        ];

        $newStatus = $nextStatus[$keluhan->status];

        $keluhan->update([
            'status' => $newStatus,
        ]);

        $statusPesan = match ($newStatus) {
            'process' => 'Keluhan Anda sedang diproses oleh admin.',
            'resolved' => 'Keluhan Anda telah selesai ditangani.',
            default => 'Status keluhan Anda telah diperbarui.',
        };

        Notification::create([
            'user_id' => $keluhan->user_id,
            'judul' => 'Update Keluhan: '.Str::limit($keluhan->judul, 30),
            'pesan' => $statusPesan,
            'tipe' => 'keluhan',
            'dibaca' => false,
        ]);

        return response()->json([
            'message' => 'Status keluhan berhasil diperbarui',
            'data' => $keluhan->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
