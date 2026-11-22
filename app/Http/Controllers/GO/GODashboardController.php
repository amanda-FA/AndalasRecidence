<?php

namespace App\Http\Controllers\GO;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCheckout;
use App\Models\Pengecekan;
use App\Models\LaporanKerusakan;

class GODashboardController extends Controller
{
    public function index()
    {
        $menungguPengecekan = PengajuanCheckout::where('status_pengajuan', 'disetujui')
            ->whereDoesntHave('pengecekan')
            ->count();

        $sedangDiperiksa = Pengecekan::where('status_verifikasi', 'dalam_pengecekan')->count();
        $pengecekanSelesai = Pengecekan::where('status_verifikasi', 'selesai')->count();
        $kerusakanDitemukan = LaporanKerusakan::count();

        $stats = [
            'menunggu' => $menungguPengecekan,
            'sedang_diperiksa' => $sedangDiperiksa,
            'selesai' => $pengecekanSelesai,
            'kerusakan' => $kerusakanDitemukan,
        ];

        $pengecekanTerbaru = PengajuanCheckout::with([
            'penempatanKamar.kamar.gedung',
            'penempatanKamar.pendaftaranHunian.client.pengguna',
            'pengecekan',
        ])
        ->where('status_pengajuan', 'disetujui')
        ->orderByDesc('tanggal_pengajuan')
        ->limit(10)
        ->get()
        ->map(function ($checkout) {
            $penempatan = $checkout->penempatanKamar;
            $kamar = $penempatan?->kamar;
            $gedung = $kamar?->gedung;
            $client = $penempatan?->pendaftaranHunian?->client;
            $pengguna = $client?->pengguna;
            $pengecekan = $checkout->pengecekan;

            if ($pengecekan && $pengecekan->status_verifikasi === 'selesai') {
                $status = 'selesai';
                $statusLabel = 'Selesai Pengecekan';
                $statusColor = 'bg-success/10 text-success';
            } elseif ($pengecekan) {
                $status = 'dalam_pengecekan';
                $statusLabel = 'Sedang Diperiksa';
                $statusColor = 'bg-[#C97D1A]/10 text-[#C97D1A]';
            } else {
                $status = 'menunggu';
                $statusLabel = 'Menunggu Pengecekan';
                $statusColor = 'bg-error/10 text-error';
            }

            return [
                'id_pengajuan' => $checkout->id_pengajuan,
                'nama' => $pengguna?->nama_lengkap ?? 'N/A',
                'kamar' => $kamar?->nomor_kamar ?? '-',
                'gedung' => $gedung?->nama_gedung ?? '-',
                'tanggal_checkout' => $checkout->tanggal_rencanaKeluar?->format('d M Y') ?? '-',
                'status' => $status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
            ];
        });

        return view('go.dashboard', compact('stats', 'pengecekanTerbaru'));
    }
}
