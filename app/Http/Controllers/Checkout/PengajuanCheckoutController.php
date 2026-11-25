<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PengajuanCheckout;
use App\Services\CheckoutService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PengajuanCheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Mengambil data Client berdasarkan akun yang sedang login.
     */
    private function getClientLogin(): Client
    {
        $user = Auth::user();

        abort_unless($user, 401, 'Silakan login terlebih dahulu.');

        $client = Client::with(['pengguna', 'jenisClient'])
            ->where('id_pengguna', $user->id_pengguna)
            ->first();

        abort_if(!$client, 404, 'Data Client tidak ditemukan.');

        return $client;
    }

    /**
     * Tampilan utama Client - Check-out & Bebas Asrama.
     */
    public function index(Request $request)
    {
        $client = $this->getClientLogin();

        $kelayakan = $this->checkoutService
            ->cekKelayakanCheckout($client);

        $penempatan = $kelayakan['penempatan'] ?? null;
        $checkout = $kelayakan['checkout_aktif'] ?? null;

        // Data penghuni
        $penggunaModel = $client->pengguna;

        $penghuni = [
            'nama' => $penggunaModel?->nama_lengkap ?? '-',
            'nim' => $client->nomor_identitas ?? '-',
            'prodi' => $client->pekerjaan ?? '-',
            'fakultas' => '-',
            'kontak' => $penggunaModel?->no_hp ?? '-',
            'email' => $penggunaModel?->email ?? '-',
            'alamat' => $client->alamat_asal ?? '-',
        ];

        // Data hunian dan kamar
        $kamar = $penempatan?->kamar;
        $gedung = $kamar?->gedung;
        $finansial = $kelayakan['finansial'] ?? [];

        $tglKeluarRencana = $checkout?->tanggal_rencanaKeluar
            ? Carbon::parse($checkout->tanggal_rencanaKeluar)
                ->translatedFormat('d F Y')
            : ($penempatan?->rencana_keluar
                ? Carbon::parse($penempatan->rencana_keluar)
                    ->translatedFormat('d F Y')
                : '-');

        $hunian = [
            'id_penempatan' => $penempatan?->id_penempatan,
            'gedung' => $gedung?->nama_gedung ?? '-',
            'nomor_kamar' => $kamar?->nomor_kamar ?? '-',
            'bed' => $penempatan?->nomor_bed
                ? 'Bed ' . chr(64 + $penempatan->nomor_bed)
                : '-',
            'lantai' => $kamar?->lantai ?? '-',
            'tipe_kamar' => '-',
            'periode' => '-',
            'tanggal_masuk' => $penempatan?->tanggal_masuk
                ? Carbon::parse($penempatan->tanggal_masuk)
                    ->translatedFormat('d F Y')
                : '-',
            'tanggal_berakhir_sewa' => $tglKeluarRencana,
            'status_finansial' => $finansial['status_label'] ?? 'Belum tersedia',
            'bebas_tunggakan' => $finansial['bebas_tunggakan'] ?? false,
            'nominal_deposit' => $checkout?->nominal_deposit ?? 0,
            'status_deposit' => $checkout?->status_deposit ?? 'Belum ada pengajuan',
        ];

        // Status tahapan Check-out
        $statusCurrent = $checkout?->status_pengajuan;
        $suratBebasModel = $checkout?->suratBebasAsrama;

        $tahap1Status = $checkout ? 'done' : 'pending';
        $tahap2Status = 'pending';
        $tahap3Status = 'pending';
        $tahap4Status = $suratBebasModel ? 'done' : 'pending';

        if (in_array($statusCurrent, ['audit_go', 'dalam_pengecekan'], true)) {
            $tahap2Status = 'in_progress';
        } elseif ($statusCurrent === 'menunggu_persetujuan') {
            $tahap2Status = 'done';
            $tahap3Status = 'in_progress';
        } elseif ($statusCurrent === PengajuanCheckout::STATUS_DISETUJUI) {
            $tahap2Status = 'done';
            $tahap3Status = 'done';
        }

        $jumlahTahapSelesai = collect([
            $tahap1Status,
            $tahap2Status,
            $tahap3Status,
            $tahap4Status,
        ])->filter(fn ($status) => $status === 'done')->count();

        $progressCheckout = (int) round(($jumlahTahapSelesai / 4) * 100);

        $tahapan = [
            [
                'urutan' => 1,
                'judul' => 'Pengajuan Dikirim',
                'subjudul' => 'Permohonan Check-out Mahasiswa',
                'status' => $tahap1Status,
                'waktu' => $checkout?->tanggal_pengajuan
                    ? Carbon::parse($checkout->tanggal_pengajuan)
                        ->translatedFormat('d M Y')
                    : 'Belum diajukan',
                'ikon' => 'assignment_turned_in',
                'keterangan' => $checkout
                    ? 'Pengajuan check-out telah tercatat di sistem.'
                    : 'Formulir check-out belum dikirim oleh mahasiswa.',
            ],
            [
                'urutan' => 2,
                'judul' => 'Pengecekan GO (Fisik Kamar & Kunci)',
                'subjudul' => 'Audit Lapangan & Serah Terima Kunci',
                'status' => $tahap2Status,
                'waktu' => $checkout?->pengecekan?->tanggal_cek
                    ? Carbon::parse($checkout->pengecekan->tanggal_cek)
                        ->translatedFormat('d M Y')
                    : 'Belum dijadwalkan',
                'ikon' => 'fact_check',
                'keterangan' => $tahap2Status === 'done'
                    ? 'Pemeriksaan kamar telah selesai.'
                    : ($tahap2Status === 'in_progress'
                        ? 'Pemeriksaan kamar sedang berlangsung.'
                        : 'Menunggu pemeriksaan oleh petugas GO.'),
            ],
            [
                'urutan' => 3,
                'judul' => 'Approval Fasilitator',
                'subjudul' => 'Verifikasi Final & Bebas Kewajiban',
                'status' => $tahap3Status,
                'waktu' => $checkout?->tangal_verifikasi
                    ? Carbon::parse($checkout->tangal_verifikasi)
                        ->translatedFormat('d M Y')
                    : 'Menunggu verifikasi',
                'ikon' => 'verified_user',
                'keterangan' => $tahap3Status === 'done'
                    ? 'Pengajuan telah disetujui oleh Fasilitator.'
                    : 'Menunggu persetujuan akhir Fasilitator.',
            ],
            [
                'urutan' => 4,
                'judul' => 'Surat Bebas Terbit',
                'subjudul' => 'Penerbitan SKBA',
                'status' => $tahap4Status,
                'waktu' => $checkout?->suratBebasAsrama?->tanggal_terbit
                    ? Carbon::parse($checkout->suratBebasAsrama->tanggal_terbit)
                        ->translatedFormat('d M Y')
                    : 'Belum terbit',
                'ikon' => 'workspace_premium',
                'keterangan' => $tahap4Status === 'done'
                    ? 'Surat Bebas Asrama telah diterbitkan.'
                    : 'SKBA akan tersedia setelah persetujuan selesai.',
            ],
        ];

        // Data hasil pemeriksaan GO
        $pengecekanModel = $checkout?->pengecekan;
        $petugasGo = $pengecekanModel?->pengguna;

        $nomorAudit = $checkout
            ? '#CHK-' . now()->format('Y') . '-'
                . str_pad($checkout->id_pengajuan, 4, '0', STR_PAD_LEFT)
            : '-';

        $denda = $checkout?->denda_kerusakan ?? 0;
        $deposit = $checkout?->nominal_deposit ?? 0;

        $inspeksi = [
            'petugas' => $petugasGo?->nama_lengkap ?? 'Belum ditugaskan',
            'role_petugas' => 'General Officer (GO)',
            'nip_petugas' => '-',
            'no_registrasi' => $nomorAudit,
            'tanggal_cek' => $pengecekanModel?->tanggal_cek
                ? Carbon::parse($pengecekanModel->tanggal_cek)
                    ->translatedFormat('d F Y')
                : 'Belum diperiksa',
            'kondisi_aset' => $pengecekanModel?->kondisi_kamar
                ? ucwords(str_replace('_', ' ', $pengecekanModel->kondisi_kamar))
                : 'Belum diperiksa',
            'total_komponen' => null,
            'komponen_baik' => null,
            'komponen_rusak' => null,
            'temuan_denda' => $denda,
            'temuan_denda_formatted' => 'Rp ' . number_format($denda, 0, ',', '.'),
            'catatan' => $pengecekanModel?->catatan ?? 'Belum ada catatan pemeriksaan.',
            'serah_terima_akses' => [
                'kartu_rfid' => 'Belum ada data serah terima',
                'kunci_fisik' => 'Belum ada data serah terima',
                'status' => 'Belum diverifikasi',
            ],
            'deposit_refund' => [
                'nominal' => $deposit,
                'nominal_formatted' => 'Rp ' . number_format($deposit, 0, ',', '.'),
                'bank' => $checkout?->nama_bank ?? '-',
                'nomor_rekening' => $checkout?->nomor_rekening ?? '-',
                'atas_nama' => $checkout?->nama_pemilik_rekening ?? '-',
                'status' => $checkout?->status_deposit ?? 'Belum ada pengajuan',
                'keterangan' => $checkout
                    ? 'Informasi pengembalian mengikuti status pengajuan.'
                    : 'Belum terdapat pengajuan pengembalian deposit.',
            ],
        ];

        // Data SKBA
        $suratBebasModel = $checkout?->suratBebasAsrama;

        $suratBebas = [
            'nomor_surat' => $suratBebasModel?->nomor_surat ?? '-',
            'tanggal_terbit' => $suratBebasModel?->tanggal_terbit
                ? Carbon::parse($suratBebasModel->tanggal_terbit)
                    ->translatedFormat('d F Y')
                : '-',
            'status' => $suratBebasModel
                ? 'Dokumen Telah Diterbitkan'
                : 'Menunggu Penerbitan',
            'integrasi_portal' => '-',
            'pejabat_nama' => '-',
            'pejabat_jabatan' => '-',
            'pejabat_nip' => '-',
            'seal_text' => '-',
            'verification_code' => '-',
            'is_ready' => (bool) $suratBebasModel,
        ];

        return view('checkout.index', compact(
            'penghuni',
            'hunian',
            'checkout',
            'tahapan',
            'progressCheckout',
            'inspeksi',
            'suratBebas',
            'kelayakan'
        ));
    }

    /**
     * Simpan pengajuan check-out mandiri.
     */
    public function store(Request $request)
    {
        $minDate = Carbon::today('Asia/Jakarta')
            ->addDays(2)
            ->format('Y-m-d');

        $validated = $request->validate([
            'tanggal_rencanaKeluar' => [
                'required',
                'date',
                'after_or_equal:' . $minDate,
            ],
            'alasan' => [
                'required',
                'string',
                'max:1000',
            ],
            'nama_bank' => [
                'required',
                'string',
                'max:50',
            ],
            'nomor_rekening' => [
                'required',
                'string',
                'max:50',
            ],
            'nama_pemilik_rekening' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'tanggal_rencanaKeluar.after_or_equal' =>
                'Rencana tanggal keluar minimal H+2 dari hari ini ('
                . Carbon::parse($minDate)->translatedFormat('d F Y')
                . ') untuk penjadwalan audit GO.',
            'alasan.required' =>
                'Harap isi alasan pengajuan check-out Anda.',
            'nama_bank.required' =>
                'Harap isi nama bank tujuan pengembalian deposit.',
            'nomor_rekening.required' =>
                'Harap isi nomor rekening tujuan pengembalian deposit.',
            'nama_pemilik_rekening.required' =>
                'Harap isi nama pemilik rekening.',
        ]);

        $client = $this->getClientLogin();

        try {
            $this->checkoutService->ajukanCheckout($client, $validated);

            return redirect()->route('checkout.index')
                ->with(
                    'success',
                    'Permohonan Check-out & Bebas Asrama berhasil diajukan! Sistem meneruskan pengajuan ke Petugas General Officer (GO).'
                );
        } catch (ValidationException $e) {
            return redirect()->route('checkout.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('checkout.index')
                ->with(
                    'error',
                    'Gagal memproses pengajuan. Silakan coba kembali.'
                );
        }
    }

    /**
     * Mengambil data pengajuan dan surat milik Client yang login.
     */
    private function getCheckoutClient(Client $client): PengajuanCheckout
    {
        $kelayakan = $this->checkoutService
            ->cekKelayakanCheckout($client);

        $checkout = $kelayakan['checkout_aktif'] ?? null;

        abort_if(
            !$checkout,
            404,
            'Pengajuan check-out tidak ditemukan.'
        );

        $checkout->load([
            'penempatanKamar.kamar.gedung',
            'suratBebasAsrama',
        ]);

        abort_if(
            !$checkout->suratBebasAsrama,
            404,
            'Surat Bebas Asrama belum diterbitkan.'
        );

        return $checkout;
    }

    /**
     * Menyiapkan data penghuni dan hunian untuk dokumen SKBA.
     */
    private function dataSurat(
        PengajuanCheckout $checkout,
        Client $client
    ): array {
        $penempatan = $checkout->penempatanKamar;
        $suratModel = $checkout->suratBebasAsrama;

        $penghuni = [
            'nama' => $client->pengguna?->nama_lengkap ?? '-',
            'nim' => $client->nomor_identitas ?? '-',
            'prodi' => $client->pekerjaan ?? '-',
            'fakultas' => '-',
        ];

        $hunian = [
            'gedung' => $penempatan?->kamar?->gedung?->nama_gedung ?? '-',
            'kamar' => $penempatan?->kamar?->nomor_kamar
                ? $penempatan->kamar->nomor_kamar
                    . ($penempatan->nomor_bed
                        ? ' - Bed ' . chr(64 + $penempatan->nomor_bed)
                        : '')
                : '-',
            'periode' => '-',
        ];

        $suratBebas = [
            'nomor_surat' => $suratModel->nomor_surat ?? '-',
            'tanggal_terbit' => $suratModel->tanggal_terbit
                ? Carbon::parse($suratModel->tanggal_terbit)
                    ->translatedFormat('d F Y')
                : '-',
            'pejabat_nama' => '-',
            'pejabat_jabatan' => '-',
            'pejabat_nip' => '-',
            'verification_code' => '-',
        ];

        return compact('penghuni', 'hunian', 'suratBebas');
    }

    /**
     * Unduh / tampilkan Surat Bebas Asrama.
     */
    public function downloadSurat(Request $request)
    {
        $client = $this->getClientLogin();
        $checkout = $this->getCheckoutClient($client);

        $data = $this->dataSurat($checkout, $client);

        return response()->view(
            'checkout.print-skba',
            $data,
            200,
            [
                'Content-Disposition' =>
                    'inline; filename="SKBA_'
                    . str_replace(
                        '/',
                        '_',
                        $data['suratBebas']['nomor_surat']
                    )
                    . '.html"',
            ]
        );
    }

    /**
     * Pratinjau Dokumen Surat Bebas Asrama.
     */
    public function previewSurat(Request $request)
    {
        $client = $this->getClientLogin();
        $checkout = $this->getCheckoutClient($client);

        $data = $this->dataSurat($checkout, $client);

        return view('checkout.print-skba', $data);
    }
}
