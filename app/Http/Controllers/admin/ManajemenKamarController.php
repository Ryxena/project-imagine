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
        $sortHarga = $request->query('sort_harga');
        $filterKamar = $request->query('filter_kamar');

        $kamar = Kamar::query()
        ->leftJoin(
            'penghunians',
            'kamars.id','=','penghunians.kamar_id'
        )->leftJoin(
            'users',
            'penghunians.user_id','=','users.id')->select([
            'kamars.id',
            'kamars.nomor_kamar',
            'kamars.tipe_kamar',
            'kamars.harga',
            'kamars.deskripsi',

            'users.name',

            'penghunians.tanggal_masuk',
            'penghunians.tanggal_checkout',
        ])

        // Filter kamar
        ->when($filterKamar === 'terisi', function ($query) {
            $query->whereNotNull('penghunians.kamar_id');
        })

        ->when($filterKamar === 'kosong', function ($query) {
            $query->whereNull('penghunians.kamar_id');
        })

        // Sort harga
        ->when($sortHarga === 'termurah', function ($query) {
            $query->orderBy('kamars.harga', 'asc');
        })

        ->when($sortHarga === 'termahal', function ($query) {
            $query->orderBy('kamars.harga', 'desc');
        })

        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil diambil.',
            'data' => $kamar,
        ]);
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
        $kamar->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil dihapus.',
        ]);
    }
}
