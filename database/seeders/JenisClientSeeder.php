<?php

namespace Database\Seeders;

use App\Models\JenisClient;
use Illuminate\Database\Seeder;

class JenisClientSeeder extends Seeder
{
    public function run(): void
    {
        $jenisList = [
            'Mahasiswa Reguler',
            'Mahasiswa KIP-K',
            'Mahasiswa Internasional',
            'Mahasiswa Pascasarjana',
            'Tamu / Umum',
        ];

        foreach ($jenisList as $jenis) {
            JenisClient::firstOrCreate([
                'jenis' => $jenis,
            ]);
        }
    }
}
