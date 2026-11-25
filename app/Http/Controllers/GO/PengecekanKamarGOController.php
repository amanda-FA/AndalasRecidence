<?php

namespace App\Http\Controllers\GO;

use App\Http\Controllers\Controller;
use App\Http\Requests\GO\SimpanPengecekanRequest;
use App\Models\DetailPengecekan;
use App\Models\Fasilitas;
use App\Models\PengajuanCheckout;
use App\Models\Pengecekan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengecekanKamarGOController extends Controller
{
    /**
     * Menampilkan daftar antrean dan riwayat pengecekan kamar untuk GO.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'semua');
        $search = $request->query('search');

        $query = PengajuanCheckout::with([
            'penempatanKamar.kamar.gedung',
            'penempatanKamar.pendaftaranHunian.client.pengguna',
            'pengecekan.pengguna',
        ]);

        if ($search) {
            $query->whereHas('penempatanKamar.pendaftaranHunian.client.pengguna', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })->orWhereHas('penempatanKamar.kamar', function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%");
            });
        }

        if ($statusFilter === 'menunggu') {
            $query->where('status_pengajuan', PengajuanCheckout::STATUS_DIAJUKAN);
        } elseif ($statusFilter === 'sedang_diperiksa') {
            $query->whereIn('status_pengajuan', [PengajuanCheckout::STATUS_AUDIT_GO, 'dalam_pengecekan']);
        } elseif ($statusFilter === 'selesai') {
            $query->whereIn('status_pengajuan', [
                PengajuanCheckout::STATUS_MENUNGGU_PERSETUJUAN,
                PengajuanCheckout::STATUS_DISETUJUI,
                'selesai',
            ]);
        }

        $allCheckouts = (clone $query)->orderByDesc('tanggal_pengajuan')->get();

        $stats = [
            'total' => PengajuanCheckout::count(),
            'menunggu' => PengajuanCheckout::where('status_pengajuan', PengajuanCheckout::STATUS_DIAJUKAN)->count(),
            'sedang_diperiksa' => PengajuanCheckout::whereIn('status_pengajuan', [PengajuanCheckout::STATUS_AUDIT_GO, 'dalam_pengecekan'])->count(),
            'selesai' => PengajuanCheckout::whereIn('status_pengajuan', [
                PengajuanCheckout::STATUS_MENUNGGU_PERSETUJUAN,
                PengajuanCheckout::STATUS_DISETUJUI,
                'selesai',
            ])->count(),
        ];

        // Format data untuk view
        $antrean = $allCheckouts->map(function ($checkout) {
            $penempatan = $checkout->penempatanKamar;
            $kamar = $penempatan?->kamar;
            $gedung = $kamar?->gedung;
            $client = $penempatan?->pendaftaranHunian?->client;
            $pengguna = $client?->pengguna;
            $pengecekan = $checkout->pengecekan;

            $statusPengajuan = $checkout->status_pengajuan;
            $statusLabel = 'Menunggu Pengecekan';
            $statusBadge = 'bg-[#C97D1A]/10 text-[#C97D1A] border-[#C97D1A]/20';

            if (in_array($statusPengajuan, [PengajuanCheckout::STATUS_AUDIT_GO, 'dalam_pengecekan'], true)) {
                $statusLabel = 'Sedang Diperiksa';
                $statusBadge = 'bg-secondary/10 text-secondary border-secondary/20';
            } elseif (in_array($statusPengajuan, [PengajuanCheckout::STATUS_MENUNGGU_PERSETUJUAN, PengajuanCheckout::STATUS_DISETUJUI, 'selesai'], true)) {
                $statusLabel = 'Pemeriksaan Selesai';
                $statusBadge = 'bg-success/10 text-success border-success/20';
            }

            return [
                'id_pengajuan' => $checkout->id_pengajuan,
                'no_audit' => '#CHK-' . now()->format('Y') . '-' . str_pad($checkout->id_pengajuan, 4, '0', STR_PAD_LEFT),
                'nama_mahasiswa' => $pengguna?->nama_lengkap ?? 'Mahasiswa',
                'nim' => $client?->nomor_identitas ?? '-',
                'gedung' => $gedung?->nama_gedung ?? 'Gedung Asrama',
                'kamar' => $kamar?->nomor_kamar ?? '-',
                'bed' => $penempatan?->nomor_bed ? 'Bed ' . chr(64 + $penempatan->nomor_bed) : 'Bed A',
                'tanggal_rencanaKeluar' => $checkout->tanggal_rencanaKeluar ? Carbon::parse($checkout->tanggal_rencanaKeluar)->translatedFormat('d M Y') : '-',
                'status_pengajuan' => $statusPengajuan,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'petugas' => $pengecekan?->pengguna?->nama_lengkap ?? '-',
                'kondisi_kamar' => $pengecekan?->kondisi_kamar ?? 'Belum Diperiksa',
                'status_verifikasi' => $pengecekan?->status_verifikasi ?? 'draft',
            ];
        });

        return view('go.pengecekan-kamar', compact('antrean', 'stats', 'statusFilter'));
    }

    /**
     * Memulai proses audit fisik kamar oleh GO.
     */
    public function mulai($id)
    {
        return DB::transaction(function () use ($id) {
            $checkout = PengajuanCheckout::findOrFail($id);
            $user = auth()->user();

            Pengecekan::firstOrCreate(
                ['id_pengajuan' => $checkout->id_pengajuan],
                [
                    'id_pengguna' => $user->id_pengguna,
                    'tanggal_cek' => Carbon::today('Asia/Jakarta'),
                    'kondisi_kamar' => 'dalam_pengecekan',
                    'catatan' => '',
                    'status_verifikasi' => 'draft',
                ]
            );

            $checkout->update(['status_pengajuan' => PengajuanCheckout::STATUS_AUDIT_GO]);

            return redirect()->route('go.pengecekan-kamar.show', $id)
                ->with('success', 'Pengecekan fisik kamar #' . $id . ' telah dimulai.');
        });
    }

    /**
     * Menampilkan formulir pengecekan fisik kamar lengkap dengan checklist digital.
     */
    public function show($id)
    {
        $checkout = PengajuanCheckout::with([
            'penempatanKamar.kamar.gedung',
            'penempatanKamar.kamar.fasilitas',
            'penempatanKamar.pendaftaranHunian.client.pengguna',
            'pengecekan.detailPengecekans.fasilitas',
        ])->findOrFail($id);

        $penempatan = $checkout->penempatanKamar;
        $kamar = $penempatan?->kamar;
        $gedung = $kamar?->gedung;
        $client = $penempatan?->pendaftaranHunian?->client;
        $pengguna = $client?->pengguna;
        $pengecekan = $checkout->pengecekan;
        $petugas = auth()->user();

        $fasilitasKamar = $kamar?->fasilitas ?? collect();
        $nomorAudit = '#CHK-' . now()->format('Y') . '-' . str_pad($checkout->id_pengajuan, 4, '0', STR_PAD_LEFT);

        return view('go.form-pengecekan', compact(
            'checkout',
            'penempatan',
            'kamar',
            'gedung',
            'pengguna',
            'pengecekan',
            'petugas',
            'fasilitasKamar',
            'nomorAudit'
        ));
    }

    /**
     * Menyimpan checklist sementara sebagai DRAF (status_verifikasi = 'draft').
     */
    public function draft(SimpanPengecekanRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $checkout = PengajuanCheckout::findOrFail($id);
            $user = auth()->user();

            $pengecekan = Pengecekan::firstOrCreate(
                ['id_pengajuan' => $checkout->id_pengajuan],
                [
                    'id_pengguna' => $user->id_pengguna,
                    'tanggal_cek' => Carbon::today('Asia/Jakarta'),
                    'kondisi_kamar' => 'dalam_pengecekan',
                    'catatan' => '',
                    'status_verifikasi' => 'draft',
                ]
            );

            // Simpan catatan dan status draf
            $pengecekan->update([
                'id_pengguna' => $user->id_pengguna,
                'tanggal_cek' => Carbon::today('Asia/Jakarta'),
                'kondisi_kamar' => $request->kondisi_kamar ?: 'dalam_pengecekan',
                'catatan' => $request->catatan ?? '',
                'status_verifikasi' => 'draft',
            ]);

            // Simpan detail checklist fasilitas ke detail_pengecekan
            $this->simpanDetailChecklist($pengecekan, $checkout, $request->all());

            $checkout->update(['status_pengajuan' => PengajuanCheckout::STATUS_AUDIT_GO]);

            return redirect()->route('go.pengecekan-kamar.index')
                ->with('success', 'Draf hasil pengecekan kamar berhasil disimpan. Anda dapat melanjutkannya kapan saja.');
        });
    }

    /**
     * Menyelesaikan pengecekan kamar (status_verifikasi = 'selesai') dan meneruskan ke Fasilitator.
     */
    public function selesai(SimpanPengecekanRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $checkout = PengajuanCheckout::with('penempatanKamar.kamar')->findOrFail($id);
            $user = auth()->user();

            $pengecekan = Pengecekan::firstOrCreate(
                ['id_pengajuan' => $checkout->id_pengajuan],
                [
                    'id_pengguna' => $user->id_pengguna,
                    'tanggal_cek' => Carbon::today('Asia/Jakarta'),
                    'kondisi_kamar' => 'baik',
                    'catatan' => '',
                    'status_verifikasi' => 'draft',
                ]
            );

            // Simpan detail checklist fasilitas
            $hasilKondisi = $this->simpanDetailChecklist($pengecekan, $checkout, $request->all());

            // Tentukan kesimpulan kondisi kamar (baik, rusak_ringan, rusak_berat)
            $kondisiAkhir = $request->kondisi_kamar ?: $hasilKondisi;

            $pengecekan->update([
                'id_pengguna' => $user->id_pengguna,
                'tanggal_cek' => Carbon::today('Asia/Jakarta'),
                'kondisi_kamar' => $kondisiAkhir,
                'catatan' => $request->catatan ?? '',
                'status_verifikasi' => 'selesai',
            ]);

            // Majukan status pengajuan ke Fasilitator
            $checkout->update([
                'status_pengajuan' => PengajuanCheckout::STATUS_MENUNGGU_PERSETUJUAN,
            ]);

            return redirect()->route('go.pengecekan-kamar.index')
                ->with('success', 'Pemeriksaan kamar berhasil difinalisasi dan diteruskan ke Fasilitator untuk persetujuan bebas asrama.');
        });
    }

    /**
     * Menyimpan butir-butir checklist ke tabel detail_pengecekan.
     */
    private function simpanDetailChecklist(Pengecekan $pengecekan, PengajuanCheckout $checkout, array $data): string
    {
        $kamar = $checkout->penempatanKamar?->kamar;
        $gedung = $kamar?->gedung;

        $items = array_merge(
            $data['checklist_a'] ?? [],
            $data['checklist_b'] ?? [],
            $data['checklist_c'] ?? []
        );

        $checklistMeta = [
            'dinding_plafon' => ['nama' => 'Dinding, Cat, & Plafon Kamar', 'default_cat' => 'Dinding'],
            'pintu_handel'   => ['nama' => 'Pintu Utama, Kunci, & Handel', 'default_cat' => 'Pintu'],
            'jendela_lantai' => ['nama' => 'Jendela Kaca & Ubin Keramik', 'default_cat' => 'Jendela'],
            'tempat_tidur'   => ['nama' => 'Tempat Tidur & Kasur Spring Bed', 'default_cat' => 'Dipan'],
            'lemari_pakaian' => ['nama' => 'Lemari Pakaian 2 Pintu & Kunci Loker', 'default_cat' => 'Lemari'],
            'meja_kursi'     => ['nama' => 'Meja Belajar & Kursi', 'default_cat' => 'Meja Belajar'],
            'ac_split'       => ['nama' => 'Unit AC Split & Remote AC', 'default_cat' => 'AC'],
            'kelistrikan'    => ['nama' => 'Sakelar Lampu / Stop Kontak / MCB', 'default_cat' => 'Kelistrikan'],
            'sanitasi'       => ['nama' => 'Instalasi Kamar Mandi Dalam', 'default_cat' => 'Kamar Mandi Dalam'],
        ];

        $adaRusakBerat = false;
        $adaRusakRingan = false;

        foreach ($items as $itemKey => $kondisiValue) {
            $meta = $checklistMeta[$itemKey] ?? ['nama' => ucfirst(str_replace('_', ' ', $itemKey)), 'default_cat' => 'Fasilitas'];

            if ($kondisiValue === 'rusak_berat') {
                $adaRusakBerat = true;
            } elseif (in_array($kondisiValue, ['rusak_ringan', 'rusak'], true)) {
                $adaRusakRingan = true;
            }

            // Cari fasilitas terkait di gedung/kamar ini
            $fasilitas = Fasilitas::where(function ($q) use ($kamar, $gedung, $meta) {
                if ($kamar) {
                    $q->where('id_kamar', $kamar->id_kamar)->where('nama_fasilitas', 'like', "%{$meta['default_cat']}%");
                }
            })->orWhere(function ($q) use ($gedung, $meta) {
                if ($gedung) {
                    $q->where('id_gedung', $gedung->id_gedung)->where('nama_fasilitas', 'like', "%{$meta['default_cat']}%");
                }
            })->first();

            // Jika belum ada di master fasilitas, buatkan entri fasilitas kamar
            if (!$fasilitas) {
                $fasilitas = Fasilitas::create([
                    'id_gedung' => $gedung?->id_gedung ?? 1,
                    'id_kamar' => $kamar?->id_kamar,
                    'nama_fasilitas' => $meta['nama'],
                    'kondisi' => $kondisiValue === 'baik' ? 'Baik' : 'Rusak',
                    'aktif' => 1,
                ]);
            }

            // Simpan ke detail_pengecekan sesuai skema ERD
            // kondisi: 'baik' atau 'rusak'
            $kondisiEnum = in_array($kondisiValue, ['rusak_ringan', 'rusak_berat', 'rusak'], true) ? 'rusak' : 'baik';
            $keterangan = $kondisiValue === 'baik'
                ? 'Kondisi fisik ' . $meta['nama'] . ' terawat baik dan berfungsi normal.'
                : 'Ditemukan indikasi kendala fisik / kerusakan (' . str_replace('_', ' ', $kondisiValue) . ') pada ' . $meta['nama'] . '.';

            DetailPengecekan::updateOrCreate(
                [
                    'id_pengecekan' => $pengecekan->id_pengecekan,
                    'id_fasilitas' => $fasilitas->id_fasilitas,
                ],
                [
                    'kondisi' => $kondisiEnum,
                    'keterangan' => $keterangan,
                ]
            );
        }

        if ($adaRusakBerat) {
            return 'rusak_berat';
        }

        if ($adaRusakRingan) {
            return 'rusak_ringan';
        }

        return 'baik';
    }
}
