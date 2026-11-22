<?php

namespace App\Http\Controllers\GO;

use App\Http\Controllers\Controller;
use App\Models\Pengecekan;
use App\Models\LaporanKerusakan;

class RiwayatPengecekanController extends Controller
{
    public function index()
    {
        $pengecekans = Pengecekan::with([
            'pengajuanCheckout.penempatanKamar.kamar.gedung',
            'pengajuanCheckout.penempatanKamar.pendaftaranHunian.client.pengguna',
            'pengajuanCheckout.suratCheckout',
            'pengajuanCheckout.suratBebasAsrama',
            'pengguna',
        ])->where('status_verifikasi', 'selesai')
          ->orderByDesc('tanggal_cek')
          ->get();

        $totalDiperiksa = $pengecekans->count();
        $layakHuni = $pengecekans->where('kondisi_kamar', 'baik')->count();
        $tingkatKelayakan = $totalDiperiksa > 0 ? round(($layakHuni / $totalDiperiksa) * 100) : 0;
        $temuanKerusakan = LaporanKerusakan::count();

        $metrics = [
            'total_diperiksa' => $totalDiperiksa,
            'tingkat_kelayakan' => $tingkatKelayakan,
            'temuan_kerusakan' => $temuanKerusakan,
            'rata_waktu' => 22,
        ];

        $riwayat = $pengecekans->map(function ($cek) {
            $checkout = $cek->pengajuanCheckout;
            $penempatan = $checkout?->penempatanKamar;
            $kamar = $penempatan?->kamar;
            $gedung = $kamar?->gedung;
            $client = $penempatan?->pendaftaranHunian?->client;
            $pengguna = $client?->pengguna;

            $kondisiMap = [
                'baik' => ['label' => 'Layak Huni', 'color' => 'bg-success/10 text-success'],
                'rusak_ringan' => ['label' => 'Rusak Ringan', 'color' => 'bg-[#C97D1A]/10 text-[#C97D1A]'],
                'rusak_berat' => ['label' => 'Rusak Berat', 'color' => 'bg-error/10 text-error'],
            ];

            $k = $kondisiMap[$cek->kondisi_kamar] ?? ['label' => 'Dalam Pengecekan', 'color' => 'bg-surface-container text-on-surface'];

            return [
                'id' => $cek->id_pengecekan,
                'nama' => $pengguna?->nama_lengkap ?? 'N/A',
                'kamar' => $kamar?->nomor_kamar ?? '-',
                'gedung' => $gedung?->nama_gedung ?? '-',
                'tanggal_cek' => $cek->tanggal_cek?->format('d M Y') ?? '-',
                'kondisi_label' => $k['label'],
                'kondisi_color' => $k['color'],
                'catatan' => $cek->catatan,
                'kunci_fisik' => $checkout?->suratCheckout ? 'Dikembalikan' : 'Belum',
                'rfid' => $checkout?->suratBebasAsrama ? 'Dikembalikan' : 'Belum',
                'validasi' => $checkout?->suratBebasAsrama ? 'Tervalidasi' : 'Menunggu',
                'petugas' => $cek->pengguna?->nama_lengkap ?? '-',
            ];
        });

        return view('go.riwayat-pengecekan', compact('metrics', 'riwayat'));
    }
}
