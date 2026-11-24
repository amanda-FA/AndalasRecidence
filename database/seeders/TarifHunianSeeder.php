<?php

namespace Database\Seeders;

use App\Models\Gedung;
use App\Models\TipeKamar;
use App\Models\TarifHunian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TarifHunianSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Sumber Data Tarif
        |--------------------------------------------------------------------------
        |
        | Data Asrama(6).xlsx
        |
        | Tarif tahunan:
        | - Harga tahunan per kamar
        | - Harga tahunan per orang
        |
        | Tarif harian:
        | - Mahasiswa
        | - Non mahasiswa (UMUM)
        |
        | Tarif bulanan:
        | - Gedung ASN
        |
        */

        $tahunan = [

            // RPX (A)
            ['Gedung Asrama RPX (A)', 'Standar 2 Orang', 5000000, 2500000],

            // Rusunawa (B)
            ['Gedung Asrama Rusunawa (B)', 'Premium 2 Orang', 15000000, 7500000],
            ['Gedung Asrama Rusunawa (B)', 'Medium 4 Orang', 20000000, 5000000],
            ['Gedung Asrama Rusunawa (B)', 'Standar 2 Orang', 12000000, 6000000],

            // Pupera Putri (C)
            ['Gedung Asrama Pupera Putri (C)', 'Medium 4 Orang', 12000000, 3000000],

            // Menpera (D)
            ['Gedung Asrama Menpera (D)', 'Standar 4 Orang', 10000000, 2500000],

            // RMS (E)
            ['Gedung Asrama RMS (E)', 'Medium 2 Orang', 12000000, 6000000],
            ['Gedung Asrama RMS (E)', 'Standar 4 Orang', 10000000, 2500000],

            // Oren (F)
            ['Gedung Asrama Oren (F)', 'Medium 4 Orang', 13000000, 3250000],
            ['Gedung Asrama Oren (F)', 'Standar 4 Orang', 10000000, 2500000],

            // Hijau (G)
            ['Gedung Asrama Hijau (G)', 'Medium 4 Orang', 12000000, 3000000],
            ['Gedung Asrama Hijau (G)', 'Standar 4 Orang', 10000000, 2500000],

            // Pupera Putera (H)
            ['Gedung Asrama Pupera Putera (H)', 'Premium 2 Orang', 15000000, 7500000],
            ['Gedung Asrama Pupera Putera (H)', 'Medium 2 Orang', 12000000, 6000000],

            // Nakes
            ['Gedung Asrama Nakes', 'Premium 4 Orang', 15000000, 3750000],
            ['Gedung Asrama Nakes', 'Medium 4 Orang', 12000000, 3000000],
        ];

        /*
         * Format:
         * Gedung, tipe kamar,
         * tarif mahasiswa, tarif umum.
         *
         * Null berarti tarif tidak tercantum
         * dalam data asli, bukan berarti gratis.
         */

        $harian = [

            // RPX (A)
            ['Gedung Asrama RPX (A)', 'Umum 2 Orang', 75000, 100000],

            // Rusunawa (B)
            ['Gedung Asrama Rusunawa (B)', 'Umum 2 Orang', null, 300000],

            // Pupera Putri (C)
            ['Gedung Asrama Pupera Putri (C)', 'Umum 4 Orang', 75000, 100000],

            // Menpera (D)
            ['Gedung Asrama Menpera (D)', 'Umum 4 Orang', 75000, 100000],

            // RMS (E)
            ['Gedung Asrama RMS (E)', 'Umum 4 Orang', 75000, 100000],

            // Oren (F)
            ['Gedung Asrama Oren (F)', 'Umum 4 Orang', 75000, 100000],

            // Hijau (G)
            ['Gedung Asrama Hijau (G)', 'Umum 4 Orang', 75000, 100000],

            // Pupera Putera (H)
            ['Gedung Asrama Pupera Putera (H)', 'Umum 2 Orang', null, 300000],

            // Nakes
            ['Gedung Asrama Nakes', 'Umum 4 Orang', null, 250000],
            ['Gedung Asrama Nakes', 'Umum VIP 2 Orang', null, 300000],
        ];

        /*
         * Tarif bulanan ASN.
         * Satuan mengikuti kolom harga bulanan
         * per orang pada Excel.
         *
         * Belum digunakan dalam pembayaran Client
         * sebelum ketentuan penghuni ASN dikonfirmasi.
         */

        $bulanan = [

            ['Gedung Asrama ASN', 'Standar 1 Keluarga', 700000],
            ['Gedung Asrama ASN', 'Medium 1 Keluarga', 800000],
            ['Gedung Asrama ASN', 'Premium 1 Keluarga', 900000],
        ];

        $data = [];

        /*
        |--------------------------------------------------------------------------
        | Menyusun tarif tahunan
        |--------------------------------------------------------------------------
        */

        foreach ($tahunan as $item) {

            [
                $gedung,
                $tipe,
                $hargaKamar,
                $hargaOrang
            ] = $item;

            $data[] = [
                $gedung,
                $tipe,
                'mahasiswa',
                'tahun_kamar',
                $hargaKamar,
            ];

            $data[] = [
                $gedung,
                $tipe,
                'mahasiswa',
                'tahun_orang',
                $hargaOrang,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Menyusun tarif harian
        |--------------------------------------------------------------------------
        */

        foreach ($harian as $item) {

            [
                $gedung,
                $tipe,
                $hargaMahasiswa,
                $hargaUmum
            ] = $item;

            if ($hargaMahasiswa !== null) {

                $data[] = [
                    $gedung,
                    $tipe,
                    'mahasiswa',
                    'hari',
                    $hargaMahasiswa,
                ];
            }

            if ($hargaUmum !== null) {

                $data[] = [
                    $gedung,
                    $tipe,
                    'umum',
                    'hari',
                    $hargaUmum,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Menyusun tarif bulanan
        |--------------------------------------------------------------------------
        */

        foreach ($bulanan as $item) {

            [
                $gedung,
                $tipe,
                $nominal
            ] = $item;

            $data[] = [
                $gedung,
                $tipe,
                'asn',
                'bulan_orang',
                $nominal,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Menyimpan tarif ke database
        |--------------------------------------------------------------------------
        */

        $berhasil = 0;
        $dilewati = 0;
        $peringatan = [];

        DB::transaction(function () use (
            $data,
            &$berhasil,
            &$dilewati,
            &$peringatan
        ) {

            foreach ($data as $item) {

                [
                    $namaGedung,
                    $namaTipe,
                    $kategori,
                    $satuan,
                    $nominal
                ] = $item;

                $gedung = Gedung::where(
                    'nama_gedung',
                    $namaGedung
                )->first();

                $tipe = TipeKamar::where(
                    'nama_tipe',
                    $namaTipe
                )->first();

                /*
                 * Tidak membuat gedung atau tipe kamar
                 * baru secara otomatis.
                 */

                if (! $gedung || ! $tipe) {

                    $peringatan[] =
                        "Master data belum tersedia: "
                        . "{$namaGedung} - {$namaTipe} "
                        . "({$kategori}, {$satuan})";

                    $dilewati++;

                    continue;
                }

                /*
                 * Memastikan kombinasi gedung dan
                 * tipe kamar benar-benar tersedia.
                 */

                $tersedia = DB::table('kamar')
                    ->where(
                        'id_gedung',
                        $gedung->id_gedung
                    )
                    ->where(
                        'id_tipeKamar',
                        $tipe->id_tipeKamar
                    )
                    ->exists();

                if (! $tersedia) {

                    $peringatan[] =
                        "Kombinasi kamar belum tersedia: "
                        . "{$namaGedung} - {$namaTipe} "
                        . "({$kategori}, {$satuan})";

                    $dilewati++;

                    continue;
                }

                /*
                 * Validasi nominal tarif.
                 */

                if ($nominal <= 0 || $nominal > 9999999999.99) {

                    throw new \RuntimeException(
                        "Nominal tarif tidak valid: "
                        . "{$namaGedung} - {$namaTipe}"
                    );
                }

                /*
                 * Update atau insert tarif.
                 * Mencegah duplikasi data tarif.
                 */

                TarifHunian::updateOrCreate(
                    [
                        'id_gedung' => $gedung->id_gedung,
                        'id_tipeKamar' => $tipe->id_tipeKamar,
                        'kategori_tarif' => $kategori,
                        'satuan_tarif' => $satuan,
                    ],
                    [
                        'nominal' => $nominal,
                    ]
                );

                $berhasil++;
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Menampilkan hasil seeding
        |--------------------------------------------------------------------------
        */

        foreach ($peringatan as $pesan) {

            $this->command->warn($pesan);
        }

        $this->command->newLine();

        $this->command->info(
            '===== HASIL SEEDER TARIF HUNIAN ====='
        );

        $this->command->info(
            'Total tarif sumber Excel: ' . count($data)
        );

        $this->command->info(
            'Tarif berhasil disimpan/diperbarui: ' . $berhasil
        );

        if ($dilewati > 0) {

            $this->command->warn(
                'Tarif belum dapat disimpan: ' . $dilewati
            );

            $this->command->warn(
                'Periksa kesesuaian master data kamar '
                . 'dengan Data Asrama(6).xlsx.'
            );

        } else {

            $this->command->info(
                'Seluruh tarif berhasil disinkronkan.'
            );
        }

        $this->command->info(
            '===================================='
        );
    }
}
