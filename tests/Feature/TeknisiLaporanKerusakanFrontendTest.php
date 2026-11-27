<?php

namespace Tests\Feature;

use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class TeknisiLaporanKerusakanFrontendTest extends TestCase
{
    private function defaultViewData(array $extra = []): array
    {
        return array_merge([
            'errors' => new ViewErrorBag(),
        ], $extra);
    }

    /**
     * Test 1: Memastikan Blade template halaman Laporan Kerusakan Teknisi terkompilasi dan memuat semua elemen UI utama.
     */
    public function test_teknisi_damage_report_view_compiles_and_renders_ui_elements(): void
    {
        $dummyReports = collect([
            [
                'id_laporan' => 101,
                'nomor_tiket' => 'TKT-2026-0101',
                'tanggal_lapor_raw' => '2026-11-26',
                'tanggal_lapor' => '26 Nov 2026',
                'waktu_lapor' => '08:15 WIB',
                'tanggal_selesai' => '—',
                'judul' => 'AC kamar bocor menetes ke meja belajar dan tidak dingin',
                'deskripsi' => 'Unit pendingin ruangan di kamar 204 mengeluarkan tetesan air cukup deras.',
                'fasilitas_nama' => 'AC Split 1 PK',
                'kategori_fasilitas' => 'Fasilitas Kamar',
                'subkomponen' => 'Pipa Talang Pembuangan Air & Filter Debu Indoor',
                'gedung_id' => 1,
                'gedung_nama' => 'Gedung Menpera (D)',
                'lantai' => 2,
                'kamar_nomor' => 'Kamar 204',
                'lokasi_lengkap' => 'Gedung Menpera (D), Lantai 2 (Kamar 204)',
                'pelapor_nama' => 'Muhammad Fadhil',
                'pelapor_identitas' => 'NIM. 2111522001',
                'pelapor_jurusan' => 'S1 Sistem Informasi',
                'pelapor_telepon' => '0812-7788-9900',
                'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
                'teknisi_nama' => 'Teknisi Pemeliharaan',
                'urgensi' => 'Darurat',
                'urgensi_color' => 'bg-error-container text-error border-error/20',
                'sla_jam' => 4,
                'sla_target' => 'Maks. 4 Jam (Respon Cepat)',
                'sla_deadline' => '26 Nov 2026, 12:15 WIB',
                'is_overdue' => false,
                'status_perbaikan' => 'dalam_pengerjaan',
                'status_badge' => ['label' => 'Dalam Pengerjaan', 'class' => 'bg-[#fff3df] text-[#99620a] border border-[#df9b2e]/30', 'icon' => 'build', 'step' => 3],
                'foto' => '/images/kerusakan/ac-kondisi-awal.png',
                'catatan_teknisi' => 'Sudah dilakukan pembersihan drainase.',
                'suku_cadang' => [
                    ['nama' => 'Pipa Fleksibel Drainase 5/8"', 'qty' => '1.5 Meter', 'status' => 'Tersedia di Gudang'],
                ],
                'linimasa' => [
                    ['tahap' => 'Laporan Diajukan', 'waktu' => '26 Nov 2026, 08:15', 'status' => 'done', 'keterangan' => 'Laporan diajukan.'],
                    ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => '26 Nov 2026, 08:30', 'status' => 'done', 'keterangan' => 'Penugasan teknisi.'],
                    ['tahap' => 'Dalam Pengerjaan', 'waktu' => '26 Nov 2026, 09:00', 'status' => 'current', 'keterangan' => 'Pengerjaan.'],
                    ['tahap' => 'Selesai & BAST', 'waktu' => 'Menunggu', 'status' => 'pending', 'keterangan' => 'Belum.'],
                ],
            ],
        ]);

        $dummyKpi = [
            'total_ditangani' => 1,
            'menunggu' => 0,
            'dalam_pengerjaan' => 1,
            'tertunda' => 0,
            'selesai' => 0,
            'butuh_tindakan_cepat' => 1,
        ];

        $dummyGedungs = collect([
            (object) ['id_gedung' => 1, 'nama_gedung' => 'Gedung Menpera (D)'],
            (object) ['id_gedung' => 2, 'nama_gedung' => 'Asrama Putra Rasuna Said'],
        ]);

        $view = $this->view('teknisi.kerusakan.index', $this->defaultViewData([
            'reports' => $dummyReports,
            'gedungs' => $dummyGedungs,
            'kpi' => $dummyKpi,
            'teknisi' => (object) ['nama_lengkap' => 'Teknisi Pemeliharaan'],
        ]));

        // Cek Judul & Header
        $view->assertSee('Laporan Kerusakan Fasilitas');
        $view->assertSee('Muat Ulang');

        // Cek Metrik KPI Cards
        $view->assertSee('Total Laporan Ditangani');
        $view->assertSee('Menunggu Penanganan');
        $view->assertSee('Dalam Perbaikan Lapangan');
        $view->assertSee('Laporan Tertunda');

        // Cek Navigasi Tab Pengelompokan
        $view->assertSee('Semua Penugasan');
        $view->assertSee('Kebutuhan Tindakan Cepat');
        $view->assertSee('Dalam Pengerjaan');
        $view->assertSee('Riwayat Selesai');

        // Cek Kolom Tabel
        $view->assertSee('ID Tiket');
        $view->assertSee('Tgl Lapor');
        $view->assertSee('Kerusakan &amp; Fasilitas', false);
        $view->assertSee('Lokasi Hunian');
        $view->assertSee('Tingkat Urgensi');
        $view->assertSee('Status Perbaikan');

        // Cek Data Baris
        $view->assertSee('TKT-2026-0101');
        $view->assertSee('AC Split 1 PK');
        $view->assertSee('Muhammad Fadhil');
        $view->assertSee('Gedung Menpera (D)');

        // Cek Bagian Standar Operasional Respon Teknisi (SLA)
        $view->assertSee('Standar Operasional Respon Teknisi (SLA)');
        $view->assertSee('&lt; 4 Jam', false);
        $view->assertSee('&lt; 24 Jam', false);
        $view->assertSee('&lt; 48 Jam (2 Hari)', false);
        $view->assertSee('&lt; 72 Jam (3 Hari)', false);

        // Cek Modal Detail & Tombol Aksi
        $view->assertSee('teknisi-detail-dialog');
        $view->assertSee('Cetak Surat Tugas PK (.PDF)');
        $view->assertSee('Ubah Status Perbaikan');
    }

    /**
     * Test 2: Memastikan Blade template Surat Perintah Kerja (PK) terkompilasi dan memuat format resmi.
     */
    public function test_print_pk_view_compiles_with_official_format(): void
    {
        $dummyReport = [
            'id_laporan' => 101,
            'nomor_tiket' => 'TKT-2026-0101',
            'tanggal_lapor' => '26 Nov 2026',
            'waktu_lapor' => '08:15 WIB',
            'pelapor_nama' => 'Muhammad Fadhil',
            'pelapor_identitas' => 'NIM. 2111522001',
            'pelapor_telepon' => '0812-7788-9900',
            'gedung_nama' => 'Gedung Menpera (D)',
            'lantai' => 2,
            'kamar_nomor' => 'Kamar 204',
            'teknisi_nama' => 'Teknisi Pemeliharaan',
            'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
            'urgensi' => 'Darurat',
            'sla_target' => 'Maks. 4 Jam (Respon Cepat)',
            'fasilitas_nama' => 'AC Split 1 PK',
            'kategori_fasilitas' => 'Fasilitas Kamar',
            'subkomponen' => 'Pipa Talang Pembuangan Air & Filter Debu Indoor',
            'deskripsi' => 'Unit pendingin ruangan di kamar 204 mengeluarkan tetesan air cukup deras.',
            'catatan_teknisi' => 'Sudah dilakukan pembersihan drainase.',
            'suku_cadang' => [
                ['nama' => 'Pipa Fleksibel Drainase 5/8"', 'qty' => '1.5 Meter', 'status' => 'Tersedia di Gudang'],
            ],
        ];

        $view = $this->view('teknisi.kerusakan.print-pk', $this->defaultViewData([
            'report' => $dummyReport,
            'teknisi' => (object) ['nama_lengkap' => 'Teknisi Pemeliharaan'],
            'nomor_spk' => 'SPK/TKN/202611/0101',
        ]));

        $view->assertSee('Surat Perintah Kerja (SPK) Pemeliharaan Fasilitas');
        $view->assertSee('SPK/TKN/202611/0101');
        $view->assertSee('Universitas Andalas');
        $view->assertSee('TKT-2026-0101');
        $view->assertSee('Teknisi Pelaksana');
        $view->assertSee('Fasilitator Asrama');
        $view->assertSee('Mahasiswa / Pelapor');
    }

    /**
     * Test 3: Memastikan Blade template Ubah Status Perbaikan terkompilasi dan memuat formulir serta modal konfirmasi.
     */
    public function test_edit_status_view_compiles_with_form_and_confirmation_modal(): void
    {
        $dummyReport = [
            'id_laporan' => 101,
            'nomor_tiket' => 'TKT-2026-0101',
            'urgensi' => 'Darurat',
            'fasilitas_nama' => 'AC Split 1 PK',
            'deskripsi' => 'Unit pendingin ruangan di kamar 204 mengeluarkan tetesan air.',
            'gedung_nama' => 'Gedung Menpera (D)',
            'lantai' => 2,
            'kamar_nomor' => 'Kamar 204',
            'pelapor_nama' => 'Muhammad Fadhil',
            'pelapor_telepon' => '0812-7788-9900',
            'sla_target' => 'Maks. 4 Jam (Respon Cepat)',
            'status_perbaikan' => 'dalam_pengerjaan',
            'status_badge' => ['label' => 'Dalam Pengerjaan', 'class' => 'bg-[#fff3df] text-[#99620a]'],
            'foto' => null,
            'catatan_teknisi' => 'Sudah dilakukan pembersihan awal.',
            'linimasa' => [
                ['tahap' => 'Laporan Diajukan', 'waktu' => '26 Nov 2026, 08:15', 'status' => 'done', 'keterangan' => 'Laporan diajukan.'],
                ['tahap' => 'Dalam Pengerjaan', 'waktu' => '26 Nov 2026, 09:00', 'status' => 'current', 'keterangan' => 'Pengerjaan.'],
            ],
        ];

        $view = $this->view('teknisi.kerusakan.edit-status', $this->defaultViewData([
            'report' => $dummyReport,
            'teknisi' => (object) ['nama_lengkap' => 'Teknisi Pemeliharaan'],
        ]));

        $view->assertSee('Ubah Status Perbaikan');
        $view->assertSee('TKT-2026-0101');
        $view->assertSee('Pilih Status Perbaikan Terbaru');
        $view->assertSee('Menunggu');
        $view->assertSee('Diproses (Pengerjaan)');
        $view->assertSee('Tertunda');
        $view->assertSee('Selesai Diperbaiki');
        $view->assertSee('Alasan Penundaan');
        $view->assertSee('Catatan Pekerjaan &amp; Pemeriksaan Lapangan', false);
        $view->assertSee('Unggah Bukti Perbaikan');
        $view->assertSee('Konfirmasi Perubahan Status');
        $view->assertSee('Periksa Kembali');
        $view->assertSee('Ya, Simpan Perubahan');
    }
}
