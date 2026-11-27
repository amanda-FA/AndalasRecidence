<?php

namespace App\Http\Controllers\Kerusakan;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\Pengguna;
use App\Models\PenugasanPerbaikan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class KinerjaTeknisiController extends Controller
{
    /**
     * Menampilkan dashboard evaluasi kinerja teknisi untuk Pimpinan.
     */
    public function index(Request $request): View
    {
        // Ambil daftar teknisi dari database
        $roleTeknisi = Role::where('nama_role', 'Teknisi')->first();
        $dbTeknisi = $roleTeknisi 
            ? Pengguna::where('id_role', $roleTeknisi->id_role)->get() 
            : collect();

        // Data kinerja tim teknisi
        $teknisiList = collect([
            [
                'id_pengguna' => 4,
                'nama_lengkap' => 'Teknisi Pemeliharaan (Budi Santoso)',
                'spesialisasi' => 'Mekanikal & Elektrikal (AC & Listrik)',
                'kontak' => '0812-3456-7804',
                'wilayah_tugas' => 'Klaster Asrama Putra (Menpera & Rasuna Said)',
                'total_tiket' => 28,
                'selesai' => 25,
                'dalam_proses' => 3,
                'kepatuhan_sla' => 96.4,
                'rata_waktu' => '14 Jam',
                'status_tugas' => 'Sedang di Lapangan',
                'status_class' => 'bg-[#fff3df] text-[#99620a]',
                'riwayat_tiket' => [
                    [
                        'nomor_tiket' => 'TKT-2026-0101',
                        'jenis_kerusakan' => 'AC Split 1 PK (Bocor & Berisik)',
                        'lokasi' => 'Gedung Menpera (D) - Kamar 204',
                        'tanggal_lapor' => '26 Nov 2026',
                        'tanggal_selesai' => 'Dalam Pengerjaan',
                        'status' => 'Proses',
                        'status_badge' => 'bg-[#fff3df] text-[#99620a]',
                    ],
                    [
                        'nomor_tiket' => 'TKT-2026-0091',
                        'jenis_kerusakan' => 'Kran Wastafel & Pipa Saluran',
                        'lokasi' => 'Asrama Putra Rasuna Said - Kamar 201',
                        'tanggal_lapor' => '18 Nov 2026',
                        'tanggal_selesai' => '19 Nov 2026',
                        'status' => 'Selesai',
                        'status_badge' => 'bg-success-container text-success',
                    ],
                    [
                        'nomor_tiket' => 'TKT-2026-0087',
                        'jenis_kerusakan' => 'Lampu LED Kamar Mandi',
                        'lokasi' => 'Gedung Menpera (D) - Lantai 1',
                        'tanggal_lapor' => '16 Nov 2026',
                        'tanggal_selesai' => '17 Nov 2026',
                        'status' => 'Selesai',
                        'status_badge' => 'bg-success-container text-success',
                    ],
                ],
            ],
            [
                'id_pengguna' => 12,
                'nama_lengkap' => 'Hendri Kurniawan',
                'spesialisasi' => 'Sanitasi & Perpipaan (Plumbing)',
                'kontak' => '0813-8899-7711',
                'wilayah_tugas' => 'Klaster Asrama Putri & Fasilitas Umum',
                'total_tiket' => 22,
                'selesai' => 20,
                'dalam_proses' => 2,
                'kepatuhan_sla' => 91.0,
                'rata_waktu' => '18 Jam',
                'status_tugas' => 'Tersedia',
                'status_class' => 'bg-success-container text-success',
                'riwayat_tiket' => [
                    [
                        'nomor_tiket' => 'TKT-2026-0102',
                        'jenis_kerusakan' => 'Wastafel Koridor Bersama Patah',
                        'lokasi' => 'Gedung Menpera (D) - Lantai 3',
                        'tanggal_lapor' => '25 Nov 2026',
                        'tanggal_selesai' => 'Tertunda (Menunggu Part)',
                        'status' => 'Tertunda',
                        'status_badge' => 'bg-error-container text-error',
                    ],
                    [
                        'nomor_tiket' => 'TKT-2026-0089',
                        'jenis_kerusakan' => 'Saluran Air Buang Kamar Mandi Mampet',
                        'lokasi' => 'Asrama Putri - Kamar 115',
                        'tanggal_lapor' => '17 Nov 2026',
                        'tanggal_selesai' => '18 Nov 2026',
                        'status' => 'Selesai',
                        'status_badge' => 'bg-success-container text-success',
                    ],
                ],
            ],
            [
                'id_pengguna' => 13,
                'nama_lengkap' => 'Agus Priyanto',
                'spesialisasi' => 'Pertukangan, Pintu & Fasilitas Kamar',
                'kontak' => '0852-4455-6677',
                'wilayah_tugas' => 'Seluruh Gedung Asrama (Mobile)',
                'total_tiket' => 19,
                'selesai' => 18,
                'dalam_proses' => 1,
                'kepatuhan_sla' => 94.7,
                'rata_waktu' => '16 Jam',
                'status_tugas' => 'Tersedia',
                'status_class' => 'bg-success-container text-success',
                'riwayat_tiket' => [
                    [
                        'nomor_tiket' => 'TKT-2026-0098',
                        'jenis_kerusakan' => 'Pintu Kamar & Silinder Kunci Patah',
                        'lokasi' => 'Gedung Menpera (D) - Kamar 212',
                        'tanggal_lapor' => '23 Nov 2026',
                        'tanggal_selesai' => '24 Nov 2026',
                        'status' => 'Selesai',
                        'status_badge' => 'bg-success-container text-success',
                    ],
                    [
                        'nomor_tiket' => 'TKT-2026-0095',
                        'jenis_kerusakan' => 'Engsel Jendela Kamar Longgar',
                        'lokasi' => 'Gedung Menpera (D) - Kamar 102',
                        'tanggal_lapor' => '20 Nov 2026',
                        'tanggal_selesai' => '21 Nov 2026',
                        'status' => 'Selesai',
                        'status_badge' => 'bg-success-container text-success',
                    ],
                ],
            ],
        ]);

        // Ringkasan KPI Kinerja
        $kpi = [
            'total_teknisi' => $teknisiList->count(),
            'total_diselesaikan' => $teknisiList->sum('selesai'),
            'rata_rata_sla' => round($teknisiList->avg('kepatuhan_sla'), 1),
            'rata_rata_waktu' => '16 Jam',
        ];

        // Log Riwayat Perbaikan Terkini
        $riwayatPekerjaan = collect([
            [
                'nomor_tiket' => 'TKT-2026-0098',
                'teknisi_nama' => 'Agus Priyanto',
                'fasilitas' => 'Pintu Kamar & Silinder Kunci',
                'lokasi' => 'Gedung Menpera (D) - Kamar 212',
                'tanggal_selesai' => '24 Nov 2026',
                'durasi' => '22 Jam',
                'status_sla' => 'Tepat Waktu',
            ],
            [
                'nomor_tiket' => 'TKT-2026-0095',
                'teknisi_nama' => 'Agus Priyanto',
                'fasilitas' => 'Jendela Kamar',
                'lokasi' => 'Gedung Menpera (D) - Kamar 102',
                'tanggal_selesai' => '21 Nov 2026',
                'durasi' => '18 Jam',
                'status_sla' => 'Tepat Waktu',
            ],
            [
                'nomor_tiket' => 'TKT-2026-0091',
                'teknisi_nama' => 'Teknisi Pemeliharaan (Budi Santoso)',
                'fasilitas' => 'Kran Wastafel & Pipa Saluran',
                'lokasi' => 'Asrama Putra Rasuna Said - Kamar 201',
                'tanggal_selesai' => '19 Nov 2026',
                'durasi' => '8 Jam',
                'status_sla' => 'Tepat Waktu',
            ],
            [
                'nomor_tiket' => 'TKT-2026-0087',
                'teknisi_nama' => 'Teknisi Pemeliharaan (Budi Santoso)',
                'fasilitas' => 'Lampu LED Kamar Mandi',
                'lokasi' => 'Gedung Menpera (D) - Lantai 1',
                'tanggal_selesai' => '17 Nov 2026',
                'durasi' => '12 Jam',
                'status_sla' => 'Tepat Waktu',
            ],
        ]);

        return view('pimpinan.kinerja-teknisi', [
            'teknisiList' => $teknisiList,
            'kpi' => $kpi,
            'riwayatPekerjaan' => $riwayatPekerjaan,
        ]);
    }
}
