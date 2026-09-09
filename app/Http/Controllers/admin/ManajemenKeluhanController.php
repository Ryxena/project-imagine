<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use Illuminate\Http\Request;

class ManajemenKeluhanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,process,resolved',
        ]);

        $keluhans = Keluhan::with([
            'user:id,name,image',
            'user.penghunian:id,user_id,kamar_id,tanggal_masuk',
            'user.penghunian.kamar:id,nomor_kamar',
        ])->when($request->status, function ($query, $status) {
            $query->where('status', $status);
        })->latest()->get();

        return response()->json([
            'success' => true,
            'filter' => $request->status ?? 'all',
            'data' => $keluhans,
        ]);
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
