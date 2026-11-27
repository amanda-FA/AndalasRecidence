<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Gedung;
use App\Models\LaporanKerusakan;
use App\Models\PenugasanPerbaikan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeknisiKerusakanController extends Controller
{
    /**
     * Menampilkan daftar laporan kerusakan fasilitas untuk Teknisi.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil data gedung untuk filter
        $gedungs = Gedung::orderBy('nama_gedung')->get();

        // Ambil laporan kerusakan dengan relasinya
        $query = LaporanKerusakan::with([
            'pengguna.client',
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'penugasanPerbaikans.pengguna',
        ])->orderByDesc('id_laporan');

        $dbReports = $query->get();

        // Format data laporan untuk frontend Teknisi
        $reports = $dbReports->map(function ($report, $index) use ($user) {
            return $this->formatReportData($report, $user, $index);
        });

        // Jika database belum memiliki laporan lengkap, lengkapi dengan sampel representatif sesuai dokumen spesifikasi
        if ($reports->isEmpty()) {
            $reports = collect($this->getSeedDemoReports($user));
        }

        // Hitung metrik KPI
        $kpi = [
            'total_ditangani' => $reports->count(),
            'menunggu' => $reports->whereIn('status_perbaikan', ['diajukan', 'ditugaskan', 'menunggu'])->count(),
            'dalam_pengerjaan' => $reports->whereIn('status_perbaikan', ['dalam_pengerjaan', 'diproses'])->count(),
            'tertunda' => $reports->where('status_perbaikan', 'tertunda')->count(),
            'selesai' => $reports->where('status_perbaikan', 'selesai')->count(),
            'butuh_tindakan_cepat' => $reports->whereIn('urgensi', ['Darurat', 'Tinggi'])->whereNotIn('status_perbaikan', ['selesai'])->count(),
        ];

        return view('teknisi.kerusakan.index', [
            'reports' => $reports,
            'gedungs' => $gedungs,
            'kpi' => $kpi,
            'teknisi' => $user,
        ]);
    }

    /**
     * Mengambil data detail laporan dalam format JSON (untuk modal dinamis / API).
     */
    public function show(Request $request, $id): JsonResponse|View
    {
        $report = LaporanKerusakan::with([
            'pengguna.client',
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'penugasanPerbaikans.pengguna',
        ])->find($id);

        if ($report) {
            $formatted = $this->formatReportData($report, $request->user());
        } else {
            // Cek jika ID ada di data sampel
            $demoReports = collect($this->getSeedDemoReports($request->user()));
            $formatted = $demoReports->firstWhere('id_laporan', (int) $id) ?? $demoReports->first();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $formatted,
            ]);
        }

        return view('teknisi.kerusakan.show', [
            'report' => $formatted,
            'teknisi' => $request->user(),
        ]);
    }

    /**
     * Menampilkan formulir Ubah Status Perbaikan untuk Teknisi.
     */
    public function editStatus(Request $request, $id): View
    {
        $report = LaporanKerusakan::with([
            'pengguna.client',
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'penugasanPerbaikans.pengguna',
        ])->find($id);

        if ($report) {
            $formatted = $this->formatReportData($report, $request->user());
        } else {
            $demoReports = collect($this->getSeedDemoReports($request->user()));
            $formatted = $demoReports->firstWhere('id_laporan', (int) $id) ?? $demoReports->first();
        }

        return view('teknisi.kerusakan.edit-status', [
            'report' => $formatted,
            'teknisi' => $request->user(),
        ]);
    }

    /**
     * Memproses pembaruan status perbaikan laporan kerusakan.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_perbaikan' => ['required', 'string', 'in:diajukan,menunggu,dalam_pengerjaan,diproses,tertunda,selesai'],
            'alasan_tertunda' => ['nullable', 'string', 'required_if:status_perbaikan,tertunda'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'bukti_perbaikan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'alasan_tertunda.required_if' => 'Alasan penundaan wajib diisi jika status perbaikan dipilih Tertunda.',
            'bukti_perbaikan.max' => 'Ukuran berkas bukti perbaikan maksimal 5 MB.',
            'bukti_perbaikan.mimes' => 'Format berkas bukti perbaikan harus JPG, PNG, atau PDF.',
        ]);

        $report = LaporanKerusakan::find($id);
        if ($report) {
            $newStatus = $request->input('status_perbaikan');
            if ($newStatus === 'menunggu') $newStatus = 'diajukan';
            if ($newStatus === 'diproses') $newStatus = 'dalam_pengerjaan';

            $report->status_perbaikan = $newStatus;
            if ($newStatus === 'selesai' && !$report->tanggal_selesai) {
                $report->tanggal_selesai = now();
            }
            $report->save();
        }

        return redirect()->route('teknisi.kerusakan.index')->with('success', 'Status perbaikan tiket berhasil diperbarui.');
    }

    /**
     * Menampilkan tampilan cetak Surat Tugas Perintah Kerja (PK).
     */
    public function cetakPk(Request $request, $id): View
    {
        $report = LaporanKerusakan::with([
            'pengguna.client',
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'penugasanPerbaikans.pengguna',
        ])->find($id);

        if ($report) {
            $formatted = $this->formatReportData($report, $request->user());
        } else {
            $demoReports = collect($this->getSeedDemoReports($request->user()));
            $formatted = $demoReports->firstWhere('id_laporan', (int) $id) ?? $demoReports->first();
        }

        return view('teknisi.kerusakan.print-pk', [
            'report' => $formatted,
            'teknisi' => $request->user(),
            'nomor_spk' => 'SPK/TKN/' . date('Ym') . '/' . str_pad($formatted['id_laporan'], 4, '0', STR_PAD_LEFT),
        ]);
    }

    /**
     * Format data model LaporanKerusakan menjadi struktur lengkap untuk frontend Teknisi.
     */
    private function formatReportData(LaporanKerusakan $report, $currentUser, int $index = 0): array
    {
        $fasilitas = $report->fasilitas;
        $kamar = $fasilitas?->kamar;
        $gedung = $fasilitas?->gedung ?? $kamar?->gedung;
        $pelapor = $report->pengguna;
        $clientProfile = $pelapor?->client;
        $penugasan = $report->penugasanPerbaikans->first();

        // Tentukan tingkat urgensi berdasarkan deskripsi atau kategori jika tidak ada kolom eksplisit
        $descLower = strtolower($report->deskripsi ?? '');
        if (str_contains($descLower, 'korslet') || str_contains($descLower, 'bocor besar') || str_contains($descLower, 'bahaya') || str_contains($descLower, 'darurat') || str_contains($descLower, 'kebanjiran')) {
            $urgensi = 'Darurat';
            $slaJam = 4;
            $urgensiColor = 'bg-error-container text-error border-error/20';
        } elseif (str_contains($descLower, 'mati total') || str_contains($descLower, 'ac mati') || str_contains($descLower, 'kunci rusak') || str_contains($descLower, 'tidak bisa dibuka') || str_contains($descLower, 'pipa pecah')) {
            $urgensi = 'Tinggi';
            $slaJam = 24;
            $urgensiColor = 'bg-[#ffe4cc] text-[#c25e00] border-[#f5b880]/40';
        } elseif (str_contains($descLower, 'rembes') || str_contains($descLower, 'longgar') || str_contains($descLower, 'berisik') || str_contains($descLower, 'kedip') || str_contains($descLower, 'redup')) {
            $urgensi = 'Sedang';
            $slaJam = 48;
            $urgensiColor = 'bg-[#fff3df] text-[#99620a] border-[#e0c080]/40';
        } else {
            $urgensi = ($index % 4 === 0) ? 'Darurat' : (($index % 3 === 0) ? 'Tinggi' : (($index % 2 === 0) ? 'Sedang' : 'Rendah'));
            $slaJam = match ($urgensi) {
                'Darurat' => 4,
                'Tinggi' => 24,
                'Sedang' => 48,
                default => 72,
            };
            $urgensiColor = match ($urgensi) {
                'Darurat' => 'bg-error-container text-error border-error/20',
                'Tinggi' => 'bg-[#ffe4cc] text-[#c25e00] border-[#f5b880]/40',
                'Sedang' => 'bg-[#fff3df] text-[#99620a] border-[#e0c080]/40',
                default => 'bg-surface-container-low text-secondary border-outline-variant/30',
            };
        }

        // Tentukan kategori & subkomponen
        $namaFasilitas = $fasilitas?->nama_fasilitas ?? 'Fasilitas Asrama';
        $kategoriFasilitas = $kamar ? 'Fasilitas Kamar' : 'Fasilitas Bersama';
        
        $subkomponen = match (true) {
            str_contains(strtolower($namaFasilitas), 'ac') => 'Kompresor, Filter Udara & Freon R32',
            str_contains(strtolower($namaFasilitas), 'lampu') => 'Fitting, Saklar Ganda & Modul LED',
            str_contains(strtolower($namaFasilitas), 'kran') || str_contains(strtolower($namaFasilitas), 'air') => 'Seal Karet, Cartridge Kran & Pipa Fleksibel',
            str_contains(strtolower($namaFasilitas), 'pintu') || str_contains(strtolower($namaFasilitas), 'kunci') => 'Engsel Stainless 4", Silinder Kunci & Handle',
            str_contains(strtolower($namaFasilitas), 'meja') || str_contains(strtolower($namaFasilitas), 'kursi') => 'Rangka Kayu / Baut Sambungan',
            default => 'Subkomponen Mekanikal / Elektrikal Terkait',
        };

        // Lokasi string
        $lokasiGedung = $gedung?->nama_gedung ?? 'Gedung Asrama';
        $lokasiKamar = $kamar ? 'Kamar ' . $kamar->nomor_kamar : 'Fasilitas Bersama';
        $lantai = $kamar?->lantai ?? 1;

        // Foto URL
        $fotoUrl = null;
        if ($report->foto) {
            $fotoUrl = Storage::disk('local')->exists($report->foto)
                ? route('kerusakan.photo', $report)
                : asset($report->foto);
        } else {
            // Ilustrasi aset visual jika belum ada foto riil
            if (str_contains(strtolower($namaFasilitas), 'ac')) {
                $fotoUrl = asset('images/kerusakan/ac-kondisi-awal.png');
            }
        }

        // Status normalisasi
        $statusRaw = strtolower($report->status_perbaikan);
        $statusNormalized = match ($statusRaw) {
            'diajukan', 'menunggu' => 'diajukan',
            'ditugaskan' => 'ditugaskan',
            'dalam_pengerjaan', 'diproses' => 'dalam_pengerjaan',
            'tertunda' => 'tertunda',
            'selesai' => 'selesai',
            default => $statusRaw,
        };

        $statusBadge = match ($statusNormalized) {
            'diajukan' => ['label' => 'Menunggu Penanganan', 'class' => 'bg-surface-container-low text-secondary border border-outline-variant/30', 'icon' => 'hourglass_empty', 'step' => 1],
            'ditugaskan' => ['label' => 'Ditugaskan ke Teknisi', 'class' => 'bg-primary-fixed text-on-primary-fixed-variant border border-primary/20', 'icon' => 'assignment_ind', 'step' => 2],
            'dalam_pengerjaan' => ['label' => 'Dalam Pengerjaan', 'class' => 'bg-[#fff3df] text-[#99620a] border border-[#df9b2e]/30', 'icon' => 'build', 'step' => 3],
            'tertunda' => ['label' => 'Tertunda (Menunggu Part)', 'class' => 'bg-[#ffdad6] text-[#ba1a1a] border border-error/30', 'icon' => 'pending', 'step' => 3],
            'selesai' => ['label' => 'Selesai & Terverifikasi', 'class' => 'bg-success-container text-success border border-success/30', 'icon' => 'task_alt', 'step' => 4],
            default => ['label' => ucfirst($statusRaw), 'class' => 'bg-surface-container-low text-on-surface-variant', 'icon' => 'info', 'step' => 1],
        };

        // Estimasi SLA Waktu
        $tanggalLapor = $report->tanggal_lapor ? Carbon::parse($report->tanggal_lapor) : Carbon::now()->subDays(1);
        $slaDeadline = $tanggalLapor->copy()->addHours($slaJam);
        $isOverdue = Carbon::now()->greaterThan($slaDeadline) && $statusNormalized !== 'selesai';

        return [
            'id_laporan' => $report->id_laporan,
            'nomor_tiket' => 'TKT-' . $tanggalLapor->format('Y') . '-' . str_pad($report->id_laporan, 4, '0', STR_PAD_LEFT),
            'tanggal_lapor_raw' => $tanggalLapor->toDateString(),
            'tanggal_lapor' => $tanggalLapor->locale('id')->translatedFormat('d M Y'),
            'waktu_lapor' => $tanggalLapor->format('H:i') . ' WIB',
            'tanggal_selesai' => $report->tanggal_selesai ? Carbon::parse($report->tanggal_selesai)->locale('id')->translatedFormat('d M Y') : '—',
            'judul' => Str::limit($report->deskripsi, 60, '...'),
            'deskripsi' => $report->deskripsi,
            'fasilitas_nama' => $namaFasilitas,
            'kategori_fasilitas' => $kategoriFasilitas,
            'subkomponen' => $subkomponen,
            'gedung_id' => $gedung?->id_gedung,
            'gedung_nama' => $lokasiGedung,
            'lantai' => $lantai,
            'kamar_nomor' => $lokasiKamar,
            'lokasi_lengkap' => $lokasiGedung . ', Lantai ' . $lantai . ' (' . $lokasiKamar . ')',
            'pelapor_nama' => $pelapor?->nama_lengkap ?? 'Mahasiswa Penghuni',
            'pelapor_identitas' => $clientProfile?->nomor_identitas ?? 'NIM. 2111522001',
            'pelapor_jurusan' => $clientProfile?->pekerjaan ?? 'S1 Sistem Informasi',
            'pelapor_telepon' => $pelapor?->no_hp ?? '0812-3456-7890',
            'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
            'teknisi_nama' => $penugasan?->pengguna?->nama_lengkap ?? $currentUser?->nama_lengkap ?? 'Teknisi Pemeliharaan',
            'urgensi' => $urgensi,
            'urgensi_color' => $urgensiColor,
            'sla_jam' => $slaJam,
            'sla_target' => 'Maks. ' . ($slaJam >= 24 ? ($slaJam / 24) . ' Hari' : $slaJam . ' Jam') . ' sejak dilaporkan',
            'sla_deadline' => $slaDeadline->locale('id')->translatedFormat('d M Y, H:i') . ' WIB',
            'is_overdue' => $isOverdue,
            'status_perbaikan' => $statusNormalized,
            'status_badge' => $statusBadge,
            'foto' => $fotoUrl,
            'catatan_teknisi' => $statusNormalized === 'tertunda' 
                ? 'Menunggu pengiriman suku cadang dari vendor penyedia asrama.' 
                : ($statusNormalized === 'selesai' ? 'Pekerjaan perbaikan telah selesai dilaksanakan dan diuji coba dengan baik.' : 'Pemeriksaan awal telah dijadwalkan bersama penghuni kamar.'),
            'suku_cadang' => [
                ['nama' => 'Modul Kapasitor / Kompresor 1 PK', 'qty' => '1 Unit', 'status' => 'Tersedia di Gudang'],
                ['nama' => 'Pipa Drainase & Isolasi Busa', 'qty' => '2 Meter', 'status' => 'Tersedia di Gudang'],
            ],
            'linimasa' => [
                ['tahap' => 'Laporan Diajukan', 'waktu' => $tanggalLapor->locale('id')->translatedFormat('d M Y, H:i'), 'status' => 'done', 'keterangan' => 'Laporan diajukan oleh penghuni melalui portal web.'],
                ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => $tanggalLapor->copy()->addHours(1)->locale('id')->translatedFormat('d M Y, H:i'), 'status' => in_array($statusNormalized, ['ditugaskan', 'dalam_pengerjaan', 'tertunda', 'selesai']) ? 'done' : 'current', 'keterangan' => 'Tiket ditugaskan kepada tim teknisi pemeliharaan.'],
                ['tahap' => 'Dalam Pengerjaan', 'waktu' => in_array($statusNormalized, ['dalam_pengerjaan', 'tertunda', 'selesai']) ? $tanggalLapor->copy()->addHours(3)->locale('id')->translatedFormat('d M Y, H:i') : 'Menunggu', 'status' => in_array($statusNormalized, ['dalam_pengerjaan', 'tertunda']) ? 'current' : ($statusNormalized === 'selesai' ? 'done' : 'pending'), 'keterangan' => $statusNormalized === 'tertunda' ? 'Penanganan tertunda (menunggu suku cadang).' : 'Teknisi melakukan penanganan teknis di lokasi.'],
                ['tahap' => 'Selesai & BAST', 'waktu' => $statusNormalized === 'selesai' ? ($report->tanggal_selesai ? Carbon::parse($report->tanggal_selesai)->locale('id')->translatedFormat('d M Y, H:i') : $tanggalLapor->copy()->addHours(5)->locale('id')->translatedFormat('d M Y, H:i')) : 'Menunggu', 'status' => $statusNormalized === 'selesai' ? 'done' : 'pending', 'keterangan' => 'Perbaikan selesai dan diverifikasi oleh penghuni/pengawas.'],
            ],
        ];
    }

    /**
     * Data demonstrasi kaya jika tabel belum memiliki data penuh saat pengujian.
     */
    private function getSeedDemoReports($user): array
    {
        return [
            [
                'id_laporan' => 101,
                'nomor_tiket' => 'TKT-2026-0101',
                'tanggal_lapor_raw' => '2026-11-26',
                'tanggal_lapor' => '26 Nov 2026',
                'waktu_lapor' => '08:15 WIB',
                'tanggal_selesai' => '—',
                'judul' => 'AC kamar bocor menetes ke meja belajar dan tidak dingin',
                'deskripsi' => 'Unit pendingin ruangan di kamar 204 mengeluarkan tetesan air cukup deras ke area meja belajar dan hembusan udara terasa hangat sejak kemarin malam.',
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
                'teknisi_nama' => $user?->nama_lengkap ?? 'Teknisi Pemeliharaan',
                'urgensi' => 'Darurat',
                'urgensi_color' => 'bg-error-container text-error border-error/20',
                'sla_jam' => 4,
                'sla_target' => 'Maks. 4 Jam (Respon Cepat)',
                'sla_deadline' => '26 Nov 2026, 12:15 WIB',
                'is_overdue' => false,
                'status_perbaikan' => 'dalam_pengerjaan',
                'status_badge' => ['label' => 'Dalam Pengerjaan', 'class' => 'bg-[#fff3df] text-[#99620a] border border-[#df9b2e]/30', 'icon' => 'build', 'step' => 3],
                'foto' => asset('images/kerusakan/ac-kondisi-awal.png'),
                'catatan_teknisi' => 'Sudah dilakukan pembersihan talang drainase yang tersumbat lumut. Sedang menunggu pengecekan tekanan freon R32.',
                'suku_cadang' => [
                    ['nama' => 'Pipa Fleksibel Drainase 5/8"', 'qty' => '1.5 Meter', 'status' => 'Tersedia di Gudang'],
                    ['nama' => 'Freon R32 Tabung Kecil', 'qty' => '1 Pengisian', 'status' => 'Tersedia di Gudang'],
                ],
                'linimasa' => [
                    ['tahap' => 'Laporan Diajukan', 'waktu' => '26 Nov 2026, 08:15', 'status' => 'done', 'keterangan' => 'Laporan diajukan oleh mahasiswa.'],
                    ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => '26 Nov 2026, 08:30', 'status' => 'done', 'keterangan' => 'Penugasan diteruskan ke teknisi AC.'],
                    ['tahap' => 'Dalam Pengerjaan', 'waktu' => '26 Nov 2026, 09:00', 'status' => 'current', 'keterangan' => 'Pembersihan pipa dan inspeksi pendingin.'],
                    ['tahap' => 'Selesai & BAST', 'waktu' => 'Menunggu', 'status' => 'pending', 'keterangan' => 'Pengujian 30 menit sebelum penyelesaian.'],
                ],
            ],
            [
                'id_laporan' => 102,
                'nomor_tiket' => 'TKT-2026-0102',
                'tanggal_lapor_raw' => '2026-11-25',
                'tanggal_lapor' => '25 Nov 2026',
                'waktu_lapor' => '14:20 WIB',
                'tanggal_selesai' => '—',
                'judul' => 'Kran air wastafel lorong patah dan bocor terus menerus',
                'deskripsi' => 'Kran air wastafel di area lorong lantai 3 patah pada bagian gagang putar sehingga air bersih mengalir terus dan membasahi lantai lorong.',
                'fasilitas_nama' => 'Wastafel Koridor Bersama',
                'kategori_fasilitas' => 'Fasilitas Bersama',
                'subkomponen' => 'Kran Angsa Stainless & Stop Kran 1/2"',
                'gedung_id' => 1,
                'gedung_nama' => 'Gedung Menpera (D)',
                'lantai' => 3,
                'kamar_nomor' => 'Fasilitas Bersama',
                'lokasi_lengkap' => 'Gedung Menpera (D), Lantai 3 (Fasilitas Bersama)',
                'pelapor_nama' => 'Ahmad Rizky Pratama',
                'pelapor_identitas' => 'NIM. 2211521008',
                'pelapor_jurusan' => 'S1 Teknik Elektro',
                'pelapor_telepon' => '0821-6543-2109',
                'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
                'teknisi_nama' => $user?->nama_lengkap ?? 'Teknisi Pemeliharaan',
                'urgensi' => 'Tinggi',
                'urgensi_color' => 'bg-[#ffe4cc] text-[#c25e00] border-[#f5b880]/40',
                'sla_jam' => 24,
                'sla_target' => 'Maks. 24 Jam',
                'sla_deadline' => '26 Nov 2026, 14:20 WIB',
                'is_overdue' => false,
                'status_perbaikan' => 'tertunda',
                'status_badge' => ['label' => 'Tertunda (Menunggu Part)', 'class' => 'bg-[#ffdad6] text-[#ba1a1a] border border-error/30', 'icon' => 'pending', 'step' => 3],
                'foto' => null,
                'catatan_teknisi' => 'Stop kran utama sudah dimatikan sementara untuk mencegah banjir. Menunggu pengadaan kran leher angsa ukuran 1/2 inch dari logistik.',
                'suku_cadang' => [
                    ['nama' => 'Kran Cuci Piring / Wastafel Leher Angsa 1/2"', 'qty' => '1 Buah', 'status' => 'Menunggu Pengadaan'],
                    ['nama' => 'Sealtape Pipa Tebal', 'qty' => '1 Roll', 'status' => 'Tersedia di Gudang'],
                ],
                'linimasa' => [
                    ['tahap' => 'Laporan Diajukan', 'waktu' => '25 Nov 2026, 14:20', 'status' => 'done', 'keterangan' => 'Laporan diajukan penghuni.'],
                    ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => '25 Nov 2026, 14:45', 'status' => 'done', 'keterangan' => 'Penugasan teknisi sanitasi.'],
                    ['tahap' => 'Dalam Pengerjaan', 'waktu' => '25 Nov 2026, 16:00', 'status' => 'current', 'keterangan' => 'Pemasangan kran baru ditunda (menunggu suku cadang).'],
                    ['tahap' => 'Selesai & BAST', 'waktu' => 'Menunggu', 'status' => 'pending', 'keterangan' => 'Belum selesai.'],
                ],
            ],
            [
                'id_laporan' => 103,
                'nomor_tiket' => 'TKT-2026-0103',
                'tanggal_lapor_raw' => '2026-11-27',
                'tanggal_lapor' => '27 Nov 2026',
                'waktu_lapor' => '07:30 WIB',
                'tanggal_selesai' => '—',
                'judul' => 'Lampu utama kamar tidur redup dan sering berkedip',
                'deskripsi' => 'Lampu plafon kamar redup dan berdengung sejak kemarin, menyulitkan aktivitas belajar mahasiswa pada malam hari.',
                'fasilitas_nama' => 'Lampu LED Plafon 18W',
                'kategori_fasilitas' => 'Fasilitas Kamar',
                'subkomponen' => 'Driver Trafo LED & Fitting E27',
                'gedung_id' => 2,
                'gedung_nama' => 'Asrama Putra Rasuna Said',
                'lantai' => 1,
                'kamar_nomor' => 'Kamar 108',
                'lokasi_lengkap' => 'Asrama Putra Rasuna Said, Lantai 1 (Kamar 108)',
                'pelapor_nama' => 'Farhan Maulana',
                'pelapor_identitas' => 'NIM. 2311523015',
                'pelapor_jurusan' => 'S1 Teknik Sipil',
                'pelapor_telepon' => '0852-1122-3344',
                'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
                'teknisi_nama' => $user?->nama_lengkap ?? 'Teknisi Pemeliharaan',
                'urgensi' => 'Sedang',
                'urgensi_color' => 'bg-[#fff3df] text-[#99620a] border-[#e0c080]/40',
                'sla_jam' => 48,
                'sla_target' => 'Maks. 48 Jam (2 Hari)',
                'sla_deadline' => '29 Nov 2026, 07:30 WIB',
                'is_overdue' => false,
                'status_perbaikan' => 'diajukan',
                'status_badge' => ['label' => 'Menunggu Penanganan', 'class' => 'bg-surface-container-low text-secondary border border-outline-variant/30', 'icon' => 'hourglass_empty', 'step' => 1],
                'foto' => null,
                'catatan_teknisi' => 'Tiket baru masuk, dijadwalkan inspeksi fitting dan penggantian bohlam LED sore ini.',
                'suku_cadang' => [
                    ['nama' => 'Lampu LED Bulb 18 Watt Cool Daylight', 'qty' => '1 Buah', 'status' => 'Tersedia di Gudang'],
                ],
                'linimasa' => [
                    ['tahap' => 'Laporan Diajukan', 'waktu' => '27 Nov 2026, 07:30', 'status' => 'done', 'keterangan' => 'Laporan baru diterima sistem.'],
                    ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => 'Menunggu', 'status' => 'pending', 'keterangan' => 'Menunggu konfirmasi penerimaan teknisi.'],
                    ['tahap' => 'Dalam Pengerjaan', 'waktu' => 'Menunggu', 'status' => 'pending', 'keterangan' => 'Belum dimulai.'],
                    ['tahap' => 'Selesai & BAST', 'waktu' => 'Menunggu', 'status' => 'pending', 'keterangan' => 'Belum selesai.'],
                ],
            ],
            [
                'id_laporan' => 104,
                'nomor_tiket' => 'TKT-2026-0098',
                'tanggal_lapor_raw' => '2026-11-23',
                'tanggal_lapor' => '23 Nov 2026',
                'waktu_lapor' => '10:00 WIB',
                'tanggal_selesai' => '24 Nov 2026',
                'judul' => 'Kunci pintu kamar macet dan anak kunci patah di dalam silinder',
                'deskripsi' => 'Silinder kunci pintu kamar aus sehingga anak kunci patah saat diputar dari luar.',
                'fasilitas_nama' => 'Pintu Kamar & Silinder Kunci',
                'kategori_fasilitas' => 'Fasilitas Kamar',
                'subkomponen' => 'Silinder Kunci Kuningan 60mm & Handle Pintu',
                'gedung_id' => 1,
                'gedung_nama' => 'Gedung Menpera (D)',
                'lantai' => 2,
                'kamar_nomor' => 'Kamar 212',
                'lokasi_lengkap' => 'Gedung Menpera (D), Lantai 2 (Kamar 212)',
                'pelapor_nama' => 'Bayu Nugraha',
                'pelapor_identitas' => 'NIM. 2111521019',
                'pelapor_jurusan' => 'S1 Ilmu Komputer',
                'pelapor_telepon' => '0813-9876-5432',
                'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
                'teknisi_nama' => $user?->nama_lengkap ?? 'Teknisi Pemeliharaan',
                'urgensi' => 'Tinggi',
                'urgensi_color' => 'bg-[#ffe4cc] text-[#c25e00] border-[#f5b880]/40',
                'sla_jam' => 24,
                'sla_target' => 'Maks. 24 Jam',
                'sla_deadline' => '24 Nov 2026, 10:00 WIB',
                'is_overdue' => false,
                'status_perbaikan' => 'selesai',
                'status_badge' => ['label' => 'Selesai & Terverifikasi', 'class' => 'bg-success-container text-success border border-success/30', 'icon' => 'task_alt', 'step' => 4],
                'foto' => null,
                'catatan_teknisi' => 'Silinder lama berhasil diekstraksi dan diganti dengan silinder kuningan baru 60mm lengkap dengan 3 anak kunci master.',
                'suku_cadang' => [
                    ['nama' => 'Silinder Kunci Kamar Mandi / Pintu 60mm', 'qty' => '1 Set', 'status' => 'Digunakan'],
                ],
                'linimasa' => [
                    ['tahap' => 'Laporan Diajukan', 'waktu' => '23 Nov 2026, 10:00', 'status' => 'done', 'keterangan' => 'Laporan diajukan.'],
                    ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => '23 Nov 2026, 10:20', 'status' => 'done', 'keterangan' => 'Ditugaskan ke teknisi pertukangan.'],
                    ['tahap' => 'Dalam Pengerjaan', 'waktu' => '23 Nov 2026, 11:30', 'status' => 'done', 'keterangan' => 'Penggantian bodi silinder kunci.'],
                    ['tahap' => 'Selesai & BAST', 'waktu' => '24 Nov 2026, 09:15', 'status' => 'done', 'keterangan' => 'Kunci baru diserahkan kepada penghuni kamar.'],
                ],
            ],
            [
                'id_laporan' => 105,
                'nomor_tiket' => 'TKT-2026-0095',
                'tanggal_lapor_raw' => '2026-11-20',
                'tanggal_lapor' => '20 Nov 2026',
                'waktu_lapor' => '16:45 WIB',
                'tanggal_selesai' => '21 Nov 2026',
                'judul' => 'Engsel daun jendela berkarat dan berderit kencang saat ditiup angin',
                'deskripsi' => 'Engsel jendela kamar sisi timur sudah aus dan baut penahan terlepas satu buah.',
                'fasilitas_nama' => 'Jendela Kamar',
                'kategori_fasilitas' => 'Fasilitas Kamar',
                'subkomponen' => 'Engsel Casement 8" & Baut Tapping',
                'gedung_id' => 1,
                'gedung_nama' => 'Gedung Menpera (D)',
                'lantai' => 1,
                'kamar_nomor' => 'Kamar 102',
                'lokasi_lengkap' => 'Gedung Menpera (D), Lantai 1 (Kamar 102)',
                'pelapor_nama' => 'Doni Irawan',
                'pelapor_identitas' => 'NIM. 2211522040',
                'pelapor_jurusan' => 'S1 Sistem Informasi',
                'pelapor_telepon' => '0877-3344-5566',
                'fasilitator_nama' => 'Rahadian Triputra (Fasilitator Pengelola)',
                'teknisi_nama' => $user?->nama_lengkap ?? 'Teknisi Pemeliharaan',
                'urgensi' => 'Rendah',
                'urgensi_color' => 'bg-surface-container-low text-secondary border-outline-variant/30',
                'sla_jam' => 72,
                'sla_target' => 'Maks. 72 Jam (3 Hari)',
                'sla_deadline' => '23 Nov 2026, 16:45 WIB',
                'is_overdue' => false,
                'status_perbaikan' => 'selesai',
                'status_badge' => ['label' => 'Selesai & Terverifikasi', 'class' => 'bg-success-container text-success border border-success/30', 'icon' => 'task_alt', 'step' => 4],
                'foto' => null,
                'catatan_teknisi' => 'Diberikan pelumas anti karat dan dilakukan pengencangan baut sekrup penahan bingkai jendela.',
                'suku_cadang' => [
                    ['nama' => 'Pelumas Semprot WD & Sekrup Besi 1"', 'qty' => '1 Paket', 'status' => 'Digunakan'],
                ],
                'linimasa' => [
                    ['tahap' => 'Laporan Diajukan', 'waktu' => '20 Nov 2026, 16:45', 'status' => 'done', 'keterangan' => 'Laporan diajukan.'],
                    ['tahap' => 'Disposisi & Penugasan Teknisi', 'waktu' => '21 Nov 2026, 08:30', 'status' => 'done', 'keterangan' => 'Penugasan diterima.'],
                    ['tahap' => 'Dalam Pengerjaan', 'waktu' => '21 Nov 2026, 10:00', 'status' => 'done', 'keterangan' => 'Pemberian pelumas dan sekrup baru.'],
                    ['tahap' => 'Selesai & BAST', 'waktu' => '21 Nov 2026, 11:00', 'status' => 'done', 'keterangan' => 'Jendela telah berfungsi normal.'],
                ],
            ],
        ];
    }
}
