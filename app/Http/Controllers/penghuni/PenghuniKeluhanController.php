<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Keluhan;
use App\Models\Notification;
use App\Models\Penghunian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PenghuniKeluhanController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->update(['last_read_keluhan_penghuni' => now()]);
        
        $userId = $request->user()->id;

        $latestPenghunian = Penghunian::where('user_id', $userId)->latest()->first();
        $tenantStatus = match (true) {
            ! $latestPenghunian => 'no_record',
            ! is_null($latestPenghunian->tanggal_checkout) => 'checked_out',
            is_null($latestPenghunian->kamar_id) => 'unassigned',
            default => 'active',
        };

        $countPending = Keluhan::where('user_id', $userId)->where('status', 'pending')->count();
        $countProcess = Keluhan::where('user_id', $userId)->where('status', 'process')->count();
        $countResolved = Keluhan::where('user_id', $userId)->where('status', 'resolved')->count();

        $keluhans = Keluhan::where('user_id', $userId)
            ->latest()
            ->paginate(5);

        return view('Penghuni.keluhan.Keluhan', compact(
            'keluhans',
            'countPending',
            'countProcess',
            'countResolved',
            'tenantStatus',
            'latestPenghunian'
        ));
    }

    public function create() {}

    public function store(Request $request)
    {
        $user = $request->user();
        $latestPenghunian = Penghunian::where('user_id', $user->id)->latest()->first();
        if (! $latestPenghunian || is_null($latestPenghunian->kamar_id) || ! is_null($latestPenghunian->tanggal_checkout)) {
            return response()->json([
                'message' => 'Anda belum ditempatkan ke kamar aktif, sehingga belum dapat membuat keluhan.',
            ], 403);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

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

        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'judul' => 'Keluhan Baru dari '.$user->name,
                'pesan' => Str::limit($keluhan->judul, 40),
                'tipe' => 'keluhan',
                'dibaca' => false,
            ]);
        }

        return response()->json([
            'message' => 'Keluhan berhasil dibuat.',
            'data' => $keluhan,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['message' => 'Detail keluhan berhasil diambil.', 'data' => $keluhan]);
    }

    public function edit(Request $request, string $id) {}

    public function update(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)->findOrFail($id);

        if ($keluhan->status !== 'pending') {
            return response()->json(['message' => 'Keluhan yang sedang diproses atau sudah selesai tidak dapat diubah.'], 403);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($keluhan->image) {
                Storage::disk('public')->delete($keluhan->image);
            }
            $validated['image'] = $request->file('image')->store('user/keluhan', 'public');
        }

        $keluhan->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'image' => $validated['image'] ?? $keluhan->image,
        ]);

        return response()->json(['message' => 'Keluhan berhasil diperbarui.', 'data' => $keluhan->fresh()]);
    }

    public function destroy(Request $request, string $id)
    {
        $keluhan = Keluhan::where('user_id', $request->user()->id)->findOrFail($id);

        if ($keluhan->status !== 'pending') {
            return response()->json(['message' => 'Keluhan yang sedang diproses atau sudah selesai tidak dapat dihapus.'], 403);
        }

        if ($keluhan->image) {
            Storage::disk('public')->delete($keluhan->image);
        }

        $keluhan->delete();

        return response()->json(['message' => 'Keluhan berhasil dihapus.']);
    }
}
