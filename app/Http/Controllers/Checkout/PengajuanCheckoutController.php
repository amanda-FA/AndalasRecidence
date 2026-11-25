<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PenempatanKamar;
use App\Models\PengajuanCheckout;
use App\Models\Pengecekan;
use App\Models\SuratBebasAsrama;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanCheckoutController extends Controller
{
    /**
     * Tampilan utama Client - Check-out & Bebas Asrama.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil penempatan aktif atau relasi terkait bila tersedia
        $penempatan = null;
        if ($user) {
            $penempatan = PenempatanKamar::with([
                'kamar.gedung',
                'pendaftaranHunian.client.pengguna',
                'pengajuanCheckout.pengecekan.pengguna',
                'pengajuanCheckout.suratBebasAsrama',
            ])->whereHas('pendaftaranHunian.client', function ($q) use ($user) {
                $q->where('id_pengguna', $user->id_pengguna);
            })->latest('id_penempatan')->first();
        }

        if (!$penempatan) {
            $penempatan = PenempatanKamar::with([
                'kamar.gedung',
                'pendaftaranHunian.client.pengguna',
                'pengajuanCheckout.pengecekan.pengguna',
                'pengajuanCheckout.suratBebasAsrama',
            ])->first();
        }

        // Data Penghuni
        $clientModel = $penempatan?->pendaftaranHunian?->client;
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
        $hunian = [
            'id_penempatan' => $penempatan?->id_penempatan ?? 1,
            'gedung' => $gedung?->nama_gedung ?? 'Gedung Menpora Putra',
            'nomor_kamar' => $kamar?->nomor_kamar ?? '202',
            'bed' => $penempatan?->nomor_bed ? 'Bed ' . chr(64 + $penempatan->nomor_bed) : 'Bed A',
            'lantai' => $kamar?->lantai ?? 2,
            'tipe_kamar' => 'Standar Mahasiswa (2 Orang / Kamar)',
            'periode' => 'Semester Ganjil 2026/2027',
            'tanggal_masuk' => $penempatan?->tanggal_masuk ? Carbon::parse($penempatan->tanggal_masuk)->translatedFormat('d F Y') : '15 Agustus 2025',
            'tanggal_berakhir_sewa' => '24 November 2026',
            'status_finansial' => 'Bebas Tunggakan / Lunas',
            'nominal_deposit' => 50000,
            'status_deposit' => 'Tercatat Aktif & Siap Dikembalikan',
        ];

        // Pengajuan Checkout Aktif
        $checkout = $penempatan?->pengajuanCheckout ?? PengajuanCheckout::first();

        // 4 Tahapan Automasi Check-out
        $tahapan = [
            [
                'urutan' => 1,
                'judul' => 'Pengajuan Dikirim',
                'subjudul' => 'Permohonan Check-out Mahasiswa',
                'status' => 'done', // done, in_progress, pending
                'waktu' => '20 Nov 2026, 09:15 WIB',
                'ikon' => 'assignment_turned_in',
                'keterangan' => 'Pengajuan formulir check-out mandiri telah berhasil dikirim ke sistem operasional asrama.',
            ],
            [
                'urutan' => 2,
                'judul' => 'Pengecekan GO (Fisik Kamar & Kunci)',
                'subjudul' => 'Audit Lapangan & Serah Terima Kunci',
                'status' => 'done',
                'waktu' => '22 Nov 2026, 14:30 WIB',
                'ikon' => 'fact_check',
                'keterangan' => 'Pemeriksaan fisik 9 fasilitas kamar dan serah terima kartu RFID telah selesai diverifikasi oleh staf GO (Rahardian Pratama, A.Md.).',
            ],
            [
                'urutan' => 3,
                'judul' => 'Approval Fasilitator',
                'subjudul' => 'Verifikasi Final & Bebas Kewajiban',
                'status' => 'done',
                'waktu' => '23 Nov 2026, 10:00 WIB',
                'ikon' => 'verified_user',
                'keterangan' => 'Fasilitator asrama menyetujui hasil audit, menetapkan denda Rp 0, dan meneruskan pencairan deposit jaminan.',
            ],
            [
                'urutan' => 4,
                'judul' => 'Surat Bebas Terbit',
                'subjudul' => 'Penerbitan SKBA Otomatis',
                'status' => 'done',
                'waktu' => '23 Nov 2026, 10:05 WIB',
                'ikon' => 'workspace_premium',
                'keterangan' => 'Surat Bebas Asrama (SKBA) diterbitkan otomatis ber-seal digital dan terhubung ke Portal Akademik UNAND.',
            ],
        ];

        // Hasil Inspeksi Kamar Fisik oleh GO
        $inspeksi = [
            'petugas' => 'Rahardian Pratama, A.Md.',
            'role_petugas' => 'General Officer (GO) Lapangan',
            'nip_petugas' => 'GO-2024-0012',
            'no_registrasi' => '#CHK-2026-0089',
            'tanggal_cek' => '22 November 2026 (14:30 WIB)',
            'kondisi_aset' => 'Lengkap & Bersih',
            'total_komponen' => 9,
            'komponen_baik' => 9,
            'komponen_rusak' => 0,
            'temuan_denda' => 0,
            'temuan_denda_formatted' => 'Rp 0',
            'catatan' => 'Kondisi ruangan sangat rapi dan bersih. Tidak ada kerusakan aset yang disebabkan kelalaian. Seluruh inventaris fisik kamar lengkap.',
            'serah_terima_akses' => [
                'kartu_rfid' => 'RFID-MNP-0202A (Diserahkan & Terbaca Valid)',
                'kunci_fisik' => 'Kunci Fisik Master Kamar (Diserahkan Lengkap)',
                'status' => 'Lengkap & Tervalidasi',
            ],
            'deposit_refund' => [
                'nominal' => 50000,
                'nominal_formatted' => 'Rp 50.000',
                'bank' => 'Bank Nagari',
                'nomor_rekening' => '2100.0210.45892-1',
                'atas_nama' => $penghuni['nama'],
                'status' => 'Diproses Otomatis / Dikembalikan',
                'keterangan' => 'Dana jaminan dikembalikan penuh tanpa potongan karena tidak terdapat denda kerusakan fasilitas.',
            ],
        ];

        // Surat Bebas Asrama (SKBA)
        $suratBebas = [
            'nomor_surat' => 'SKBA/UNAND/ASR/2026/0892',
            'tanggal_terbit' => '23 November 2026',
            'status' => 'Dokumen Sah & Aktif',
            'integrasi_portal' => 'Terkoneksi langsung ke Portal Akademik UNAND (Syarat KRS & Wisuda)',
            'pejabat_nama' => 'Dr. Ir. H. Syamsul Bahri, M.T.',
            'pejabat_jabatan' => 'Kepala UPT Asrama Mahasiswa Universitas Andalas',
            'pejabat_nip' => '19750814 200212 1 002',
            'seal_text' => 'UNIVERSITAS ANDALAS - UPT ASRAMA MAHASISWA - DIGITAL SEALED',
            'verification_code' => 'SKBA-2026-UNAND-0892-VALID',
        ];

        return view('checkout.index', compact(
            'penghuni',
            'hunian',
            'checkout',
            'tahapan',
            'inspeksi',
            'suratBebas'
        ));
    }

    /**
     * Simpan pengajuan check-out mandiri (single submission).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_rencanaKeluar' => 'required|date',
            'alasan' => 'required|string|max:1000',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'nama_pemilik_rekening' => 'nullable|string|max:100',
        ]);

        // Simpan atau perbarui pengajuan checkout bila ada model penempatan
        $user = Auth::user();
        $penempatan = PenempatanKamar::whereHas('pendaftaranHunian.client', function ($q) use ($user) {
            $q->where('id_pengguna', $user?->id_pengguna);
        })->latest('id_penempatan')->first();

        if ($penempatan) {
            PengajuanCheckout::updateOrCreate(
                ['id_penempatan' => $penempatan->id_penempatan],
                [
                    'tanggal_pengajuan' => now(),
                    'tanggal_rencanaKeluar' => $validated['tanggal_rencanaKeluar'],
                    'alasan' => $validated['alasan'],
                    'status_pengajuan' => 'diajukan',
                    'catatan' => 'Rekening Refund: ' . ($validated['nama_bank'] ?? 'Bank Nagari') . ' - ' . ($validated['nomor_rekening'] ?? '-') . ' a.n ' . ($validated['nama_pemilik_rekening'] ?? '-'),
                ]
            );
        }

        return redirect()->route('checkout.index')->with('success', 'Permohonan Check-out & Bebas Asrama berhasil diajukan! Sistem akan otomatis memproses pengecekan fisik kamar oleh staf General Officer (GO).');
    }

    /**
     * Unduh Surat Bebas Asrama (.PDF / Printable Document).
     */
    public function downloadSurat(Request $request)
    {
        $penghuni = [
            'nama' => 'Muhammad Fadhil Ar-Rayyan',
            'nim' => '2211522004',
            'prodi' => 'S1 Sistem Informasi',
            'fakultas' => 'Fakultas Teknologi Informasi',
        ];
        $hunian = [
            'gedung' => 'Gedung Menpora Putra',
            'kamar' => '202 - Bed A',
            'periode' => 'Semester Ganjil 2026/2027',
        ];
        $suratBebas = [
            'nomor_surat' => 'SKBA/UNAND/ASR/2026/0892',
            'tanggal_terbit' => '23 November 2026',
            'pejabat_nama' => 'Dr. Ir. H. Syamsul Bahri, M.T.',
            'pejabat_jabatan' => 'Kepala UPT Asrama Mahasiswa Universitas Andalas',
            'pejabat_nip' => '19750814 200212 1 002',
            'verification_code' => 'SKBA-2026-UNAND-0892-VALID',
        ];

        return response()->view('checkout.print-skba', compact('penghuni', 'hunian', 'suratBebas'), 200, [
            'Content-Disposition' => 'inline; filename="SKBA_2026_0892_' . ($penghuni['nim']) . '.html"',
        ]);
    }

    /**
     * Pratinjau Dokumen Surat Bebas Asrama.
     */
    public function previewSurat(Request $request)
    {
        $penghuni = [
            'nama' => 'Muhammad Fadhil Ar-Rayyan',
            'nim' => '2211522004',
            'prodi' => 'S1 Sistem Informasi',
            'fakultas' => 'Fakultas Teknologi Informasi',
        ];
        $hunian = [
            'gedung' => 'Gedung Menpora Putra',
            'kamar' => '202 - Bed A',
            'periode' => 'Semester Ganjil 2026/2027',
        ];
        $suratBebas = [
            'nomor_surat' => 'SKBA/UNAND/ASR/2026/0892',
            'tanggal_terbit' => '23 November 2026',
            'pejabat_nama' => 'Dr. Ir. H. Syamsul Bahri, M.T.',
            'pejabat_jabatan' => 'Kepala UPT Asrama Mahasiswa Universitas Andalas',
            'pejabat_nip' => '19750814 200212 1 002',
            'verification_code' => 'SKBA-2026-UNAND-0892-VALID',
        ];

        return view('checkout.print-skba', compact('penghuni', 'hunian', 'suratBebas'));
    }
}
