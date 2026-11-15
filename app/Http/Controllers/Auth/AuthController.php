<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathForUser($request->user()));
    }

    /**
     * Tentukan tujuan redirect awal berdasarkan role pengguna.
     */
    private function redirectPathForUser(User $user): string
    {
        if ($user->hasRole('Pimpinan')) {
            return route('dashboard.okupansi');
        }

        if ($user->hasRole('Fasilitator')) {
            return route('gedung.index');
        }

        // Role lain (Admin Layanan, Teknisi, GO, Client) sementara diarahkan ke beranda
        // hingga dashboard/halaman modul masing-masing tersedia.
        return '/';
    }

    // Menampilkan halaman registrasi
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses registrasi
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'applicant_category' => ['nullable', 'string', 'in:reguler,kipk,summer_course,guest'],
            'identity_number' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'applicant_category' => $data['applicant_category'] ?? null,
            'identity_number' => $data['identity_number'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'web',
        ]);

        $user->assignRole('Client');

        return redirect()->route('login')
            ->with('status', 'Akun Client berhasil dibuat! Silakan masuk menggunakan email dan kata sandi Anda.');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
