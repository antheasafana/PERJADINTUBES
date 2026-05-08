<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// AUTH
use Illuminate\Support\Facades\Auth;

// HASH
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===============================
    // FORM LOGIN
    // ===============================
    public function showLoginForm()
    {
        return view('login');
    }

    // ===============================
    // PROSES LOGIN
    // ===============================
    public function login(Request $request)
    {
        $credentials = $request->validate([

            'email' => 'required|email',

            'password' => 'required|min:6',

        ]);

        // LOGIN
        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])
        ) {

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([

            'email' => 'Email atau password salah.',

        ]);
    }

    // ===============================
    // LOGOUT
    // ===============================
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // ===============================
    // FORM UBAH PASSWORD
    // ===============================
    public function ubahpassword()
    {
        return view('ubahpassword');
    }

    // ===============================
    // PROSES UBAH PASSWORD
    // ===============================
    public function prosesubahpassword(Request $request)
    {
        $request->validate([

            'password' => 'required|string|min:5',

        ]);

        $user = Auth::user();

        $user->password = Hash::make(
            $request->password
        );

        $user->save();

        return redirect('/dashboard')
            ->with(
                'success',
                'Password berhasil diperbarui!'
            );
    }
}