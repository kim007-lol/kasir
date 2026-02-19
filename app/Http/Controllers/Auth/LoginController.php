<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required|min:8'
        ], [
            'login.required' => 'Username atau Email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // M2 Fix: Rate limiting - 5 percobaan per menit
        $throttleKey = Str::lower($request->input('login')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('login');
        }

        // Cek apakah input adalah email atau username
        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $loginCredentials = [
            $loginType => $credentials['login'],
            'password' => $credentials['password']
        ];

        if (Auth::attempt($loginCredentials)) {
            $user = Auth::user();

            // SEC-01: Block soft-deleted users
            if ($user->trashed()) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'login' => 'Akun ini sudah dinonaktifkan. Hubungi administrator.',
                ])->onlyInput('login');
            }

            // SEC-06: Block pelanggan from staff login
            if ($user->role === 'pelanggan') {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'login' => 'Akun pelanggan tidak bisa login di sini. Gunakan halaman login pelanggan.',
                ])->onlyInput('login');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role === 'kasir') {
                return redirect()->intended(route('cashier.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
