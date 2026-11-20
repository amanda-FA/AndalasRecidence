<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\JenisClient;
use App\Models\PeriodePendaftaran;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    private function buildings(?Gedung $building = null): Collection
    {
        $today = today('Asia/Jakarta')->toDateString();
        $query = Gedung::with([
            'fasilitas', 'kamars.tipeKamar',
            'kamars' => fn ($rooms) => $rooms->withCount(['penempatanKamars as penghuni_aktif' => fn ($placements) => $placements
                ->where('status_penempatan', 'aktif')->whereNull('tanggal_keluar')->whereDate('tanggal_masuk', '<=', $today)
                ->where(fn ($q) => $q->whereNull('rencana_keluar')->orWhereDate('rencana_keluar', '>=', $today))])
                ->orderBy('lantai')->orderBy('nomor_kamar'),
        ]);
        if ($building) $query->whereKey($building->id_gedung);

        return $query->orderBy('nama_gedung')->get()->map(function ($building): array {
            $rooms = $building->kamars->map(function ($room): array {
                $type = $room->getRelation('tipeKamar');
                $remaining = max(0, ($type?->kapasitas ?? 0) - $room->penghuni_aktif);
                $available = $room->status_kamar === 'tersedia' && $remaining > 0;

                return [
                    'nomor' => $room->nomor_kamar, 'lantai' => $room->lantai,
                    'tipe' => $type?->nama_tipe ?? 'Belum ditentukan', 'tipe_id' => $type?->id_tipeKamar,
                    'kapasitas' => $type?->kapasitas, 'tarif' => $type?->tarif,
                    'tersedia' => $available, 'sisa_bed' => $available ? $remaining : 0,
                    'status' => $available ? 'Tersedia' : match ($room->status_kamar) {
                        'perbaikan' => 'Dalam perbaikan', 'penuh' => 'Penuh',
                        'tersedia' => $remaining === 0 ? 'Penuh' : 'Belum tersedia', default => 'Belum tersedia',
                    },
                ];
            });

            return [
                'id' => $building->id_gedung, 'nama' => $building->nama_gedung,
                'foto_url' => $building->foto_url,
                'peruntukan' => $building->jenis_penghuni, 'label' => $building->peruntukan_label,
                'keterangan' => $building->keterangan, 'lantai' => $building->jumlah_lantai,
                'fasilitas' => $building->fasilitas->pluck('nama_fasilitas')->unique()->values(),
                'kamar' => $rooms, 'jumlah_kamar' => $rooms->count(),
                'kamar_tersedia' => $rooms->where('tersedia', true)->count(),
                'sisa_bed' => $rooms->sum('sisa_bed'),
                'tipe' => $rooms->pluck('tipe')->unique()->values(),
                'tarif_mulai' => $rooms->pluck('tarif')->filter(fn ($rate) => $rate !== null)->min(),
            ];
        });
    }

    public function index(): View
    {
        $buildings = $this->buildings();
        $today = today('Asia/Jakarta')->toDateString();
        $periods = PeriodePendaftaran::orderByDesc('tanggal_buka')->get()->map(function ($period) use ($today): array {
            $start = $period->tanggal_buka->toDateString();
            $end = $period->tanggal_tutup->toDateString();
            $status = $period->status !== 'dibuka' || $end < $today ? 'Ditutup' : ($start > $today ? 'Akan Datang' : 'Dibuka');

            return ['id' => $period->id_periode, 'nama' => $period->nama_periode,
                'tahun' => $period->tahun_akademik, 'buka' => $period->tanggal_buka,
                'tutup' => $period->tanggal_tutup, 'status' => $status];
        })->sortBy(fn ($period) => array_search($period['status'], ['Dibuka', 'Akan Datang', 'Ditutup'], true))->values();

        return view('landing.index', [
            'buildings' => $buildings->take(6), 'buildingCount' => $buildings->count(),
            'roomCount' => $buildings->sum('jumlah_kamar'),
            'typeCount' => $buildings->flatMap(fn ($building) => $building['kamar'])->pluck('tipe_id')->filter()->unique()->count(),
            'periods' => $periods->take(3), 'hasOpenPeriod' => $periods->contains('status', 'Dibuka'),
            'categories' => JenisClient::orderBy('jenis')->pluck('jenis'),
        ]);
    }

    public function catalog(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'peruntukan' => ['nullable', 'in:putra,putri,campuran,tamu']]);
        $buildings = $this->buildings()->filter(fn ($building) =>
            (! ($filters['peruntukan'] ?? null) || $building['peruntukan'] === $filters['peruntukan'])
            && (! ($filters['q'] ?? null) || mb_stripos($building['nama'], trim($filters['q'])) !== false))->values();

        return view('landing.catalog', compact('buildings'));
    }

    public function show(Request $request, Gedung $gedung): View
    {
        $building = $this->buildings($gedung)->first();
        $page = max(1, (int) $request->query('page', 1));
        $rooms = new LengthAwarePaginator($building['kamar']->forPage($page, 12)->values(), $building['jumlah_kamar'], 12, $page, ['path' => $request->url()]);

        return view('landing.hunian-detail', compact('building', 'rooms'));
    }
}
