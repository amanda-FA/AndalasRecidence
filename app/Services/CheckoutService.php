<?php

namespace App\Services;

use App\Models\Client;
use App\Models\JenisClient;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\PenempatanKamar;
use App\Models\PengajuanCheckout;
use App\Models\Pengecekan;
use App\Models\SuratBebasAsrama;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Memeriksa status finansial / tunggakan client.
     */
    public function cekStatusFinansial(Client $client, ?PenempatanKamar $penempatan = null): array
    {
        // Jika KIP-K atau Internasional beasiswa, otomatis bebas tunggakan sewa
        $isKipk = (bool) $client->status_KIPK;
        $kategoriJenis = $client->jenisClient?->jenis;

        if ($isKipk || $kategoriJenis === JenisClient::CATEGORIES['kipk'] || $kategoriJenis === JenisClient::CATEGORIES['international_funded']) {
            return [
                'bebas_tunggakan' => true,
                'status_label' => 'Bebas Tunggakan (KIP-K / Subsidi)',
                'tunggakan_nominal' => 0.00,
                'tagihan_pending' => collect(),
            ];
        }

        // Ambil pendaftaran terkait penempatan atau pendaftaran terakhir client
        $pendaftaranId = $penempatan?->id_pendaftaran ?? $client->pendaftaranHunians()->latest('id_pendaftaran')->value('id_pendaftaran');

        if (!$pendaftaranId) {
            return [
                'bebas_tunggakan' => true,
                'status_label' => 'Bebas Tunggakan / Lunas',
                'tunggakan_nominal' => 0.00,
                'tagihan_pending' => collect(),
            ];
        }

        // Cek pembayaran yang masih pending/menunggu
        $tagihanPending = Pembayaran::where('id_pendaftaran', $pendaftaranId)
            ->whereNotIn('status_pembayaran', ['lunas', 'terverifikasi', 'menunggu_kipk', 'simulasi_lunas'])
            ->where('jumlah', '>', 0)
            ->get();

        $totalTunggakan = (float) $tagihanPending->sum('jumlah');

        return [
            'bebas_tunggakan' => $totalTunggakan <= 0,
            'status_label' => $totalTunggakan <= 0 ? 'Bebas Tunggakan / Lunas' : 'Memiliki Tunggakan Tagihan',
            'tunggakan_nominal' => $totalTunggakan,
            'tagihan_pending' => $tagihanPending,
        ];
    }

    /**
     * Memeriksa kelayakan Client untuk mengajukan check-out.
     */
    public function cekKelayakanCheckout(Client $client): array
    {
        $penempatanAktif = PenempatanKamar::with(['kamar.gedung', 'pendaftaranHunian'])
            ->whereHas('pendaftaranHunian', function ($q) use ($client) {
                $q->where('id_client', $client->id_client);
            })
            ->where('status_penempatan', 'aktif')
            ->latest('id_penempatan')
            ->first();

        if (!$penempatanAktif) {
            // Cek apakah memiliki penempatan sebelumnya yang memiliki checkout
            $penempatanLama = PenempatanKamar::with(['kamar.gedung', 'pendaftaranHunian', 'pengajuanCheckout.suratBebasAsrama'])
                ->whereHas('pendaftaranHunian', function ($q) use ($client) {
                    $q->where('id_client', $client->id_client);
                })
                ->latest('id_penempatan')
                ->first();

            return [
                'eligible' => false,
                'penempatan' => $penempatanLama,
                'finansial' => ['bebas_tunggakan' => true, 'status_label' => 'Tidak Aktif', 'tunggakan_nominal' => 0],
                'checkout_aktif' => $penempatanLama?->pengajuanCheckout,
                'pesan' => 'Anda tidak memiliki penempatan kamar hunian berstatus aktif saat ini.',
            ];
        }

        $finansial = $this->cekStatusFinansial($client, $penempatanAktif);

        // Cek pengajuan yang sudah ada untuk penempatan ini
        $checkout = PengajuanCheckout::with(['pengecekan.pengguna', 'suratBebasAsrama', 'fasilitator'])
            ->where('id_penempatan', $penempatanAktif->id_penempatan)
            ->first();

        return [
            'eligible' => $finansial['bebas_tunggakan'],
            'penempatan' => $penempatanAktif,
            'finansial' => $finansial,
            'checkout_aktif' => $checkout,
            'pesan' => $finansial['bebas_tunggakan']
                ? 'Penghuni memenuhi syarat untuk mengajukan check-out.'
                : 'Masih terdapat tunggakan pembayaran hunian yang belum diselesaikan.',
        ];
    }

    /**
     * Mengajukan check-out mandiri oleh Client (Single Entry).
     */
    public function ajukanCheckout(Client $client, array $data): PengajuanCheckout
    {
        $kelayakan = $this->cekKelayakanCheckout($client);

        if (!$kelayakan['penempatan'] || $kelayakan['penempatan']->status_penempatan !== 'aktif') {
            throw ValidationException::withMessages([
                'checkout' => 'Hanya penghuni dengan kamar berstatus aktif yang dapat mengajukan check-out.',
            ]);
        }

        if (!$kelayakan['finansial']['bebas_tunggakan']) {
            throw ValidationException::withMessages([
                'tunggakan' => 'Terdapat tunggakan pembayaran sebesar Rp ' . number_format($kelayakan['finansial']['tunggakan_nominal'], 0, ',', '.') . '. Harap selesaikan pembayaran sebelum check-out.',
            ]);
        }

        $penempatan = $kelayakan['penempatan'];
        $tglKeluar = Carbon::parse($data['tanggal_rencanaKeluar'])->startOfDay();
        $minDate = Carbon::today('Asia/Jakarta')->addDays(2)->startOfDay();

        if ($tglKeluar->lt($minDate)) {
            throw ValidationException::withMessages([
                'tanggal_rencanaKeluar' => 'Rencana tanggal keluar minimal H+2 dari hari ini (' . $minDate->translatedFormat('d F Y') . ') agar petugas GO dapat menjadwalkan audit fisik kamar.',
            ]);
        }

        return DB::transaction(function () use ($penempatan, $client, $data) {
            $checkout = PengajuanCheckout::updateOrCreate(
                ['id_penempatan' => $penempatan->id_penempatan],
                [
                    'tanggal_pengajuan' => Carbon::today('Asia/Jakarta'),
                    'tanggal_rencanaKeluar' => $data['tanggal_rencanaKeluar'],
                    'alasan' => $data['alasan'],
                    'nama_bank' => $data['nama_bank'] ?? 'Bank Nagari',
                    'nomor_rekening' => $data['nomor_rekening'] ?? null,
                    'nama_pemilik_rekening' => $data['nama_pemilik_rekening'] ?? ($client->pengguna?->nama_lengkap ?? 'Mahasiswa'),
                    'nominal_deposit' => 50000.00,
                    'status_deposit' => 'menunggu_pencairan',
                    'denda_kerusakan' => 0.00,
                    'status_pengajuan' => PengajuanCheckout::STATUS_DIAJUKAN,
                    'catatan' => $data['catatan'] ?? null,
                    'alasan_penolakan' => null,
                ]
            );

            // Buat record pengecekan jika belum ada agar GO langsung melihat penugasan audit
            Pengecekan::firstOrCreate(
                ['id_pengajuan' => $checkout->id_pengajuan],
                [
                    'id_pengguna' => 5, // Default petugas GO / admin lapangan
                    'tanggal_cek' => Carbon::parse($data['tanggal_rencanaKeluar'])->subDay(),
                    'kondisi_kamar' => 'dalam_antrean',
                    'catatan' => 'Menunggu audit fisik oleh Petugas General Officer (GO).',
                    'status_verifikasi' => 'draft',
                ]
            );

            return $checkout;
        });
    }

    /**
     * Menyetujui pengajuan check-out oleh Fasilitator.
     * Otomatis menerbitkan SKBA, menutup penempatan, dan memperbarui status kamar.
     */
    public function setujuiCheckout(PengajuanCheckout $checkout, ?int $idFasilitator = null): SuratBebasAsrama
    {
        return DB::transaction(function () use ($checkout, $idFasilitator) {
            $today = Carbon::today('Asia/Jakarta');

            // 1. Update status checkout
            $checkout->update([
                'status_pengajuan' => PengajuanCheckout::STATUS_DISETUJUI,
                'id_fasilitator' => $idFasilitator ?? auth()->id(),
                'tangal_verifikasi' => $today,
                'status_deposit' => 'dikembalikan',
            ]);

            // 2. Tutup penempatan kamar menjadi selesai
            $penempatan = $checkout->penempatanKamar;
            if ($penempatan) {
                $penempatan->update([
                    'status_penempatan' => 'selesai',
                    'tanggal_keluar' => $today,
                ]);

                // 3. Update status kamar
                $pengecekan = $checkout->pengecekan;
                $kamar = $penempatan->kamar;
                if ($kamar) {
                    $adaKerusakan = $pengecekan && in_array($pengecekan->kondisi_kamar, ['rusak_ringan', 'rusak_berat'], true);
                    $kamar->update([
                        'status_kamar' => $adaKerusakan ? 'dalam_perbaikan' : 'tersedia',
                    ]);
                }
            }

            // 4. Terbitkan Surat Bebas Asrama (SKBA) otomatis
            $nomorSurat = $this->generateNomorSkba();

            return SuratBebasAsrama::updateOrCreate(
                ['id_pengajuan' => $checkout->id_pengajuan],
                [
                    'jenis_surat' => 'Surat Keterangan Bebas Asrama',
                    'nomor_surat' => $nomorSurat,
                    'tanggal_terbit' => $today,
                    'file_surat' => 'skba/' . str_replace('/', '_', $nomorSurat) . '.pdf',
                ]
            );
        });
    }

    /**
     * Menolak pengajuan check-out.
     */
    public function tolakCheckout(PengajuanCheckout $checkout, string $alasanPenolakan, ?int $idFasilitator = null): void
    {
        $checkout->update([
            'status_pengajuan' => PengajuanCheckout::STATUS_DITOLAK,
            'alasan_penolakan' => $alasanPenolakan,
            'id_fasilitator' => $idFasilitator ?? auth()->id(),
            'tangal_verifikasi' => Carbon::today('Asia/Jakarta'),
        ]);
    }

    /**
     * Generate nomor surat SKBA terstruktur: SKBA/UNAND/ASR/{tahun}/{nomor_urut_4_digit}
     */
    public function generateNomorSkba(): string
    {
        $tahun = Carbon::now('Asia/Jakarta')->year;
        $urutan = SuratBebasAsrama::whereYear('tanggal_terbit', $tahun)->count() + 1;

        return sprintf('SKBA/UNAND/ASR/%d/%04d', $tahun, $urutan);
    }
}
