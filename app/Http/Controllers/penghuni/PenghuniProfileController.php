<?php

namespace App\Http\Controllers\penghuni;

use App\Http\Controllers\Controller;
use App\Models\Penghunian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenghuniProfileController extends Controller
{
    /**
     * Menampilkan profile penghuni yang sedang login.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $penghunian = Penghunian::where('user_id', $user->id)
            ->with('kamar')
            ->latest()
            ->first();

        return view('Penghuni.Profil', compact('user', 'penghunian'));
    }

    /**
     * Update profile penghuni yang sedang login.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'no_hp' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            // Hapus image lama
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // Simpan image baru
            $validated['image'] = $request->file('image')
                ->store('user/profile', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make(
                $validated['password']
            );
        } else {
            unset($validated['password']);
        }

        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        |
        | role dan status tidak ada di $validated,
        | sehingga tidak dapat diubah melalui endpoint ini.
        |
        */

        $user->update($validated);

        return response()->json([
            'message' => 'Profile berhasil diperbarui.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Menghapus akun penghuni yang sedang login.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        /*
        |--------------------------------------------------------------------------
        | Hapus Image
        |--------------------------------------------------------------------------
        */

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus User
        |--------------------------------------------------------------------------
        |
        | penghunians.user_id menggunakan onDelete('cascade'),
        | sehingga data penghunian user ini ikut terhapus.
        |
        */

        $user->delete();

        /*
        |--------------------------------------------------------------------------
        | Hapus Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Akun berhasil dihapus.',
        ]);
    }
}
