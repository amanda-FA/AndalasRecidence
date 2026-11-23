<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\JenisClient;
use App\Models\Kamar;
use App\Models\PenempatanKamar;
use App\Models\PeriodePendaftaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.index', $this->buildOccupancyReport($request));
    }

    public function exportPdf(Request $request)
    {
        return view('dashboard.pdf', $this->buildOccupancyReport($request));
    }

    public function exportCsv(Request $request)
    {
        $report = $this->buildOccupancyReport($request);
        $filename = 'laporan-okupansi-andalas-residence-' . now('Asia/Jakarta')->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM agar Excel membaca karakter dengan benar
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Gedung', 'Klaster', 'Jumlah Kamar', 'Total Bed', 'Bed Terisi', 'Bed Tersedia', 'Okupansi (%)', 'Status']);

            foreach ($report['gedungList'] as $row) {
                fputcsv($out, [
                    $row['nama'],
                    $row['klaster_label'],
                    $row['kamar'],
                    $row['total_bed'],
                    $row['terisi'],
                    $row['tersedia'],
                    $row['persentase'] . '%',
                    $row['status'],
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildOccupancyReport(Request $request): array
    {
        $klasterFilter = $request->query('klaster', 'all');
        $periodeFilter = $request->query('periode', 'all');

        $periodes = PeriodePendaftaran::orderByDesc('tanggal_buka')
            ->orderByDesc('id_periode')
            ->get();

        $selectedPeriode = $periodeFilter !== 'all'
            ? $periodes->firstWhere('id_periode', (int) $periodeFilter)
            : null;

        $query = Gedung::with(['kamars.tipeKamar']);

        if ($klasterFilter === 'putra') {
            $query->where('jenis_penghuni', 'putra');
        } elseif ($klasterFilter === 'putri') {
            $query->where('jenis_penghuni', 'putri');
        } elseif ($klasterFilter === 'khusus') {
            $query->whereIn('jenis_penghuni', ['campuran', 'tamu']);
        }

        $gedungModels = $query->orderBy('nama_gedung')->get();
        $gedungIds = $gedungModels->pluck('id_gedung')->all();

        $activePlacementsQuery = PenempatanKamar::with([
            'kamar:id_kamar,id_gedung',
            'pendaftaranHunian.client.jenisClient',
        ])
            ->where('status_penempatan', 'aktif')
            ->whereHas('kamar', fn ($q) => $q->whereIn('id_gedung', $gedungIds));

        if ($selectedPeriode) {
            $activePlacementsQuery->whereHas('pendaftaranHunian', fn ($q) => $q->where('id_periode', $selectedPeriode->id_periode));
        }

        $activePlacements = $activePlacementsQuery->get();
        $terisiPerGedung = $activePlacements->groupBy(fn (PenempatanKamar $p) => $p->kamar?->id_gedung)
            ->map->count();

        $gedungList = $gedungModels->map(function (Gedung $gedung) use ($terisiPerGedung) {
            $totalBed = $gedung->kamars->sum('kapasitas_bed');
            $terisi = (int) ($terisiPerGedung[$gedung->id_gedung] ?? 0);
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

        $komposisiCounts = [
            'reguler' => 0,
            'kipk' => 0,
            'internasional' => 0,
            'umum' => 0,
        ];

        foreach ($activePlacements as $placement) {
            $jenis = $placement->pendaftaranHunian?->client?->jenisClient?->jenis;
            $isKipk = $placement->pendaftaranHunian?->client?->status_KIPK;

            if ($isKipk || $jenis === JenisClient::CATEGORIES['kipk']) {
                $komposisiCounts['kipk']++;
            } elseif (in_array($jenis, [
                JenisClient::CATEGORIES['international_private'],
                JenisClient::CATEGORIES['international_funded'],
                JenisClient::CATEGORIES['summer_course'],
            ], true)) {
                $komposisiCounts['internasional']++;
            } elseif ($jenis === JenisClient::CATEGORIES['guest']) {
                $komposisiCounts['umum']++;
            } else {
                $komposisiCounts['reguler']++;
            }
        }

        $formatPersen = fn (int $count): string => $totalTerisi > 0
            ? number_format(($count / $totalTerisi) * 100, 1) . '%'
            : '0.0%';

        $komposisi = [
            ['label' => 'Reguler & S1 Non-Maba', 'jumlah' => $komposisiCounts['reguler'], 'persen' => $formatPersen($komposisiCounts['reguler']), 'dot' => 'bg-secondary'],
            ['label' => 'Mahasiswa KIP-K', 'jumlah' => $komposisiCounts['kipk'], 'persen' => $formatPersen($komposisiCounts['kipk']), 'dot' => 'bg-[#2E9E5B]'],
            ['label' => 'Internasional & Summer', 'jumlah' => $komposisiCounts['internasional'], 'persen' => $formatPersen($komposisiCounts['internasional']), 'dot' => 'bg-secondary-fixed-dim'],
            ['label' => 'Non Mahasiswa (Umum)', 'jumlah' => $komposisiCounts['umum'], 'persen' => $formatPersen($komposisiCounts['umum']), 'dot' => 'bg-on-primary-fixed-variant'],
        ];

        return compact(
            'gedungList',
            'kpi',
            'komposisi',
            'klasterFilter',
            'periodes',
            'periodeFilter',
            'selectedPeriode'
        );
    }
}
