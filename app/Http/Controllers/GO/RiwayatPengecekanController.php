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

        $stats = [
            'total_diperiksa' => $totalDiperiksa,
            'tingkat_kelayakan' => $tingkatKelayakan,
            'temuan_kerusakan' => $temuanKerusakan,
            'rata_rata_audit' => 20,
        ];
        $metrics = $stats;

        $riwayat = $pengecekans->map(function ($cek) {
            $checkout = $cek->pengajuanCheckout;
            $penempatan = $checkout?->penempatanKamar;
            $kamar = $penempatan?->kamar;
            $gedung = $kamar?->gedung;
            $client = $penempatan?->pendaftaranHunian?->client;
            $pengguna = $client?->pengguna;

            $item = new \stdClass();
            $item->id = $cek->id_pengecekan;
            $item->pengguna = (object)[
                'nama_lengkap' => $pengguna?->nama_lengkap ?? 'N/A',
            ];
            $item->gedung = (object)[
                'nama_gedung' => $gedung?->nama_gedung ?? '-',
            ];
            $item->kamar = (object)[
                'nomor_kamar' => $kamar?->nomor_kamar ?? '-',
            ];
            $item->tanggal_audit = $cek->tanggal_cek ?? now();
            $item->kondisi = $cek->kondisi_kamar ?? 'baik';
            $item->kunci_fisik = !empty($checkout?->suratCheckout);
            $item->rfid_valid = !empty($checkout?->suratBebasAsrama);
            $item->validasi = !empty($checkout?->suratBebasAsrama);
            $item->petugas = (object)[
                'nama_lengkap' => $cek->pengguna?->nama_lengkap ?? '-',
            ];

            return $item;
        });

        return view('go.riwayat-pengecekan', compact('stats', 'metrics', 'riwayat'));
    }
}
