<?php

namespace Tests\Feature;

use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class PimpinanMonitoringFrontendTest extends TestCase
{
    private function defaultViewData(array $extra = []): array
    {
        return array_merge([
            'errors' => new ViewErrorBag(),
        ], $extra);
    }

    /**
     * Test 1: Memastikan view Monitoring Kerusakan untuk Pimpinan terkompilasi dan memuat metrik serta tabel.
     */
    public function test_pimpinan_monitoring_kerusakan_view_compiles_and_renders_metrics_and_table(): void
    {
        $dummyReports = collect([
            [
                'id_laporan' => 101,
                'nomor_tiket' => 'TKT-2026-0101',
                'tanggal_lapor' => '26 Nov 2026',
                'fasilitas_nama' => 'AC Split 1 PK',
                'deskripsi' => 'Unit pendingin ruangan kamar 204 meneteskan air.',
                'gedung_nama' => 'Gedung Menpera (D)',
                'kamar_nomor' => 'Kamar 204',
                'pelapor_nama' => 'Muhammad Fadhil',
                'teknisi_nama' => 'Teknisi Pemeliharaan',
                'status_perbaikan' => 'dalam_pengerjaan',
                'status_label' => 'Dalam Pengerjaan',
                'status_badge' => 'bg-[#fff3df] text-[#99620a]',
            ],
        ]);

        $dummyKpi = [
            'total_laporan' => 1,
            'selesai' => 0,
            'dalam_pengerjaan' => 1,
            'tertunda' => 0,
            'menunggu' => 0,
            'persen_selesai' => 0,
        ];

        $dummyRekap = collect([
            [
                'nama_gedung' => 'Gedung Menpera (D)',
                'total' => 1,
                'selesai' => 0,
                'dalam_proses' => 1,
                'tingkat_selesai' => 0,
            ],
        ]);

        $view = $this->view('pimpinan.monitoring-laporan', $this->defaultViewData([
            'reports' => $dummyReports,
            'gedungs' => collect([]),
            'kpi' => $dummyKpi,
            'rekapGedung' => $dummyRekap,
        ]));

        $view->assertSee('Monitoring Kerusakan Fasilitas');
        $view->assertSee('Total Laporan');
        $view->assertSee('Selesai Diperbaiki');
        $view->assertSee('Sedang Dikerjakan');
        $view->assertSee('Distribusi Penanganan per Gedung');
        $view->assertSee('Daftar Laporan Terkini');
        $view->assertSee('TKT-2026-0101');
        $view->assertSee('Gedung Menpera (D)');
        $view->assertSee('Lihat');
        $view->assertSee('modal-detail-laporan');
    }

    /**
     * Test 2: Memastikan view Kinerja Teknisi untuk Pimpinan terkompilasi dan memuat tabel kinerja serta log.
     */
    public function test_pimpinan_kinerja_teknisi_view_compiles_and_renders_kpi_and_tables(): void
    {
        $dummyTeknisiList = collect([
            [
                'id_pengguna' => 4,
                'nama_lengkap' => 'Teknisi Pemeliharaan (Budi Santoso)',
                'spesialisasi' => 'Mekanikal & Elektrikal (AC & Listrik)',
                'kontak' => '0812-3456-7804',
                'total_tiket' => 28,
                'selesai' => 25,
                'dalam_proses' => 3,
                'kepatuhan_sla' => 96.4,
                'rata_waktu' => '14 Jam',
                'status_tugas' => 'Sedang di Lapangan',
                'status_class' => 'bg-[#fff3df] text-[#99620a]',
            ],
        ]);

        $dummyKpi = [
            'total_teknisi' => 1,
            'total_diselesaikan' => 25,
            'rata_rata_sla' => 96.4,
            'rata_rata_waktu' => '14 Jam',
        ];

        $dummyRiwayat = collect([
            [
                'nomor_tiket' => 'TKT-2026-0098',
                'teknisi_nama' => 'Teknisi Pemeliharaan (Budi Santoso)',
                'fasilitas' => 'Pintu Kamar & Silinder Kunci',
                'lokasi' => 'Gedung Menpera (D) - Kamar 212',
                'tanggal_selesai' => '24 Nov 2026',
                'durasi' => '22 Jam',
                'status_sla' => 'Tepat Waktu',
            ],
        ]);

        $view = $this->view('pimpinan.kinerja-teknisi', $this->defaultViewData([
            'teknisiList' => $dummyTeknisiList,
            'kpi' => $dummyKpi,
            'riwayatPekerjaan' => $dummyRiwayat,
        ]));

        $view->assertSee('Monitoring Kinerja Teknisi');
        $view->assertSee('Total Teknisi Aktif');
        $view->assertSee('Tiket Diselesaikan');
        $view->assertSee('Kepatuhan SLA Rata-rata');
        $view->assertSee('Tabel Kinerja Tim Teknisi');
        $view->assertSee('Riwayat Pekerjaan Selesai Terkini');
        $view->assertSee('Budi Santoso');
        $view->assertSee('96.4%');
        $view->assertSee('Lihat Detail');
        $view->assertSee('modal-detail-teknisi');
    }
}
