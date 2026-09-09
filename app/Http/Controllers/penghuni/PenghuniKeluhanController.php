<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenghuniKeluhanController extends Controller
{
        /**
     * Menampilkan daftar keluhan milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $keluhans = Keluhan::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar keluhan berhasil diambil.',
            'data' => $keluhans,
        ]);

        // return view('penghuni.keluhan', compact('keluhans'));
    }

    /**
     * Form create.
     *
     * Jika menggunakan Blade, method ini dapat digunakan
     * untuk menampilkan halaman/form membuat keluhan.
     */
    public function create()
    {

    }

    /**
     * Menyimpan keluhan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => [
                'required',
                'string',
                'max:255',
            ],

            'deskripsi' => [
                'required',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = $request->user();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('user/keluhan', 'public');
        }

        $keluhan = Keluhan::create([
            'user_id' => $user->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'image' => $validated['image'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Keluhan berhasil dibuat.',
            'data' => $keluhan,
        ], 201);
    }

    /**
     * Menampilkan detail keluhan milik user yang sedang login.
     */
    public function show(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail keluhan berhasil diambil.',
            'data' => $keluhan,
        ]);
    }

    /**
     * Form edit.
     */
    public function edit(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // return view('penghuni.keluhan', compact('keluhan'));
    }

    /**
     * Update keluhan.
     *
     * Penghuni tidak dapat mengubah status.
     */
    public function update(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'judul' => [
                'required',
                'string',
                'max:255',
            ],

            'deskripsi' => [
                'required',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        if ($request->hasFile('image')) {

            if ($keluhan->image) {
                Storage::disk('public')->delete($keluhan->image);
            }

            $validated['image'] = $request->file('image')
                ->store('user/keluhan', 'public');
        }

        $keluhan->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'image' => $validated['image'] ?? $keluhan->image,
        ]);

        return response()->json([
            'message' => 'Keluhan berhasil diperbarui.',
            'data' => $keluhan->fresh(),
        ]);
    }

    /**
     * Menghapus keluhan milik user yang sedang login.
     */
    public function destroy(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($keluhan->image) {
            Storage::disk('public')->delete($keluhan->image);
        }

        $keluhan->delete();

        return response()->json([
            'message' => 'Keluhan berhasil dihapus.',
        ]);
    }
}
