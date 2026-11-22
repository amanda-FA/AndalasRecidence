<?php

namespace App\Http\Controllers\GO;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use Illuminate\Http\Request;

class LaporanKerusakanGOController extends Controller
{
    public function index()
    {
        $laporans = LaporanKerusakan::with([
            'fasilitas.kamar.gedung',
            'fasilitas.gedung',
            'pengguna',
            'penugasanPerbaikans.pengguna',
        ])->orderByDesc('tanggal_lapor')->get();

        $stats = [
            'total' => $laporans->count(),
            'menunggu_disposisi' => $laporans->where('status_perbaikan', 'diajukan')->count(),
            'dalam_perbaikan' => $laporans->whereIn('status_perbaikan', ['ditugaskan', 'dalam_pengerjaan'])->count(),
            'selesai' => $laporans->where('status_perbaikan', 'selesai')->count(),
        ];

        $tikets = $laporans->map(function ($laporan) {
            $fasilitas = $laporan->fasilitas;
            $kamar = $fasilitas?->kamar;
            $gedung = $kamar?->gedung ?? $fasilitas?->gedung;
            $penugasan = $laporan->penugasanPerbaikans->first();

            $statusMap = [
                'diajukan' => ['label' => 'Menunggu Disposisi', 'color' => 'bg-error/10 text-error'],
                'ditugaskan' => ['label' => 'Ditugaskan', 'color' => 'bg-secondary/10 text-secondary'],
                'dalam_pengerjaan' => ['label' => 'Dalam Perbaikan', 'color' => 'bg-[#C97D1A]/10 text-[#C97D1A]'],
                'selesai' => ['label' => 'Selesai Diperbaiki', 'color' => 'bg-success/10 text-success'],
            ];

            $st = $statusMap[$laporan->status_perbaikan] ?? ['label' => ucfirst($laporan->status_perbaikan), 'color' => 'bg-surface-container text-on-surface'];

            return [
                'id_laporan' => $laporan->id_laporan,
                'nomor_tiket' => 'TKT-' . now()->format('Y') . '-' . str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT),
                'fasilitas' => $fasilitas?->nama_fasilitas ?? '-',
                'kamar' => $kamar?->nomor_kamar ?? '-',
                'gedung' => $gedung?->nama_gedung ?? '-',
                'deskripsi' => $laporan->deskripsi,
                'tanggal_lapor' => $laporan->tanggal_lapor?->format('d M Y') ?? '-',
                'teknisi' => $penugasan?->pengguna?->nama_lengkap ?? 'Belum ditugaskan',
                'status_label' => $st['label'],
                'status_color' => $st['color'],
            ];
        });

        return view('go.laporan-kerusakan', compact('stats', 'tikets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_fasilitas' => 'required|exists:fasilitas,id_fasilitas',
            'deskripsi' => 'required|string|min:10|max:2000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('kerusakan', 'public');
        }

        LaporanKerusakan::create([
            'id_pengguna' => auth()->user()->id_pengguna,
            'id_fasilitas' => $request->id_fasilitas,
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
            'tanggal_lapor' => now(),
            'status_perbaikan' => 'diajukan',
        ]);

        return redirect()->route('go.laporan-kerusakan.index')
            ->with('success', 'Laporan kerusakan berhasil dibuat.');
    }
}
