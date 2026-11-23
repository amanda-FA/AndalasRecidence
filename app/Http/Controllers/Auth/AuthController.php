<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\JenisClient;
use App\Models\Pengguna;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function home(Request $request)
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $path = $this->redirectPathForUser($request->user());

        return redirect($path);
    }

    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        // Dukung login menggunakan email ataupun username
        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authAttempt = [
            $loginField => $credentials['email'],
            'password' => $credentials['password'],
        ];

        // Tabel pengguna pada skema ERD tidak memiliki kolom remember_token
        if (!Auth::attempt($authAttempt, false)) {
            return back()
                ->withErrors([
                    'email' => 'Email/Username atau password salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathForUser($request->user()));
    }

    /**
     * Tentukan tujuan redirect awal berdasarkan role pengguna.
     */
    private function redirectPathForUser(Pengguna $user): string
    {
        if ($user->hasRole('Pimpinan')) {
            return route('dashboard.okupansi');
        }

        if ($user->hasRole('Fasilitator')) {
            return route('gedung.index');
        }

        if ($user->hasRole('Client')) {
            return route('pendaftaran.create');
        }

        if ($user->hasRole('Admin Layanan')) {
            return route('periode.index');
        }

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
            'name' => ['required', 'string', 'max:150'],
            'applicant_category' => ['nullable', Rule::in(array_keys(JenisClient::CATEGORIES))],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150', 'unique:pengguna,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $clientRole = Role::firstOrCreate(['nama_role' => 'Client']);

        // Generate username unik dari bagian depan email
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $data['email'])[0]));
        $username = $baseUsername;
        $counter = 1;
        while (Pengguna::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = Pengguna::create([
            'id_role' => $clientRole->id_role,
            'nama_lengkap' => $data['name'],
            'email' => $data['email'],
            'username' => $username,
            'no_hp' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status_aktif' => 1,
        ]);

        $category = $data['applicant_category'] ?? 'reguler';
        $categoryLabel = JenisClient::CATEGORIES[$category];
        $jenisClient = JenisClient::firstOrCreate(['jenis' => $categoryLabel]);

        Client::create([
            'id_jenis_client' => $jenisClient->id_jenis_client,
            'id_pengguna' => $user->id_pengguna,
            'nomor_identitas' => $data['identity_number'] ?? null,
            'status_KIPK' => $category === 'kipk',
            'pekerjaan' => $categoryLabel,
        ]);

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
