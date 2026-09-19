<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    /**
     * Menampilkan profile admin yang sedang login.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return view('admin.Profile', compact('user'));
    }

    /**
     * Update profile admin yang sedang login.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'no_hp' => ['sometimes', 'required', 'string', 'max:255'],

            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],

            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $validated['image'] = $request->file('image')
                ->store('user/profile', 'public');
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile berhasil diperbarui.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Menghapus akun admin yang sedang login.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        Auth::logout();

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Profile berhasil dihapus.',
        ]);
    }
}
