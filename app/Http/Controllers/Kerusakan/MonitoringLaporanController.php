<?php

namespace App\Http\Controllers\Kerusakan;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\LaporanKerusakan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MonitoringLaporanController extends Controller
{
    /**
     * Menampilkan dashboard pemantauan laporan kerusakan fasilitas untuk Pimpinan.
     */
    public function index(Request $request): View
    {
        $gedungs = Gedung::orderBy('nama_gedung')->get();

        $query = LaporanKerusakan::with([
            'pengguna.client',
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'penugasanPerbaikans.pengguna',
        ])->orderByDesc('id_laporan');

        $dbReports = $query->get();

        $reports = $dbReports->map(function ($report) {
            $fasilitas = $report->fasilitas;
            $kamar = $fasilitas?->kamar;
            $gedung = $fasilitas?->gedung ?? $kamar?->gedung;
            $pelapor = $report->pengguna;
            $penugasan = $report->penugasanPerbaikans->first();
            $tanggal = $report->tanggal_lapor ? Carbon::parse($report->tanggal_lapor) : Carbon::now();

            $statusRaw = strtolower($report->status_perbaikan);
            $statusNormalized = match ($statusRaw) {
                'diajukan', 'menunggu' => 'diajukan',
                'dalam_pengerjaan', 'diproses', 'ditugaskan' => 'dalam_pengerjaan',
                'tertunda' => 'tertunda',
                'selesai' => 'selesai',
                default => $statusRaw,
            };

            $statusLabel = match ($statusNormalized) {
                'diajukan' => 'Menunggu',
                'dalam_pengerjaan' => 'Dalam Pengerjaan',
                'tertunda' => 'Tertunda',
                'selesai' => 'Selesai',
                default => ucfirst($statusRaw),
            };

            $statusBadge = match ($statusNormalized) {
                'diajukan' => 'bg-surface-container text-secondary',
                'dalam_pengerjaan' => 'bg-[#fff3df] text-[#99620a]',
                'tertunda' => 'bg-error-container text-error',
                'selesai' => 'bg-success-container text-success',
                default => 'bg-surface-container text-on-surface-variant',
            };

            return [
                'id_laporan' => $report->id_laporan,
                'nomor_tiket' => 'TKT-' . $tanggal->format('Y') . '-' . str_pad($report->id_laporan, 4, '0', STR_PAD_LEFT),
                'tanggal_lapor' => $tanggal->locale('id')->translatedFormat('d M Y'),
                'fasilitas_nama' => $fasilitas?->nama_fasilitas ?? 'Fasilitas Asrama',
                'deskripsi' => $report->deskripsi,
                'gedung_nama' => $gedung?->nama_gedung ?? 'Gedung Menpera (D)',
                'kamar_nomor' => $kamar ? 'Kamar ' . $kamar->nomor_kamar : 'Fasilitas Bersama',
                'pelapor_nama' => $pelapor?->nama_lengkap ?? 'Mahasiswa Penghuni',
                'kontak_pelapor' => $pelapor?->no_hp ?? '0812-7788-9900',
                'teknisi_nama' => $penugasan?->pengguna?->nama_lengkap ?? 'Teknisi Pemeliharaan',
                'status_perbaikan' => $statusNormalized,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'sla_target' => '24 Jam sejak pelaporan',
                'catatan_penanganan' => $penugasan?->catatan ?? 'Pemeriksaan awal komponen unit dan kesiapan suku cadang.',
                'kebutuhan_suku_cadang' => 'Komponen standar pemeliharaan asrama',
                'linimasa' => [
                    ['tahap' => 'Laporan Diterima', 'waktu' => $tanggal->format('d M Y, H:i') . ' WIB', 'status' => 'selesai'],
                    ['tahap' => 'Penugasan Teknisi', 'waktu' => $tanggal->addHours(2)->format('d M Y, H:i') . ' WIB', 'status' => 'selesai'],
                    ['tahap' => 'Pengerjaan Fisik', 'waktu' => $statusNormalized !== 'diajukan' ? $tanggal->addHours(4)->format('d M Y, H:i') . ' WIB' : 'Menunggu antrean', 'status' => $statusNormalized !== 'diajukan' ? 'selesai' : 'tertunda'],
                    ['tahap' => 'Verifikasi Selesai', 'waktu' => $statusNormalized === 'selesai' ? $tanggal->addHours(18)->format('d M Y, H:i') . ' WIB' : 'Belum selesai', 'status' => $statusNormalized === 'selesai' ? 'selesai' : 'menunggu'],
                ],
            ];
        });

        // Data sampel representatif jika tabel masih kosong
        if ($reports->isEmpty()) {
            $reports = collect([
                [
                    'id_laporan' => 101,
                    'nomor_tiket' => 'TKT-2026-0101',
                    'tanggal_lapor' => '26 Nov 2026',
                    'fasilitas_nama' => 'AC Split 1 PK',
                    'deskripsi' => 'Unit pendingin ruangan kamar 204 meneteskan air ke meja belajar.',
                    'gedung_nama' => 'Gedung Menpera (D)',
                    'kamar_nomor' => 'Kamar 204',
                    'pelapor_nama' => 'Muhammad Fadhil',
                    'kontak_pelapor' => '0812-3456-7890',
                    'teknisi_nama' => 'Teknisi Pemeliharaan (Budi Santoso)',
                    'status_perbaikan' => 'dalam_pengerjaan',
                    'status_label' => 'Dalam Pengerjaan',
                    'status_badge' => 'bg-[#fff3df] text-[#99620a]',
                    'sla_target' => '27 Nov 2026, 16:00 WIB (SLA 24 Jam)',
                    'catatan_penanganan' => 'Talang drainase tersumbat debu tebal, sedang proses flushing pipa pembuangan dan pembersihan evaporator.',
                    'kebutuhan_suku_cadang' => 'Selang fleksibel drain 1.5 meter',
                    'linimasa' => [
                        ['tahap' => 'Laporan Diterima', 'waktu' => '26 Nov 2026, 09:15 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Penugasan Teknisi', 'waktu' => '26 Nov 2026, 10:00 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Pengerjaan Fisik', 'waktu' => '26 Nov 2026, 14:30 WIB', 'status' => 'proses'],
                        ['tahap' => 'Verifikasi Selesai', 'waktu' => 'Target: 27 Nov 2026', 'status' => 'menunggu'],
                    ],
                ],
                [
                    'id_laporan' => 102,
                    'nomor_tiket' => 'TKT-2026-0102',
                    'tanggal_lapor' => '25 Nov 2026',
                    'fasilitas_nama' => 'Wastafel Koridor Bersama',
                    'deskripsi' => 'Kran air patah dan mengalir terus di lorong lantai 3.',
                    'gedung_nama' => 'Gedung Menpera (D)',
                    'kamar_nomor' => 'Fasilitas Bersama',
                    'pelapor_nama' => 'Ahmad Rizky Pratama',
                    'kontak_pelapor' => '0821-6543-2109',
                    'teknisi_nama' => 'Hendri Kurniawan',
                    'status_perbaikan' => 'tertunda',
                    'status_label' => 'Tertunda',
                    'status_badge' => 'bg-error-container text-error',
                    'sla_target' => '26 Nov 2026, 12:00 WIB (Tertunda)',
                    'catatan_penanganan' => 'Stop kran utama jalur sementara ditutup. Menunggu kedatangan kran leher angsa stainless dari gudang logistik.',
                    'kebutuhan_suku_cadang' => '1 Unit Kran Angsa 1/2 inch Stainless Steel 304, Seal tape pipa',
                    'linimasa' => [
                        ['tahap' => 'Laporan Diterima', 'waktu' => '25 Nov 2026, 08:30 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Pemeriksaan Awal', 'waktu' => '25 Nov 2026, 11:00 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Tertunda (Menunggu Part)', 'waktu' => '25 Nov 2026, 13:00 WIB', 'status' => 'tertunda'],
                        ['tahap' => 'Pemasangan Lanjutan', 'waktu' => 'Estimasi tiba: 28 Nov 2026', 'status' => 'menunggu'],
                    ],
                ],
                [
                    'id_laporan' => 103,
                    'nomor_tiket' => 'TKT-2026-0103',
                    'tanggal_lapor' => '27 Nov 2026',
                    'fasilitas_nama' => 'Lampu LED Plafon 18W',
                    'deskripsi' => 'Lampu utama kamar tidur berkedip dan redup.',
                    'gedung_nama' => 'Asrama Putra Rasuna Said',
                    'kamar_nomor' => 'Kamar 108',
                    'pelapor_nama' => 'Farhan Maulana',
                    'kontak_pelapor' => '0852-1122-3344',
                    'teknisi_nama' => 'Teknisi Pemeliharaan (Budi Santoso)',
                    'status_perbaikan' => 'diajukan',
                    'status_label' => 'Menunggu',
                    'status_badge' => 'bg-surface-container text-secondary',
                    'sla_target' => '28 Nov 2026, 10:00 WIB (SLA 24 Jam)',
                    'catatan_penanganan' => 'Tiket baru masuk dari penghuni, menunggu konfirmasi teknisi piket.',
                    'kebutuhan_suku_cadang' => 'Lampu LED Bulb 18 Watt E27 Cool Daylight',
                    'linimasa' => [
                        ['tahap' => 'Laporan Diterima', 'waktu' => '27 Nov 2026, 07:45 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Antrean Penugasan', 'waktu' => 'Dalam antrean verifikasi', 'status' => 'proses'],
                        ['tahap' => 'Pengerjaan Fisik', 'waktu' => '-', 'status' => 'menunggu'],
                        ['tahap' => 'Verifikasi Selesai', 'waktu' => '-', 'status' => 'menunggu'],
                    ],
                ],
                [
                    'id_laporan' => 104,
                    'nomor_tiket' => 'TKT-2026-0098',
                    'tanggal_lapor' => '23 Nov 2026',
                    'fasilitas_nama' => 'Pintu Kamar & Silinder Kunci',
                    'deskripsi' => 'Silinder kunci aus dan anak kunci patah di dalam.',
                    'gedung_nama' => 'Gedung Menpera (D)',
                    'kamar_nomor' => 'Kamar 212',
                    'pelapor_nama' => 'Bayu Nugraha',
                    'kontak_pelapor' => '0877-9988-1122',
                    'teknisi_nama' => 'Agus Priyanto',
                    'status_perbaikan' => 'selesai',
                    'status_label' => 'Selesai',
                    'status_badge' => 'bg-success-container text-success',
                    'sla_target' => '24 Nov 2026, 17:00 WIB (Tercapai)',
                    'catatan_penanganan' => 'Silinder kunci lama telah diekstraksi dan diganti dengan silinder kuningan baru. 3 anak kunci duplikat telah diserahkan kepada penghuni.',
                    'kebutuhan_suku_cadang' => '1 Set Silinder Kunci Pintu 60mm Brass + 3 Kunci',
                    'linimasa' => [
                        ['tahap' => 'Laporan Diterima', 'waktu' => '23 Nov 2026, 14:10 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Penugasan Teknisi', 'waktu' => '23 Nov 2026, 15:00 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Penggantian Part', 'waktu' => '24 Nov 2026, 10:30 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Verifikasi Selesai', 'waktu' => '24 Nov 2026, 11:45 WIB', 'status' => 'selesai'],
                    ],
                ],
                [
                    'id_laporan' => 105,
                    'nomor_tiket' => 'TKT-2026-0095',
                    'tanggal_lapor' => '20 Nov 2026',
                    'fasilitas_nama' => 'Jendela Kamar',
                    'deskripsi' => 'Engsel jendela longgar dan baut penahan terlepas.',
                    'gedung_nama' => 'Gedung Menpera (D)',
                    'kamar_nomor' => 'Kamar 102',
                    'pelapor_nama' => 'Doni Irawan',
                    'kontak_pelapor' => '0813-2233-4455',
                    'teknisi_nama' => 'Agus Priyanto',
                    'status_perbaikan' => 'selesai',
                    'status_label' => 'Selesai',
                    'status_badge' => 'bg-success-container text-success',
                    'sla_target' => '21 Nov 2026, 16:00 WIB (Tercapai)',
                    'catatan_penanganan' => 'Pemasangan fisher dan sekrup penahan baru pada kusen aluminium jendela. Jendela dapat membuka dan menutup dengan rapat.',
                    'kebutuhan_suku_cadang' => 'Sekrup tapping 4 buah, Fischer S6',
                    'linimasa' => [
                        ['tahap' => 'Laporan Diterima', 'waktu' => '20 Nov 2026, 11:00 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Penugasan Teknisi', 'waktu' => '20 Nov 2026, 13:00 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Perbaikan Engsel', 'waktu' => '21 Nov 2026, 09:30 WIB', 'status' => 'selesai'],
                        ['tahap' => 'Verifikasi Selesai', 'waktu' => '21 Nov 2026, 10:15 WIB', 'status' => 'selesai'],
                    ],
                ],
            ]);
        }

        // Metrik Eksekutif
        $total = $reports->count();
        $selesai = $reports->where('status_perbaikan', 'selesai')->count();
        $dalamPengerjaan = $reports->where('status_perbaikan', 'dalam_pengerjaan')->count();
        $tertunda = $reports->where('status_perbaikan', 'tertunda')->count();
        $menunggu = $reports->where('status_perbaikan', 'diajukan')->count();
        $persenSelesai = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        $kpi = [
            'total_laporan' => $total,
            'selesai' => $selesai,
            'dalam_pengerjaan' => $dalamPengerjaan,
            'tertunda' => $tertunda,
            'menunggu' => $menunggu,
            'persen_selesai' => $persenSelesai,
        ];

        // Rekapitulasi per Gedung
        $rekapGedung = $gedungs->map(function ($g) use ($reports) {
            $buildingReports = $reports->filter(fn ($r) => str_contains(strtolower($r['gedung_nama']), strtolower($g->nama_gedung)));
            $bTotal = $buildingReports->count();
            $bSelesai = $buildingReports->where('status_perbaikan', 'selesai')->count();
            $bProses = $buildingReports->whereIn('status_perbaikan', ['dalam_pengerjaan', 'tertunda', 'diajukan'])->count();
            $bRate = $bTotal > 0 ? round(($bSelesai / $bTotal) * 100) : 100;

            return [
                'nama_gedung' => $g->nama_gedung,
                'total' => $bTotal,
                'selesai' => $bSelesai,
                'dalam_proses' => $bProses,
                'tingkat_selesai' => $bRate,
            ];
        });

        return view('pimpinan.monitoring-laporan', [
            'reports' => $reports,
            'gedungs' => $gedungs,
            'kpi' => $kpi,
            'rekapGedung' => $rekapGedung,
        ]);
    }
}
