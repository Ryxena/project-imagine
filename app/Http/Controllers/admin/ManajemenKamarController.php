<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;

class ManajemenKamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kamar = Kamar::query()
            ->leftJoin(
                'penghunians',
                'kamars.id', '=', 'penghunians.kamar_id'
            )->leftJoin(
                'users',
                'penghunians.user_id', '=', 'users.id'
            )->select([
                'kamars.id',
                'kamars.nomor_kamar',
                'kamars.tipe_kamar',
                'kamars.harga',
                'kamars.deskripsi',

                'users.name',
                'users.image',

                'penghunians.kamar_id as penghuni_kamar_id',
                'penghunians.tanggal_masuk',
                'penghunians.tanggal_checkout',
            ])
            ->where(function ($q) {
                $q->whereNull('penghunians.tanggal_checkout')
                    ->orWhereNull('penghunians.kamar_id');
            })
            ->get();

        return view('admin.kamar.ManajemenKamar', compact('kamar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:255'],
            'tipe_kamar' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['required', 'string'],
        ]);

        $kamar = Kamar::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil ditambahkan.',
            'data' => $kamar,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kamar $kamar)
    {
        //
        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:255'],
            'tipe_kamar' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['required', 'string'],
        ]);

        $kamar->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil diperbarui.',
            'data' => $kamar,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kamar $kamar)
    {
        if ($kamar->penghunian()->whereNull('tanggal_checkout')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tidak dapat dihapus karena sedang terisi oleh penghuni.',
            ], 422);
        }

        $kamar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil dihapus.',
        ]);
    }
}
