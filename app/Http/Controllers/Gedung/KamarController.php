<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $gedungs = Gedung::orderBy('nama')->get();

        $query = Kamar::with('gedung');

        if ($gedungId = $request->query('gedung_id')) {
            $query->where('gedung_id', $gedungId);
        }

        if ($lantai = $request->query('lantai')) {
            $query->where('lantai', $lantai);
        }

        if ($tipe = $request->query('tipe_kamar')) {
            $query->where('tipe_kamar', $tipe);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhere('tipe_kamar', 'like', "%{$search}%")
                  ->orWhereHas('gedung', fn ($g) => $g->where('nama', 'like', "%{$search}%"));
            });
        }

        $kamars = $query->orderBy('gedung_id')->orderBy('lantai')->orderBy('nomor_kamar')->get();

        $allKamars = Kamar::all();
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
            'kamar_tersedia' => $allKamars->where('status', 'tersedia')->count(),
            'bed_kosong' => max(0, $totalBed - $bedTerisi),
            'kamar_perbaikan' => $allKamars->where('status', 'perbaikan')->count(),
        ];

        return view('kamar.index', compact('kamars', 'gedungs', 'stats'));
    }

    public function create(Request $request)
    {
        $gedungs = Gedung::orderBy('nama')->get();
        $selectedGedungId = $request->query('gedung_id');

        return view('kamar.create', compact('gedungs', 'selectedGedungId'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        Kamar::create($data);

        return redirect()
            ->route('kamar.index', ['gedung_id' => $data['gedung_id']])
            ->with('success', 'Data kamar baru berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        $gedungs = Gedung::orderBy('nama')->get();

        return view('kamar.edit', compact('kamar', 'gedungs'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('foto')) {
            if ($kamar->foto) {
                Storage::disk('public')->delete($kamar->foto);
            }
            $data['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        $kamar->update($data);

        return redirect()
            ->route('kamar.index', ['gedung_id' => $data['gedung_id']])
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        $gedungId = $kamar->gedung_id;
        if ($kamar->foto) {
            Storage::disk('public')->delete($kamar->foto);
        }
        $kamar->delete();

        return redirect()
            ->route('kamar.index', ['gedung_id' => $gedungId])
            ->with('success', 'Data kamar berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'gedung_id' => ['required', 'exists:gedungs,id'],
            'nomor_kamar' => ['required', 'string', 'max:30'],
            'lantai' => ['required', 'integer', 'min:1', 'max:20'],
            'sayap' => ['nullable', 'string', 'max:60'],
            'tipe_kamar' => ['required', 'string', 'max:50'],
            'kapasitas_bed' => ['required', 'integer', 'min:1', 'max:20'],
            'bed_terisi' => ['required', 'integer', 'min:0', 'lte:kapasitas_bed'],
            'tarif' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:tersedia,penuh,perbaikan'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['string', 'max:100'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'catatan' => ['nullable', 'string'],
        ]);

        $validated['rfid_enabled'] = $request->boolean('rfid_enabled');
        $validated['tarif'] = $validated['tarif'] ?? 0;
        $validated['fasilitas'] = $validated['fasilitas'] ?? [];

        return $validated;
    }
}
