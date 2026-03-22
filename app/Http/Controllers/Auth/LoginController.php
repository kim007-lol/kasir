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

            // SECURITY: Block login ke akun demo via form login manual
            if (str_starts_with($user->username ?? '', 'demo_')) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'login' => 'Akun demo hanya bisa diakses melalui tombol Demo di halaman utama.',
                ])->onlyInput('login');
            }

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

    public function demoLogin(Request $request, $role)
    {
        // Pastikan APP_MODE = demo sebelum mengizinkan auto-login
        if (!isDemo()) {
            return redirect('/')->with('error', 'Demo mode tidak aktif.');
        }

        // SECURITY FIX #1: Whitelist role yang valid
        $allowedRoles = ['admin', 'kasir', 'pelanggan'];
        if (!in_array($role, $allowedRoles, true)) {
            abort(404);
        }

        // Mapping role ke username demo
        $username = "demo_{$role}";

        // Cari user demo berdasarkan username
        $user = \App\Models\User::where('username', '=', $username)->first();

        if (!$user) {
            return redirect('/')->with('error', "Akun demo untuk {$role} belum tersedia.");
        }

        // Logout user saat ini jika ada
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Auto-login
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect sesuai role
        if ($user->role === 'kasir') {
            return redirect()->intended(route('cashier.dashboard'))->with('info', "Anda login sebagai Kasir (Mode Demo).");
        } elseif ($user->role === 'pelanggan') {
            return redirect()->intended(route('booking.menu'))->with('info', "Anda login sebagai Pelanggan (Mode Demo).");
        }

        return redirect()->intended(route('dashboard'))->with('info', "Anda login sebagai Admin (Mode Demo).");
    }

    public function logout(Request $request)
    {
        $isDemo = isDemoUser();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isDemo) {
            return redirect('/')->with('info', 'Anda telah logout dari Mode Demo.');
        }

        return redirect('/login');
    }
}
