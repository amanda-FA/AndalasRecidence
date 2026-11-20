<?php

namespace Database\Seeders;

use App\Models\JenisBerkas;
use Illuminate\Database\Seeder;

class JenisBerkasSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Fotocopy Kartu Keluarga', 'Fotocopy KTP', 'Hasil Tes Kesehatan'] as $nama) {
            JenisBerkas::firstOrCreate(['nama_berkas' => $nama]);
        }
    }
}
