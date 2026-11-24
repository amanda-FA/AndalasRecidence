<?php

namespace Database\Seeders;

use App\Models\Gedung;
use App\Models\JenisClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GedungJenisClientSeeder extends Seeder
{
    public function run(): void
    {
        // Aturan berdasarkan Data Asrama.xlsx
        // Bagian Penghuni Yang Eligible.

        $aturan = [
            'Gedung Asrama RPX (A)' => [
                'kipk',
                'reguler',
                's1_non_maba',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Rusunawa (B)' => [
                'reguler',
                's1_non_maba',
                'international_private',
                'international_funded',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Pupera Putri (C)' => [
                'reguler',
                's1_non_maba',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Menpera (D)' => [
                'kipk',
                'reguler',
                's1_non_maba',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama RMS (E)' => [
                'reguler',
                's1_non_maba',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Oren (F)' => [
                'reguler',
                's1_non_maba',
                'international_private',
                'international_funded',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Hijau (G)' => [
                'kipk',
                'reguler',
                's1_non_maba',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Pupera Putera (H)' => [
                'reguler',
                's1_non_maba',
                'international_private',
                'international_funded',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama Nakes' => [
                's1_non_maba',
                'guest',
                'summer_course',
            ],

            'Gedung Asrama ASN' => [
                'guest',
                'summer_course',
            ],
        ];

        DB::transaction(function () use ($aturan) {

            foreach ($aturan as $namaGedung => $kategori) {

                $gedung = Gedung::where(
                    'nama_gedung',
                    $namaGedung
                )->first();

                if (!$gedung) {
                    throw new RuntimeException(
                        'Gedung tidak ditemukan: ' . $namaGedung
                    );
                }

                $jenisClientIds = [];

                foreach ($kategori as $key) {

                    $namaKategori = JenisClient::CATEGORIES[$key];

                    $jenisClient = JenisClient::where(
                        'jenis',
                        $namaKategori
                    )->first();

                    if (!$jenisClient) {
                        throw new RuntimeException(
                            'Kategori tidak ditemukan: ' . $namaKategori
                        );
                    }

                    $jenisClientIds[] = $jenisClient->id_jenis_client;
                }

                // Menambahkan relasi tanpa menghapus
                // relasi lain yang sudah tersedia.
                $gedung->eligibleJenisClients()
                    ->syncWithoutDetaching($jenisClientIds);
            }
        });

        $this->command->info(
            'Relasi kategori Client dan gedung berhasil diisi.'
        );
    }
}
