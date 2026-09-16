<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    /**
     * GET /notifikasi
     *
     * Menampilkan daftar notifikasi milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar notifikasi berhasil diambil.',
            'unread_count' => $notifications->where('dibaca', false)->count(),
            'data' => $notifications,
        ]);
    }

    /**
     * PUT /notifikasi/{notifikasi}
     *
     * Menandai satu notifikasi milik user yang sedang login sebagai sudah dibaca.
     */
    public function markRead(Request $request, string $id)
    {
        $notification = Notification::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $notification->update([
            'dibaca' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai sudah dibaca.',
            'data' => $notification,
        ]);
    }

    /**
     * PUT /notifikasi
     *
     * Menandai semua notifikasi milik user yang sedang login sebagai sudah dibaca.
     */
    public function markAllRead(Request $request)
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('dibaca', false)
            ->update([
                'dibaca' => true,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai sudah dibaca.',
        ]);
    }

    /**
     * DELETE /notifikasi/{notifikasi}
     *
     * Menghapus notifikasi milik user yang sedang login.
     */
    public function destroy(Request $request, string $id)
    {
        $notification = Notification::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus.',
        ]);
    }
}
