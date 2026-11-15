<?php

namespace Database\Seeders;

use App\Models\Gedung;
use App\Models\Kamar;
use Illuminate\Database\Seeder;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        $gedungs = [
            [
                'kode' => 'GD-UNAND-MN',
                'nama' => 'Gedung Menpera',
                'peruntukan' => 'putra',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Barat',
                'jumlah_lantai' => 4,
                'estimasi_kamar' => 100,
                'kapasitas_bed' => 400,
                'fasilitas' => ['Dapur / Pantry', 'Musholla Terpadu', 'Ruang Belajar / Cowork', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN'],
                'deskripsi' => 'Hunian mahasiswa putra reguler dengan akses dekat halte bus kampus dan ruang belajar bersama.',
                'sample_terisi' => 384,
            ],
            [
                'kode' => 'GD-UNAND-PP',
                'nama' => 'Gedung Pupera',
                'peruntukan' => 'putri',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Timur (Pusat Hunian)',
                'jumlah_lantai' => 4,
                'estimasi_kamar' => 125,
                'kapasitas_bed' => 500,
                'fasilitas' => ['Dapur / Pantry', 'Musholla Terpadu', 'Parkir Berkanopi', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN'],
                'deskripsi' => 'Rusunawa mahasiswi berstandar nasional dilengkapi pos pengamanan terpadu 24 jam dan pantry induksi.',
                'sample_terisi' => 470,
            ],
            [
                'kode' => 'GD-UNAND-RSN',
                'nama' => 'Gedung Rusunawa',
                'peruntukan' => 'putra',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Barat',
                'jumlah_lantai' => 5,
                'estimasi_kamar' => 125,
                'kapasitas_bed' => 500,
                'fasilitas' => ['Ruang Belajar / Cowork', 'Parkir Berkanopi', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN', 'Sistem Hydrant'],
                'deskripsi' => 'Asrama terintegrasi untuk mahasiswa tingkat awal dan akhir dekat lapangan olahraga.',
                'sample_terisi' => 460,
            ],
            [
                'kode' => 'GD-UNAND-RPX',
                'nama' => 'Gedung RPX',
                'peruntukan' => 'putri',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Timur (Pusat Hunian)',
                'jumlah_lantai' => 4,
                'estimasi_kamar' => 88,
                'kapasitas_bed' => 350,
                'fasilitas' => ['Dapur / Pantry', 'Musholla Terpadu', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN'],
                'deskripsi' => 'Hunian unggulan mahasiswi baru dengan fasilitas aula belajar mandiri dan laundry bersama.',
                'sample_terisi' => 311,
            ],
            [
                'kode' => 'GD-UNAND-RM',
                'nama' => 'Gedung Rosmainshev',
                'peruntukan' => 'campuran',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Timur (Pusat Hunian)',
                'jumlah_lantai' => 4,
                'estimasi_kamar' => 75,
                'kapasitas_bed' => 300,
                'fasilitas' => ['Ruang Belajar / Cowork', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN'],
                'deskripsi' => 'Hunian multi-kultur bagi peneliti tamu, mahasiswa pascasarjana, dan pertukaran internasional.',
                'sample_terisi' => 255,
            ],
            [
                'kode' => 'GD-UNAND-OR',
                'nama' => 'Gedung Orange',
                'peruntukan' => 'putri',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Barat',
                'jumlah_lantai' => 3,
                'estimasi_kamar' => 63,
                'kapasitas_bed' => 250,
                'fasilitas' => ['Dapur / Pantry', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN'],
                'deskripsi' => 'Klaster hunian mahasiswi reguler di area kampus selatan.',
                'sample_terisi' => 205,
            ],
            [
                'kode' => 'GD-UNAND-PT',
                'nama' => 'Gedung Pupera Putra',
                'peruntukan' => 'putra',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Barat',
                'jumlah_lantai' => 3,
                'estimasi_kamar' => 63,
                'kapasitas_bed' => 250,
                'fasilitas' => ['Musholla Terpadu', 'Parkir Berkanopi', 'CCTV 24 Jam Aktif'],
                'deskripsi' => 'Asrama prioritas mahasiswa putra penerima beasiswa KIP-Kuliah.',
                'sample_terisi' => 200,
            ],
            [
                'kode' => 'GD-UNAND-HJ',
                'nama' => 'Gedung Hijau',
                'peruntukan' => 'putri',
                'status' => 'aktif',
                'klaster_wilayah' => 'Sektor Kampus Limau Manis - Sayap Timur (Pusat Hunian)',
                'jumlah_lantai' => 3,
                'estimasi_kamar' => 50,
                'kapasitas_bed' => 200,
                'fasilitas' => ['Dapur / Pantry', 'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN'],
                'deskripsi' => 'Hunian mahasiswi afirmasi di kawasan hijau agrikultur kampus.',
                'sample_terisi' => 156,
            ],
            [
                'kode' => 'GD-UNAND-AK',
                'nama' => 'Gedung Akes',
                'peruntukan' => 'tamu',
                'status' => 'aktif',
                'klaster_wilayah' => 'Kampus Jati Padang (Fakultas Kedokteran & Keperawatan)',
                'jumlah_lantai' => 3,
                'estimasi_kamar' => 25,
                'kapasitas_bed' => 100,
                'fasilitas' => ['CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN', 'Sistem Hydrant'],
                'deskripsi' => 'Wisma hunian mahasiswa ko-as dan tamu medis di kompleks kesehatan.',
                'sample_terisi' => 72,
            ],
        ];

        foreach ($gedungs as $item) {
            $sampleTerisi = $item['sample_terisi'];
            unset($item['sample_terisi']);

            $gedung = Gedung::firstOrCreate(
                ['kode' => $item['kode']],
                $item
            );

            if ($gedung->kamars()->count() === 0) {
                $kamarCount = 4;
                $kapasitasPerKamar = (int) floor($gedung->kapasitas_bed / $kamarCount);
                $sisaKapasitas = $gedung->kapasitas_bed % $kamarCount;
                $remainingTerisi = $sampleTerisi;

                for ($i = 1; $i <= $kamarCount; $i++) {
                    $kap = $kapasitasPerKamar + ($i === 1 ? $sisaKapasitas : 0);
                    $terisi = min($kap, $remainingTerisi);
                    $remainingTerisi = max(0, $remainingTerisi - $terisi);

                    $status = $i === 4 && $gedung->kode === 'GD-UNAND-RSN'
                        ? 'perbaikan'
                        : ($terisi >= $kap ? 'penuh' : 'tersedia');

                    Kamar::create([
                        'gedung_id' => $gedung->id,
                        'nomor_kamar' => sprintf('%d0%d', $i, $i),
                        'lantai' => $i,
                        'tipe_kamar' => $gedung->peruntukan === 'tamu' ? 'Wisma' : 'Reguler',
                        'kapasitas_bed' => $kap,
                        'bed_terisi' => $terisi,
                        'status' => $status,
                    ]);
                }
            }
        }
    }
}
