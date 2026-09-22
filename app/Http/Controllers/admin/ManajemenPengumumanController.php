<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManajemenPengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with('admin:id,name')
            ->latest()
            ->paginate(6);

        return view('admin.pengumuman.ManajemenPengumuman', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'type' => 'required|in:informasi,umum,penting',
            'deskripsi' => 'required|string',
            'tanggal_publish' => 'required|date',
        ]);

        $pengumuman = Pengumuman::create([
            'admin_id' => $user->id,
            'judul' => $validated['judul'],
            'type' => $validated['type'],
            'deskripsi' => $validated['deskripsi'],
            'tanggal_publish' => $validated['tanggal_publish'],
        ]);

        $penghunis = User::where('role', 'user')->get();

        foreach ($penghunis as $penghuni) {
            Notification::create([
                'user_id' => $penghuni->id,
                'judul' => 'Pengumuman Baru: '.$pengumuman->judul,
                'pesan' => Str::limit($pengumuman->deskripsi, 80),
                'tipe' => 'pengumuman',
                'dibaca' => false,
            ]);
        }

        $pengumuman->load('admin:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat dan notifikasi dikirim.',
            'data' => $pengumuman,
        ], 201);
    }

    public function show(Pengumuman $pengumuman)
    {
        $pengumuman->load('admin:id,name');

        return response()->json([
            'success' => true,
            'data' => $pengumuman,
        ]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:informasi,umum,penting',
            'deskripsi' => 'sometimes|required|string',
            'tanggal_publish' => 'sometimes|required|date',
        ]);

        $pengumuman->update($validated);

        $penghunis = User::where('role', 'user')->get();

        foreach ($penghunis as $penghuni) {
            Notification::create([
                'user_id' => $penghuni->id,
                'judul' => 'Pengumuman Diperbarui: '.$pengumuman->judul,
                'pesan' => 'Ada pembaruan informasi pada pengumuman ini.',
                'tipe' => 'pengumuman',
                'dibaca' => false,
            ]);
        }

        $pengumuman->load('admin:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui dan notifikasi dikirim.',
            'data' => $pengumuman,
        ]);
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus',
        ]);
    }
}
