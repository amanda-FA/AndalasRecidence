<?php

namespace App\Http\Controllers\GO;

use App\Http\Controllers\Controller;
use App\Http\Requests\GO\SimpanLaporanKerusakanRequest;
use App\Models\Fasilitas;
use App\Models\LaporanKerusakan;
use App\Models\PengajuanCheckout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKerusakanGOController extends Controller
{
    /**
     * Menampilkan daftar tiket laporan kerusakan fasilitas oleh GO.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'semua');

        $query = LaporanKerusakan::with([
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'pengguna',
            'penugasanPerbaikans.pengguna',
        ]);

        if ($statusFilter !== 'semua') {
            $query->where('status_perbaikan', $statusFilter);
        }

        $laporans = $query->orderByDesc('tanggal_lapor')->get();

        $stats = [
            'total' => LaporanKerusakan::count(),
            'menunggu_disposisi' => LaporanKerusakan::where('status_perbaikan', 'diajukan')->count(),
            'dalam_perbaikan' => LaporanKerusakan::whereIn('status_perbaikan', ['ditugaskan', 'dalam_pengerjaan'])->count(),
            'selesai' => LaporanKerusakan::where('status_perbaikan', 'selesai')->count(),
        ];

        $tikets = $laporans->map(function ($laporan) {
            $fasilitas = $laporan->fasilitas;
            $kamar = $fasilitas?->kamar;
            $gedung = $kamar?->gedung ?? $fasilitas?->gedung;
            $penugasan = $laporan->penugasanPerbaikans->first();

            $statusMap = [
                'diajukan' => ['label' => 'Menunggu Disposisi', 'color' => 'bg-error/10 text-error'],
                'ditugaskan' => ['label' => 'Ditugaskan ke Teknisi', 'color' => 'bg-secondary/10 text-secondary'],
                'dalam_pengerjaan' => ['label' => 'Dalam Perbaikan', 'color' => 'bg-[#C97D1A]/10 text-[#C97D1A]'],
                'selesai' => ['label' => 'Selesai Diperbaiki', 'color' => 'bg-success/10 text-success'],
            ];

            $st = $statusMap[$laporan->status_perbaikan] ?? ['label' => ucfirst($laporan->status_perbaikan), 'color' => 'bg-surface-container text-on-surface'];

            return [
                'id_laporan' => $laporan->id_laporan,
                'nomor_tiket' => 'TKT-' . now()->format('Y') . '-' . str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT),
                'fasilitas' => $fasilitas?->nama_fasilitas ?? 'Fasilitas Hunian',
                'kamar' => $kamar?->nomor_kamar ?? '-',
                'gedung' => $gedung?->nama_gedung ?? '-',
                'deskripsi' => $laporan->deskripsi,
                'foto' => $laporan->foto,
                'tanggal_lapor' => $laporan->tanggal_lapor ? Carbon::parse($laporan->tanggal_lapor)->format('d M Y') : '-',
                'teknisi' => $penugasan?->pengguna?->nama_lengkap ?? 'Menunggu Penugasan Teknisi',
                'status_label' => $st['label'],
                'status_color' => $st['color'],
            ];
        });

        return view('go.laporan-kerusakan', compact('stats', 'tikets', 'statusFilter'));
    }

    /**
     * Menampilkan form input laporan kerusakan baru yang terhubung ke pengajuan check-out.
     */
    public function create(Request $request)
    {
        $idPengajuan = $request->query('pengajuan_id');
        $komponen = $request->query('komponen', 'ac_split');

        $checkout = null;
        if ($idPengajuan) {
            $checkout = PengajuanCheckout::with([
                'penempatanKamar.kamar.gedung',
                'penempatanKamar.pendaftaranHunian.client.pengguna',
                'pengecekan',
            ])->find($idPengajuan);
        }

        if (!$checkout) {
            $checkout = PengajuanCheckout::with([
                'penempatanKamar.kamar.gedung',
                'penempatanKamar.pendaftaranHunian.client.pengguna',
                'pengecekan',
            ])->first();
        }

        $penempatan = $checkout?->penempatanKamar;
        $kamar = $penempatan?->kamar;
        $gedung = $kamar?->gedung;
        $client = $penempatan?->pendaftaranHunian?->client;
        $pengguna = $client?->pengguna;
        $nomorAudit = '#CHK-' . now()->format('Y') . '-' . str_pad($checkout?->id_pengajuan ?? 1, 4, '0', STR_PAD_LEFT);

        // Ambil daftar fasilitas yang tersedia untuk kamar dan gedung ini
        $fasilitasQuery = Fasilitas::where(function ($q) use ($kamar, $gedung) {
            if ($kamar) {
                $q->where('id_kamar', $kamar->id_kamar);
            }
            if ($gedung) {
                $q->orWhere('id_gedung', $gedung->id_gedung);
            }
        });

        $fasilitas = $fasilitasQuery->get();

        if ($fasilitas->isEmpty()) {
            $fasilitas = Fasilitas::limit(15)->get();
        }

        return view('go.form-laporan-kerusakan', compact(
            'checkout',
            'penempatan',
            'kamar',
            'gedung',
            'client',
            'pengguna',
            'nomorAudit',
            'komponen',
            'fasilitas'
        ));
    }

    /**
     * Menyimpan laporan kerusakan baru dan memformat deskripsi standar.
     */
    public function store(SimpanLaporanKerusakanRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $fasilitas = Fasilitas::with('kamar')->findOrFail($request->id_fasilitas);

            // Handle upload file foto bukti
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('kerusakan', 'public');
            } elseif ($request->hasFile('foto_jarak_dekat')) {
                $fotoPath = $request->file('foto_jarak_dekat')->store('kerusakan', 'public');
            } elseif ($request->hasFile('foto_sudut_ruang')) {
                $fotoPath = $request->file('foto_sudut_ruang')->store('kerusakan', 'public');
            } elseif ($request->hasFile('foto_ruang')) {
                $fotoPath = $request->file('foto_ruang')->store('kerusakan', 'public');
            } elseif ($request->hasFile('foto_tambahan')) {
                $fotoPath = $request->file('foto_tambahan')->store('kerusakan', 'public');
            }

            // Standarisasi Format Deskripsi Kerusakan sesuai Catatan Teknis 2:
            // "[Pengecekan Kamar #CHK-xxx] {nama_fasilitas} rusak per temuan audit GO: {judul}. {detail} [Urgensi: ...]"
            $auditRef = $request->id_pengajuan
                ? ('#CHK-' . now()->format('Y') . '-' . str_pad($request->id_pengajuan, 4, '0', STR_PAD_LEFT))
                : 'AUDIT-GO';

            $namaFasilitas = $fasilitas->nama_fasilitas;
            $judul = $request->judul_kerusakan ? trim($request->judul_kerusakan) . '. ' : '';
            $detail = trim($request->deskripsi);
            $urgensi = $request->tingkat_urgensi ? ' [Urgensi: ' . strtoupper($request->tingkat_urgensi) . ']' : '';
            $aset = $request->kode_aset ? ' [Kode Aset: ' . trim($request->kode_aset) . ']' : '';
            $disposisi = $request->catatan_disposisi ? ' [Catatan Disposisi: ' . trim($request->catatan_disposisi) . ']' : '';

            $standardizedDeskripsi = sprintf(
                '[Pengecekan Kamar %s] Fasilitas %s rusak per temuan audit GO: %s%s%s%s%s',
                $auditRef,
                $namaFasilitas,
                $judul,
                $detail,
                $aset,
                $urgensi,
                $disposisi
            );

            // Simpan laporan kerusakan sesuai skema ERD
            $laporan = LaporanKerusakan::create([
                'id_pengguna' => $user->id_pengguna,
                'id_fasilitas' => $fasilitas->id_fasilitas,
                'deskripsi' => $standardizedDeskripsi,
                'foto' => $fotoPath,
                'tanggal_lapor' => Carbon::today('Asia/Jakarta'),
                'status_perbaikan' => 'diajukan',
                'tanggal_selesai' => null,
            ]);

            // Jika fasilitas melekat pada kamar tertentu, perbarui status kamar menjadi dalam_perbaikan
            if ($fasilitas->kamar) {
                $fasilitas->kamar->update(['status_kamar' => 'dalam_perbaikan']);
            }

            $nomorTiket = 'TKT-' . now()->format('Y') . '-' . str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT);

            return redirect()->route('go.laporan-kerusakan.index')
                ->with('success', 'Tiket kerusakan #' . $nomorTiket . ' berhasil diterbitkan dan diteruskan ke tim teknisi.');
        });
    }
}
