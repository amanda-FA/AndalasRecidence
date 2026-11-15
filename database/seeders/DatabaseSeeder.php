<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\JenisClient;
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            JenisClientSeeder::class,
            DataAsramaSeeder::class,
        ]);

        $accounts = [
            [
                'nama_lengkap' => 'Prof. Dr. Ir. H. Mansyur, M.T.',
                'email' => 'pimpinan@unand.ac.id',
                'username' => 'pimpinan',
                'no_hp' => '081234567801',
                'role' => 'Pimpinan',
            ],
            [
                'nama_lengkap' => 'Admin Layanan Asrama',
                'email' => 'admin@unand.ac.id',
                'username' => 'admin',
                'no_hp' => '081234567802',
                'role' => 'Admin Layanan',
            ],
            [
                'nama_lengkap' => 'Rahadian Triputra',
                'email' => 'fasilitator@unand.ac.id',
                'username' => 'fasilitator',
                'no_hp' => '081234567803',
                'role' => 'Fasilitator',
            ],
            [
                'nama_lengkap' => 'Teknisi Pemeliharaan',
                'email' => 'teknisi@unand.ac.id',
                'username' => 'teknisi',
                'no_hp' => '081234567804',
                'role' => 'Teknisi',
            ],
            [
                'nama_lengkap' => 'Petugas GO / CS',
                'email' => 'go@unand.ac.id',
                'username' => 'petugas_go',
                'no_hp' => '081234567805',
                'role' => 'GO',
            ],
            [
                'nama_lengkap' => 'Muhammad Fadhil',
                'email' => 'client@unand.ac.id',
                'username' => 'fadhil_client',
                'no_hp' => '081234567806',
                'role' => 'Client',
            ],
        ];

        $jenisReguler = JenisClient::where('jenis', 'Mahasiswa Reguler')->first();

        foreach ($accounts as $acc) {
            $role = Role::where('nama_role', $acc['role'])->first();

            $pengguna = Pengguna::firstOrCreate(
                ['email' => $acc['email']],
                [
                    'id_role' => $role?->id_role,
                    'nama_lengkap' => $acc['nama_lengkap'],
                    'username' => $acc['username'],
                    'no_hp' => $acc['no_hp'],
                    'password' => Hash::make('password'),
                    'status_aktif' => 1,
                ]
            );

            // Jika role Client, buatkan entri profil client
            if ($acc['role'] === 'Client' && $jenisReguler) {
                Client::firstOrCreate(
                    ['id_pengguna' => $pengguna->id_pengguna],
                    [
                        'id_jenis_client' => $jenisReguler->id_jenis_client,
                        'nomor_identitas' => '2111522001',
                        'pekerjaan' => 'Mahasiswa S1 Sistem Informasi',
                        'jenis_kelamin' => 'Laki-laki',
                        'status_KIPK' => 0,
                        'alamat_asal' => 'Kota Padang, Sumatera Barat',
                    ]
                );
            }
        }
    }
}
