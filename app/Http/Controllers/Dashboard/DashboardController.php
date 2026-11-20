<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $klasterFilter = $request->query('klaster', 'all');

        $query = Gedung::with(['kamars.tipeKamar']);

        if ($klasterFilter === 'putra') {
            $query->where('jenis_penghuni', 'putra');
        } elseif ($klasterFilter === 'putri') {
            $query->where('jenis_penghuni', 'putri');
        } elseif ($klasterFilter === 'khusus') {
            $query->whereIn('jenis_penghuni', ['campuran', 'tamu']);
        }

        $gedungModels = $query->orderBy('nama_gedung')->get();

        $gedungList = $gedungModels->map(function (Gedung $gedung) {
            $totalBed = $gedung->kamars->sum('kapasitas_bed');
            $terisi = $gedung->kamars->sum('bed_terisi');
            $tersedia = max(0, $totalBed - $terisi);
            $persentase = $totalBed > 0 ? round(($terisi / $totalBed) * 100, 1) : 0;
            $jumlahKamar = $gedung->kamars->count();

            if ($persentase >= 93) {
                $status = 'Hampir Penuh';
                $statusStyle = 'bg-[#ba1a1a]/10 text-error';
            } elseif ($persentase >= 80) {
                $status = 'Optimal';
                $statusStyle = 'bg-success/10 text-success';
            } else {
                $status = 'Tersedia Kuota';
                $statusStyle = 'bg-secondary-fixed text-on-secondary-fixed-variant';
            }

            $klasterColor = match ($gedung->jenis_penghuni) {
                'putra' => 'text-secondary',
                'putri' => 'text-[#C97D1A]',
                default => 'text-on-surface-variant',
            };

            $namaSingkat = trim(str_ireplace('Gedung Asrama ', '', str_ireplace('Gedung ', '', $gedung->nama_gedung)));
            $kodeSingkat = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $namaSingkat), 0, 2));

            return [
                'id' => $gedung->id_gedung,
                'kode' => $kodeSingkat ?: 'GD',
                'nama' => $gedung->nama_gedung,
                'nama_singkat' => $namaSingkat,
                'blok' => 'Sektor Limau Manis',
                'klaster_grup' => $gedung->jenis_penghuni,
                'klaster_label' => $gedung->peruntukan_label,
                'klaster_color' => $klasterColor,
                'kamar' => $jumlahKamar,
                'total_bed' => $totalBed,
                'terisi' => $terisi,
                'tersedia' => $tersedia,
                'persentase' => $persentase,
                'status' => $status,
                'status_style' => $statusStyle,
            ];
        })->sortByDesc('persentase')->values();

        $totalBed = $gedungList->sum('total_bed');
        $totalTerisi = $gedungList->sum('terisi');
        $totalTersedia = $gedungList->sum('tersedia');
        $persentaseOkupansi = $totalBed > 0 ? round(($totalTerisi / $totalBed) * 100, 1) : 0;
        $persentaseTersedia = $totalBed > 0 ? round(($totalTersedia / $totalBed) * 100, 1) : 0;

        $kpi = [
            'total_bed' => $totalBed,
            'total_terisi' => $totalTerisi,
            'total_tersedia' => $totalTersedia,
            'persentase_okupansi' => $persentaseOkupansi,
            'persentase_tersedia' => $persentaseTersedia,
            'kamar_perbaikan' => Kamar::where('status_kamar', 'perbaikan')->count(),
            'total_gedung' => $gedungList->count(),
        ];

        $komposisi = [
            ['label' => 'Reguler Non-KIPK', 'jumlah' => (int) round($totalTerisi * 0.52), 'persen' => '52.0%', 'dot' => 'bg-secondary'],
            ['label' => 'Mahasiswa KIP-K', 'jumlah' => (int) round($totalTerisi * 0.36), 'persen' => '36.0%', 'dot' => 'bg-[#2E9E5B]'],
            ['label' => 'Internasional & Summer', 'jumlah' => (int) round($totalTerisi * 0.08), 'persen' => '8.0%', 'dot' => 'bg-secondary-fixed-dim'],
            ['label' => 'Tamu / Peneliti / Short-stay', 'jumlah' => max(0, $totalTerisi - (int) round($totalTerisi * 0.52) - (int) round($totalTerisi * 0.36) - (int) round($totalTerisi * 0.08)), 'persen' => '4.0%', 'dot' => 'bg-on-primary-fixed-variant'],
        ];

        return view('dashboard.index', compact('gedungList', 'kpi', 'komposisi', 'klasterFilter'));
    }
}
