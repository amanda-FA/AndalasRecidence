<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $gedungs = Gedung::orderBy('nama_gedung')->get();

        $query = Kamar::with(['gedung', 'tipeKamar']);

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
            $query->where('status_kamar', $status);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhereHas('tipeKamar', fn ($tk) => $tk->where('nama_tipe', 'like', "%{$search}%"))
                  ->orWhereHas('gedung', fn ($g) => $g->where('nama_gedung', 'like', "%{$search}%"));
            });
        }

        $kamars = $query->orderBy('id_gedung')->orderBy('lantai')->orderBy('nomor_kamar')->get();

        $allKamars = Kamar::with('tipeKamar')->get();
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
            'kamar_tersedia' => $allKamars->where('status_kamar', 'tersedia')->count(),
            'bed_kosong' => max(0, $totalBed - $bedTerisi),
            'kamar_perbaikan' => $allKamars->where('status_kamar', 'perbaikan')->count(),
        ];

        return view('kamar.index', compact('kamars', 'gedungs', 'stats'));
    }

    public function create(Request $request)
    {
        $gedungs = Gedung::orderBy('nama_gedung')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();
        $selectedGedungId = $request->query('gedung_id');

        return view('kamar.create', compact('gedungs', 'tipeKamars', 'selectedGedungId'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $defaultTipe = TipeKamar::firstOrCreate(
            ['nama_tipe' => $data['tipe_kamar']],
            ['kapasitas' => $data['kapasitas_bed'] ?? 4, 'tarif' => $data['tarif'] ?? 0]
        );

        Kamar::create([
            'id_gedung' => $data['gedung_id'],
            'id_tipeKamar' => $defaultTipe->id_tipeKamar,
            'nomor_kamar' => $data['nomor_kamar'],
            'lantai' => $data['lantai'],
            'status_kamar' => $data['status'] ?? 'tersedia',
        ]);

        return redirect()
            ->route('kamar.index', ['gedung_id' => $data['gedung_id']])
            ->with('success', 'Data kamar baru berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        $gedungs = Gedung::orderBy('nama_gedung')->get();
        $tipeKamars = TipeKamar::orderBy('nama_tipe')->get();

        return view('kamar.edit', compact('kamar', 'gedungs', 'tipeKamars'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $data = $this->validatedData($request);

        $defaultTipe = TipeKamar::firstOrCreate(
            ['nama_tipe' => $data['tipe_kamar']],
            ['kapasitas' => $data['kapasitas_bed'] ?? 4, 'tarif' => $data['tarif'] ?? 0]
        );

        $kamar->update([
            'id_gedung' => $data['gedung_id'],
            'id_tipeKamar' => $defaultTipe->id_tipeKamar,
            'nomor_kamar' => $data['nomor_kamar'],
            'lantai' => $data['lantai'],
            'status_kamar' => $data['status'] ?? 'tersedia',
        ]);

        return redirect()
            ->route('kamar.index', ['gedung_id' => $data['gedung_id']])
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        $gedungId = $kamar->id_gedung;
        $kamar->delete();

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
        ]);

        return $validated;
    }
}
