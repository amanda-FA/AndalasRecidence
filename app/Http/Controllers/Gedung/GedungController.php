<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GedungController extends Controller
{
    public function index(Request $request)
    {
        $query = Gedung::with('kamars');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('klaster_wilayah', 'like', "%{$search}%");
            });
        }

        if ($peruntukan = $request->query('peruntukan')) {
            $query->where('peruntukan', $peruntukan);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $gedungs = $query->orderBy('nama')->get();

        $allGedungs = Gedung::with('kamars')->get();
        $totalBed = $allGedungs->sum(fn ($g) => $g->kamars->sum('kapasitas_bed') ?: $g->kapasitas_bed);
        $totalTerisi = $allGedungs->sum(fn ($g) => $g->kamars->sum('bed_terisi'));
        $totalKamar = $allGedungs->sum(fn ($g) => $g->kamars->count() ?: $g->estimasi_kamar);
        $kamarTersedia = Kamar::where('status', 'tersedia')->count();

        $metrics = [
            'total_gedung' => $allGedungs->count(),
            'gedung_aktif' => $allGedungs->where('status', 'aktif')->count(),
            'total_lantai' => $allGedungs->sum('jumlah_lantai'),
            'total_kamar' => $totalKamar,
            'kamar_tersedia' => $kamarTersedia,
            'total_bed' => $totalBed,
            'bed_terisi' => $totalTerisi,
            'okupansi_persen' => $totalBed > 0 ? round(($totalTerisi / $totalBed) * 100, 1) : 0,
        ];

        return view('gedung.index', compact('gedungs', 'metrics'));
    }

    public function create()
    {
        return view('gedung.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('gedung', 'public');
        }

        Gedung::create($data);

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung baru berhasil ditambahkan.');
    }

    public function edit(Gedung $gedung)
    {
        return view('gedung.edit', compact('gedung'));
    }

    public function update(Request $request, Gedung $gedung)
    {
        $data = $this->validatedData($request, $gedung->id);

        if ($request->hasFile('foto')) {
            if ($gedung->foto) {
                Storage::disk('public')->delete($gedung->foto);
            }
            $data['foto'] = $request->file('foto')->store('gedung', 'public');
        }

        $gedung->update($data);

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung berhasil diperbarui.');
    }

    public function destroy(Gedung $gedung)
    {
        if ($gedung->foto) {
            Storage::disk('public')->delete($gedung->foto);
        }

        $gedung->delete();

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung berhasil dihapus.');
    }

    private function validatedData(Request $request, ?int $gedungId = null): array
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['required', 'string', 'max:30', 'unique:gedungs,kode,' . ($gedungId ?? 'NULL')],
            'peruntukan' => ['required', 'in:putra,putri,campuran,tamu'],
            'status' => ['required', 'in:aktif,persiapan,draf'],
            'klaster_wilayah' => ['required', 'string', 'max:255'],
            'jumlah_lantai' => ['required', 'integer', 'min:1', 'max:20'],
            'estimasi_kamar' => ['required', 'integer', 'min:1'],
            'kapasitas_bed' => ['required', 'integer', 'min:1'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['string', 'max:100'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);
        $validated['fasilitas'] = $validated['fasilitas'] ?? [];

        return $validated;
    }
}
