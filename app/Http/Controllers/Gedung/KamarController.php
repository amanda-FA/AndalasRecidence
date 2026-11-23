<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use App\Models\TipeKamar;
use App\Services\FacilitySelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $gedungs = Gedung::withCount('kamars')->orderBy('nama_gedung')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();

        $query = Kamar::with(['gedung', 'tipeKamar', 'fasilitas']);

        if ($gedungId = $request->query('gedung_id')) {
            $query->where('id_gedung', $gedungId);
        }

        if ($lantai = $request->query('lantai')) {
            $query->where('lantai', $lantai);
        }

        if ($tipe = $request->query('tipe_kamar')) {
            $query->whereHas('tipeKamar', fn ($tk) => $tk->where('nama_tipe', 'like', "%{$tipe}%"));
        }

        if ($status = $request->query('status')) {
            if ($status === 'perbaikan') {
                $query->where('status_kamar', 'perbaikan');
            } elseif ($status === 'penuh') {
                $query->where('status_kamar', '!=', 'perbaikan')
                    ->whereRaw('(select count(*) from penempatan_kamar where penempatan_kamar.id_kamar = kamar.id_kamar and penempatan_kamar.status_penempatan = ?) >= coalesce((select kapasitas from tipe_kamar where tipe_kamar.id_tipeKamar = kamar.id_tipeKamar), 0)', ['aktif']);
            } elseif ($status === 'tersedia') {
                $query->where('status_kamar', '!=', 'perbaikan')
                    ->whereRaw('(select count(*) from penempatan_kamar where penempatan_kamar.id_kamar = kamar.id_kamar and penempatan_kamar.status_penempatan = ?) < coalesce((select kapasitas from tipe_kamar where tipe_kamar.id_tipeKamar = kamar.id_tipeKamar), 0)', ['aktif']);
            }
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhereHas('tipeKamar', fn ($tk) => $tk->where('nama_tipe', 'like', "%{$search}%"))
                  ->orWhereHas('gedung', fn ($g) => $g->where('nama_gedung', 'like', "%{$search}%"));
            });
        }

        $gedungId = $request->query('gedung_id');
        $selectedGedung = $gedungId ? $gedungs->firstWhere('id_gedung', (int) $gedungId) : null;

        $kamars = $query->orderBy('id_gedung')->orderBy('lantai')->orderBy('nomor_kamar')->paginate(15)->withQueryString();

        $allKamarsQuery = Kamar::with('tipeKamar');
        if ($selectedGedung) {
            $allKamarsQuery->where('id_gedung', $selectedGedung->id_gedung);
        }
        $allKamars = $allKamarsQuery->get();
        $totalBed = $allKamars->sum('kapasitas_bed');
        $bedTerisi = $allKamars->sum('bed_terisi');
        $totalKamar = $allKamars->count();
        $kamarTerisi = $allKamars->where('bed_terisi', '>', 0)->count();

        $stats = [
            'total_kamar' => $totalKamar,
            'total_bed' => $totalBed,
            'kamar_terisi' => $kamarTerisi,
            'kamar_terisi_persen' => $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0,
            'bed_terisi' => $bedTerisi,
            'kamar_tersedia' => $allKamars->filter(fn (Kamar $k) => $k->status === 'tersedia')->count(),
            'bed_kosong' => max(0, $totalBed - $bedTerisi),
            'kamar_perbaikan' => $allKamars->where('status_kamar', 'perbaikan')->count(),
        ];

        return view('kamar.index', compact('kamars', 'gedungs', 'tipeKamars', 'stats', 'selectedGedung'));
    }

    public function create(Request $request)
    {
        $gedungs = Gedung::withCount('kamars')->orderBy('nama_gedung')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();
        $selectedGedungId = $request->query('gedung_id');

        return view('kamar.create', compact('gedungs', 'tipeKamars', 'selectedGedungId') + app(FacilitySelectionService::class)->formData('kamar'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $this->saveKamar($request, $data);

        return redirect()
            ->route('kamar.index', ['gedung_id' => $data['gedung_id']])
            ->with('success', 'Data kamar baru berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        $gedungs = Gedung::withCount('kamars')->orderBy('nama_gedung')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();

        return view('kamar.edit', compact('kamar', 'gedungs', 'tipeKamars') + app(FacilitySelectionService::class)->formData('kamar', $kamar));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $data = $this->validatedData($request);

        $this->saveKamar($request, $data, $kamar);

        return redirect()
            ->route('kamar.index', ['gedung_id' => $data['gedung_id']])
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        $gedungId = $kamar->id_gedung;
        try {
            $deleted = DB::transaction(function () use ($kamar) {
                $owner = Kamar::whereKey($kamar->id_kamar)->lockForUpdate()->firstOrFail();
                if ($owner->penempatanKamars()->exists() || ! app(FacilitySelectionService::class)->deleteUnusedInventory($owner)) return false;
                return $owner->delete();
            });
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() !== '23000') throw $exception;
            $deleted = false;
        }
        if (! $deleted) return redirect()->route('kamar.index', ['gedung_id' => $gedungId])->with('error', 'Kamar atau fasilitasnya masih digunakan pada penempatan, laporan kerusakan, atau pengecekan.');

        return redirect()
            ->route('kamar.index', ['gedung_id' => $gedungId])
            ->with('success', 'Data kamar berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'gedung_id' => ['required', 'exists:gedung,id_gedung'],
            'nomor_kamar' => ['required', 'string', 'max:20'],
            'lantai' => ['required', 'integer', 'min:1', 'max:20'],
            'tipe_kamar' => ['required', 'string', 'max:100'],
            'kapasitas_bed' => ['nullable', 'integer', 'min:1', 'max:20'],
            'tarif' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:30'],
        ] + FacilitySelectionService::rules('kamar'));

        return $validated;
    }

    private function saveKamar(Request $request, array $data, ?Kamar $kamar = null): void
    {
        DB::transaction(function () use ($request, $data, $kamar) {
            $type = TipeKamar::firstOrCreate(
                ['nama_tipe' => $data['tipe_kamar']],
                ['kapasitas' => $data['kapasitas_bed'] ?? 4, 'tarif' => $data['tarif'] ?? 0]
            );
            $owner = $kamar ? Kamar::whereKey($kamar->id_kamar)->lockForUpdate()->firstOrFail() : new Kamar();
            $owner->fill([
                'id_gedung' => $data['gedung_id'],
                'id_tipeKamar' => $type->id_tipeKamar,
                'nomor_kamar' => $data['nomor_kamar'],
                'lantai' => $data['lantai'],
                'status_kamar' => $data['status'],
            ])->save();
            if ($request->has('fasilitas_present') || $request->has('fasilitas')) {
                app(FacilitySelectionService::class)->sync($owner, $data['fasilitas'] ?? []);
            }
        });
    }
}
