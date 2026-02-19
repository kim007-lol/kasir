<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show customer login form
     */
    public function showLoginForm()
    {
        return view('auth.customer-login');
    }

    /**
     * Handle customer login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        // Rate limiting
        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('username');
        }

        // Only allow pelanggan role to login here
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            // SEC-01: Block soft-deleted users
            if ($user->trashed()) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'username' => 'Akun ini sudah dinonaktifkan.',
                ])->onlyInput('username');
            }

            if ($user->role !== 'pelanggan') {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'username' => 'Akun ini bukan akun pelanggan. Silakan login di halaman staf.',
                ])->onlyInput('username');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('booking.menu'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Show customer registration form
     */
    public function showRegisterForm()
    {
        return view('auth.customer-register');
    }

    /**
     * Handle customer registration
     * Creates both User (for login) and Member (for profile/transactions)
     */
    public function register(Request $request)
    {
        // WARN-01: Rate limit registrasi (5 per menit per IP)
        $throttleKey = 'register|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan registrasi. Coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }
        RateLimiter::hit($throttleKey, 60);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'username' => 'required|string|max:255|unique:users,username|alpha_dash',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
            'address.required' => 'Alamat/Kelas harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['username'] . '@pelanggan.local', // auto-generated
                'password' => Hash::make($validated['password']),
                'role' => 'pelanggan',
            ]);

            // Create linked member profile
            Member::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'user_id' => $user->id,
            ]);

            DB::commit();

            // Auto login after registration
            Auth::login($user);

            return redirect()->route('booking.menu')->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'username' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
            ])->withInput();
        }
    }

    /**
     * Handle customer logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
