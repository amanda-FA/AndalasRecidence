<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            GedungSeeder::class,
        ]);

        $accounts = [
            [
                'name' => 'Prof. Dr. Ir. H. Mansyur, M.T.',
                'email' => 'pimpinan@unand.ac.id',
                'role' => 'Pimpinan',
            ],
            [
                'name' => 'Admin Layanan Asrama',
                'email' => 'admin@unand.ac.id',
                'role' => 'Admin Layanan',
            ],
            [
                'name' => 'Rahadian Triputra',
                'email' => 'fasilitator@unand.ac.id',
                'role' => 'Fasilitator',
            ],
            [
                'name' => 'Teknisi Pemeliharaan',
                'email' => 'teknisi@unand.ac.id',
                'role' => 'Teknisi',
            ],
            [
                'name' => 'Petugas GO / CS',
                'email' => 'go@unand.ac.id',
                'role' => 'GO',
            ],
            [
                'name' => 'Muhammad Fadhil (Client)',
                'email' => 'client@unand.ac.id',
                'role' => 'Client',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles([$account['role']]);
        }
    }
}
