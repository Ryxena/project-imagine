<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'aktif',
        ])) {
            throw ValidationException::withMessages([
                'email' => 'Email, password, atau akun tidak aktif.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        // TODO: ganti ke route('penghuni.dashboard') begitu controller & route-nya aktif.
        return redirect()->route('penghuni.tagihan.index');
    }

    public function me()
    {
        return response()->json([
            'message' => 'Berhasil mendapatkan data pengguna.',
            'user' => Auth::user(),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
