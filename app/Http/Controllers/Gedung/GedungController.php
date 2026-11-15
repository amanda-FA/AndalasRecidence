<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    public function index(Request $request)
    {
        $query = Gedung::with(['kamars.tipeKamar']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_gedung', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($peruntukan = $request->query('peruntukan')) {
            $query->where('jenis_penghuni', $peruntukan);
        }

        $gedungs = $query->orderBy('nama_gedung')->get();

        $allGedungs = Gedung::with(['kamars.tipeKamar'])->get();
        $totalBed = $allGedungs->sum(fn ($g) => $g->kamars->sum('kapasitas_bed'));
        $totalTerisi = $allGedungs->sum(fn ($g) => $g->kamars->sum('bed_terisi'));
        $totalKamar = $allGedungs->sum(fn ($g) => $g->kamars->count());
        $kamarTersedia = Kamar::where('status_kamar', 'tersedia')->count();

        $metrics = [
            'total_gedung' => $allGedungs->count(),
            'gedung_aktif' => $allGedungs->count(),
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

        Gedung::create([
            'nama_gedung' => $data['nama'],
            'jenis_penghuni' => $data['peruntukan'],
            'jumlah_lantai' => $data['jumlah_lantai'],
            'keterangan' => $data['deskripsi'] ?? null,
        ]);

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
        $data = $this->validatedData($request, $gedung->id_gedung);

        $gedung->update([
            'nama_gedung' => $data['nama'],
            'jenis_penghuni' => $data['peruntukan'],
            'jumlah_lantai' => $data['jumlah_lantai'],
            'keterangan' => $data['deskripsi'] ?? null,
        ]);

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung berhasil diperbarui.');
    }

    public function destroy(Gedung $gedung)
    {
        $gedung->delete();

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung berhasil dihapus.');
    }

    private function validatedData(Request $request, ?int $gedungId = null): array
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'peruntukan' => ['required', 'in:putra,putri,campuran,tamu'],
            'jumlah_lantai' => ['required', 'integer', 'min:1', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        return $validated;
    }
}
