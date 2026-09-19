<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use Illuminate\Http\Request;

class ManajemenKeluhanController extends Controller
{
    public function index(Request $request)
    {
        $keluhans = Keluhan::with([
            'user:id,name',
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

        $keluhan->update([
            'status' => $nextStatus[$keluhan->status],
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
