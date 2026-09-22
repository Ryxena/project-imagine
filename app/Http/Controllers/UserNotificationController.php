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
        $query = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('dibaca', false);

        $unreadCount = $query->count();

        $notifications = $query->latest()
            ->take(30)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar notifikasi belum dibaca berhasil diambil.',
            'unread_count' => $unreadCount,
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
