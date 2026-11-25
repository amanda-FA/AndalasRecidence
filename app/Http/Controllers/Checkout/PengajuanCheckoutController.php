<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PenempatanKamar;
use App\Models\PengajuanCheckout;
use App\Models\Pengecekan;
use App\Models\SuratBebasAsrama;
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
     * Tampilan utama Client - Check-out & Bebas Asrama.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil data client dari user login atau fallback data demo client pertama
        $client = null;
        if ($user) {
            $client = Client::with(['pengguna', 'jenisClient'])
                ->where('id_pengguna', $user->id_pengguna)
                ->first();
        }

        if (!$client) {
            $client = Client::with(['pengguna', 'jenisClient'])->first();
        }

        // Cek kelayakan dan status finansial melalui CheckoutService
        $kelayakan = $client ? $this->checkoutService->cekKelayakanCheckout($client) : [
            'eligible' => true,
            'penempatan' => null,
            'finansial' => ['bebas_tunggakan' => true, 'status_label' => 'Bebas Tunggakan / Lunas', 'tunggakan_nominal' => 0],
            'checkout_aktif' => null,
            'pesan' => 'Siap mengajukan check-out.',
        ];

        $penempatan = $kelayakan['penempatan'];
        if (!$penempatan) {
            $penempatan = PenempatanKamar::with([
                'kamar.gedung',
                'pendaftaranHunian.client.pengguna',
                'pengajuanCheckout.pengecekan.pengguna',
                'pengajuanCheckout.suratBebasAsrama',
            ])->latest('id_penempatan')->first();
        }

        // Data Penghuni
        $clientModel = $penempatan?->pendaftaranHunian?->client ?? $client;
        $penggunaModel = $clientModel?->pengguna ?? $user;

        $penghuni = [
            'nama' => $penggunaModel?->nama_lengkap ?? 'Muhammad Fadhil Ar-Rayyan',
            'nim' => $clientModel?->nomor_identitas ?? '2211522004',
            'prodi' => $clientModel?->pekerjaan ?? 'S1 Sistem Informasi',
            'fakultas' => 'Fakultas Teknologi Informasi',
            'kontak' => $penggunaModel?->no_hp ?? '0812-3456-7806',
            'email' => $penggunaModel?->email ?? 'fadhil.arrayyan@student.unand.ac.id',
            'alamat' => $clientModel?->alamat_asal ?? 'Kota Padang, Sumatera Barat',
        ];

        // Data Hunian & Kamar
        $kamar = $penempatan?->kamar;
        $gedung = $kamar?->gedung;
        $finansial = $kelayakan['finansial'];

        $checkout = $kelayakan['checkout_aktif'] ?? $penempatan?->pengajuanCheckout ?? PengajuanCheckout::with(['pengecekan.pengguna', 'suratBebasAsrama'])->first();

        $tglKeluarRencana = $checkout?->tanggal_rencanaKeluar
            ? Carbon::parse($checkout->tanggal_rencanaKeluar)->translatedFormat('d F Y')
            : ($penempatan?->rencana_keluar ? Carbon::parse($penempatan->rencana_keluar)->translatedFormat('d F Y') : '24 November 2026');

        $hunian = [
            'id_penempatan' => $penempatan?->id_penempatan ?? 1,
            'gedung' => $gedung?->nama_gedung ?? 'Gedung Menpora Putra',
            'nomor_kamar' => $kamar?->nomor_kamar ?? '202',
            'bed' => $penempatan?->nomor_bed ? 'Bed ' . chr(64 + $penempatan->nomor_bed) : 'Bed A',
            'lantai' => $kamar?->lantai ?? 2,
            'tipe_kamar' => 'Standar Mahasiswa (2 Orang / Kamar)',
            'periode' => 'Semester Ganjil 2026/2027',
            'tanggal_masuk' => $penempatan?->tanggal_masuk ? Carbon::parse($penempatan->tanggal_masuk)->translatedFormat('d F Y') : '15 Agustus 2025',
            'tanggal_berakhir_sewa' => $tglKeluarRencana,
            'status_finansial' => $finansial['status_label'] ?? 'Bebas Tunggakan / Lunas',
            'bebas_tunggakan' => $finansial['bebas_tunggakan'] ?? true,
            'nominal_deposit' => $checkout?->nominal_deposit ?? 50000,
            'status_deposit' => $checkout?->status_deposit ?? 'Tercatat Aktif & Siap Dikembalikan',
        ];

        // Pemetaan Dinamis 4 Tahapan Automasi Check-out
        $statusCurrent = $checkout?->status_pengajuan ?? 'belum_diajukan';

        $tahap1Status = $checkout ? 'done' : 'pending';
        $tahap2Status = 'pending';
        $tahap3Status = 'pending';
        $tahap4Status = 'pending';

        if (in_array($statusCurrent, ['audit_go', 'dalam_pengecekan'], true)) {
            $tahap2Status = 'in_progress';
        } elseif (in_array($statusCurrent, ['menunggu_persetujuan'], true)) {
            $tahap2Status = 'done';
            $tahap3Status = 'in_progress';
        } elseif (in_array($statusCurrent, [PengajuanCheckout::STATUS_DISETUJUI, 'selesai'], true)) {
            $tahap2Status = 'done';
            $tahap3Status = 'done';
            $tahap4Status = 'done';
        }

        $tahapan = [
            [
                'urutan' => 1,
                'judul' => 'Pengajuan Dikirim',
                'subjudul' => 'Permohonan Check-out Mahasiswa',
                'status' => $tahap1Status,
                'waktu' => $checkout?->tanggal_pengajuan ? Carbon::parse($checkout->tanggal_pengajuan)->translatedFormat('d M Y') : 'Menunggu Pengajuan',
                'ikon' => 'assignment_turned_in',
                'keterangan' => $checkout ? 'Pengajuan formulir check-out mandiri telah berhasil dikirim ke sistem.' : 'Formulir check-out mandiri belum dikirim oleh mahasiswa.',
            ],
            [
                'urutan' => 2,
                'judul' => 'Pengecekan GO (Fisik Kamar & Kunci)',
                'subjudul' => 'Audit Lapangan & Serah Terima Kunci',
                'status' => $tahap2Status,
                'waktu' => $checkout?->pengecekan?->tanggal_cek ? Carbon::parse($checkout->pengecekan->tanggal_cek)->translatedFormat('d M Y') : 'Jadwal H-1 Keluar',
                'ikon' => 'fact_check',
                'keterangan' => $tahap2Status === 'done'
                    ? 'Pemeriksaan fisik 9 fasilitas kamar dan serah terima kartu RFID telah selesai diverifikasi oleh staf GO.'
                    : ($tahap2Status === 'in_progress' ? 'Staf General Officer (GO) sedang melakukan audit fisik kamar dan inventaris.' : 'Menunggu penjadwalan inspeksi fisik kamar oleh staf GO.'),
            ],
            [
                'urutan' => 3,
                'judul' => 'Approval Fasilitator',
                'subjudul' => 'Verifikasi Final & Bebas Kewajiban',
                'status' => $tahap3Status,
                'waktu' => $checkout?->tangal_verifikasi ? Carbon::parse($checkout->tangal_verifikasi)->translatedFormat('d M Y') : 'Menunggu Audit Selesai',
                'ikon' => 'verified_user',
                'keterangan' => $tahap3Status === 'done'
                    ? 'Fasilitator asrama menyetujui hasil audit, menetapkan denda Rp 0, dan meneruskan pencairan deposit jaminan.'
                    : 'Persetujuan akhir oleh Fasilitator Asrama sebelum penerbitan SKBA.',
            ],
            [
                'urutan' => 4,
                'judul' => 'Surat Bebas Terbit',
                'subjudul' => 'Penerbitan SKBA Otomatis',
                'status' => $tahap4Status,
                'waktu' => $checkout?->suratBebasAsrama?->tanggal_terbit ? Carbon::parse($checkout->suratBebasAsrama->tanggal_terbit)->translatedFormat('d M Y') : 'Terbit Otomatis',
                'ikon' => 'workspace_premium',
                'keterangan' => $tahap4Status === 'done'
                    ? 'Surat Bebas Asrama (SKBA) telah resmi terbit ber-seal digital dan terhubung ke Portal Akademik UNAND.'
                    : 'Dokumen SKBA akan otomatis terbit begitu Fasilitator menyetujui check-out.',
            ],
        ];

        // Hasil Inspeksi Kamar Fisik oleh GO
        $pengecekanModel = $checkout?->pengecekan;
        $petugasGo = $pengecekanModel?->pengguna;

        $inspeksi = [
            'petugas' => $petugasGo?->nama_lengkap ?? 'Rahardian Pratama, A.Md.',
            'role_petugas' => 'General Officer (GO) Lapangan',
            'nip_petugas' => 'GO-2024-0012',
            'no_registrasi' => '#CHK-' . now()->format('Y') . '-' . str_pad($checkout?->id_pengajuan ?? 1, 4, '0', STR_PAD_LEFT),
            'tanggal_cek' => $pengecekanModel?->tanggal_cek ? Carbon::parse($pengecekanModel->tanggal_cek)->translatedFormat('d F Y') : '22 November 2026 (14:30 WIB)',
            'kondisi_aset' => $pengecekanModel?->kondisi_kamar === 'baik' || !$pengecekanModel ? 'Lengkap & Bersih' : ucwords(str_replace('_', ' ', $pengecekanModel->kondisi_kamar)),
            'total_komponen' => 9,
            'komponen_baik' => 9,
            'komponen_rusak' => 0,
            'temuan_denda' => $checkout?->denda_kerusakan ?? 0,
            'temuan_denda_formatted' => 'Rp ' . number_format($checkout?->denda_kerusakan ?? 0, 0, ',', '.'),
            'catatan' => $pengecekanModel?->catatan ?: 'Kondisi ruangan sangat rapi dan bersih. Tidak ada kerusakan aset yang disebabkan kelalaian. Seluruh inventaris fisik kamar lengkap.',
            'serah_terima_akses' => [
                'kartu_rfid' => 'RFID-MNP-0202A (Diserahkan & Terbaca Valid)',
                'kunci_fisik' => 'Kunci Fisik Master Kamar (Diserahkan Lengkap)',
                'status' => 'Lengkap & Tervalidasi',
            ],
            'deposit_refund' => [
                'nominal' => $checkout?->nominal_deposit ?? 50000,
                'nominal_formatted' => 'Rp ' . number_format($checkout?->nominal_deposit ?? 50000, 0, ',', '.'),
                'bank' => $checkout?->nama_bank ?? 'Bank Nagari',
                'nomor_rekening' => $checkout?->nomor_rekening ?? '2100.0210.45892-1',
                'atas_nama' => $checkout?->nama_pemilik_rekening ?? $penghuni['nama'],
                'status' => $checkout?->status_deposit === 'dikembalikan' ? 'Dana Dikembalikan / Sukses' : 'Diproses Otomatis / Dikembalikan',
                'keterangan' => 'Dana jaminan dikembalikan penuh tanpa potongan karena tidak terdapat denda kerusakan fasilitas.',
            ],
        ];

        // Surat Bebas Asrama (SKBA)
        $suratBebasModel = $checkout?->suratBebasAsrama;
        $suratBebas = [
            'nomor_surat' => $suratBebasModel?->nomor_surat ?? 'SKBA/UNAND/ASR/2026/0892',
            'tanggal_terbit' => $suratBebasModel?->tanggal_terbit ? Carbon::parse($suratBebasModel->tanggal_terbit)->translatedFormat('d F Y') : '23 November 2026',
            'status' => $tahap4Status === 'done' ? 'Dokumen Sah & Aktif' : 'Menunggu Approval Fasilitator',
            'integrasi_portal' => 'Terkoneksi langsung ke Portal Akademik UNAND (Syarat KRS & Wisuda)',
            'pejabat_nama' => 'Dr. Ir. H. Syamsul Bahri, M.T.',
            'pejabat_jabatan' => 'Kepala UPT Asrama Mahasiswa Universitas Andalas',
            'pejabat_nip' => '19750814 200212 1 002',
            'seal_text' => 'UNIVERSITAS ANDALAS - UPT ASRAMA MAHASISWA - DIGITAL SEALED',
            'verification_code' => 'SKBA-2026-UNAND-0892-VALID',
            'is_ready' => $tahap4Status === 'done',
        ];

        return view('checkout.index', compact(
            'penghuni',
            'hunian',
            'checkout',
            'tahapan',
            'inspeksi',
            'suratBebas',
            'kelayakan'
        ));
    }

    /**
     * Simpan pengajuan check-out mandiri (single submission).
     */
    public function store(Request $request)
    {
        $minDate = Carbon::today('Asia/Jakarta')->addDays(2)->format('Y-m-d');

        $validated = $request->validate([
            'tanggal_rencanaKeluar' => 'required|date|after_or_equal:' . $minDate,
            'alasan' => 'required|string|max:1000',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'nama_pemilik_rekening' => 'nullable|string|max:100',
        ], [
            'tanggal_rencanaKeluar.after_or_equal' => 'Rencana tanggal keluar minimal H+2 dari hari ini (' . Carbon::parse($minDate)->translatedFormat('d F Y') . ') untuk penjadwalan audit GO.',
            'alasan.required' => 'Harap isi alasan pengajuan check-out Anda.',
        ]);

        $user = Auth::user();
        $client = null;
        if ($user) {
            $client = Client::where('id_pengguna', $user->id_pengguna)->first();
        }
        if (!$client) {
            $client = Client::first();
        }

        if (!$client) {
            return redirect()->route('checkout.index')->with('error', 'Profil data Client tidak ditemukan.');
        }

        try {
            $this->checkoutService->ajukanCheckout($client, $validated);

            return redirect()->route('checkout.index')
                ->with('success', 'Permohonan Check-out & Bebas Asrama berhasil diajukan! Sistem otomatis meneruskan jadwal audit fisik kamar ke Petugas General Officer (GO).');
        } catch (ValidationException $e) {
            return redirect()->route('checkout.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')
                ->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Unduh Surat Bebas Asrama (.PDF / Printable Document).
     */
    public function downloadSurat(Request $request)
    {
        $user = Auth::user();
        $client = $user ? Client::with('pengguna')->where('id_pengguna', $user->id_pengguna)->first() : null;
        if (!$client) {
            $client = Client::with('pengguna')->first();
        }

        $checkout = PengajuanCheckout::with(['penempatanKamar.kamar.gedung', 'suratBebasAsrama'])->latest('id_pengajuan')->first();
        $penempatan = $checkout?->penempatanKamar;

        $penghuni = [
            'nama' => $client?->pengguna?->nama_lengkap ?? 'Muhammad Fadhil Ar-Rayyan',
            'nim' => $client?->nomor_identitas ?? '2211522004',
            'prodi' => $client?->pekerjaan ?? 'S1 Sistem Informasi',
            'fakultas' => 'Fakultas Teknologi Informasi',
        ];

        $hunian = [
            'gedung' => $penempatan?->kamar?->gedung?->nama_gedung ?? 'Gedung Menpora Putra',
            'kamar' => ($penempatan?->kamar?->nomor_kamar ?? '202') . ' - Bed A',
            'periode' => 'Semester Ganjil 2026/2027',
        ];

        $suratBebas = [
            'nomor_surat' => $checkout?->suratBebasAsrama?->nomor_surat ?? 'SKBA/UNAND/ASR/2026/0892',
            'tanggal_terbit' => $checkout?->suratBebasAsrama?->tanggal_terbit ? Carbon::parse($checkout->suratBebasAsrama->tanggal_terbit)->translatedFormat('d F Y') : '23 November 2026',
            'pejabat_nama' => 'Dr. Ir. H. Syamsul Bahri, M.T.',
            'pejabat_jabatan' => 'Kepala UPT Asrama Mahasiswa Universitas Andalas',
            'pejabat_nip' => '19750814 200212 1 002',
            'verification_code' => 'SKBA-2026-UNAND-0892-VALID',
        ];

        return response()->view('checkout.print-skba', compact('penghuni', 'hunian', 'suratBebas'), 200, [
            'Content-Disposition' => 'inline; filename="SKBA_' . str_replace('/', '_', $suratBebas['nomor_surat']) . '.html"',
        ]);
    }

    /**
     * Pratinjau Dokumen Surat Bebas Asrama.
     */
    public function previewSurat(Request $request)
    {
        $user = Auth::user();
        $client = $user ? Client::with('pengguna')->where('id_pengguna', $user->id_pengguna)->first() : null;
        if (!$client) {
            $client = Client::with('pengguna')->first();
        }

        $checkout = PengajuanCheckout::with(['penempatanKamar.kamar.gedung', 'suratBebasAsrama'])->latest('id_pengajuan')->first();
        $penempatan = $checkout?->penempatanKamar;

        $penghuni = [
            'nama' => $client?->pengguna?->nama_lengkap ?? 'Muhammad Fadhil Ar-Rayyan',
            'nim' => $client?->nomor_identitas ?? '2211522004',
            'prodi' => $client?->pekerjaan ?? 'S1 Sistem Informasi',
            'fakultas' => 'Fakultas Teknologi Informasi',
        ];

        $hunian = [
            'gedung' => $penempatan?->kamar?->gedung?->nama_gedung ?? 'Gedung Menpora Putra',
            'kamar' => ($penempatan?->kamar?->nomor_kamar ?? '202') . ' - Bed A',
            'periode' => 'Semester Ganjil 2026/2027',
        ];

        $suratBebas = [
            'nomor_surat' => $checkout?->suratBebasAsrama?->nomor_surat ?? 'SKBA/UNAND/ASR/2026/0892',
            'tanggal_terbit' => $checkout?->suratBebasAsrama?->tanggal_terbit ? Carbon::parse($checkout->suratBebasAsrama->tanggal_terbit)->translatedFormat('d F Y') : '23 November 2026',
            'pejabat_nama' => 'Dr. Ir. H. Syamsul Bahri, M.T.',
            'pejabat_jabatan' => 'Kepala UPT Asrama Mahasiswa Universitas Andalas',
            'pejabat_nip' => '19750814 200212 1 002',
            'verification_code' => 'SKBA-2026-UNAND-0892-VALID',
        ];

        return view('checkout.print-skba', compact('penghuni', 'hunian', 'suratBebas'));
    }
}
