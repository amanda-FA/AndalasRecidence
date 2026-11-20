<?php

namespace App\Http\Controllers\Gedung;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Kamar;
use App\Services\FacilitySelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        return view('gedung.create', app(FacilitySelectionService::class)->formData('gedung'));
    }

    public function store(Request $request)
    {
        $this->saveGedung($request);

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung baru berhasil ditambahkan.');
    }

    public function edit(Gedung $gedung)
    {
        return view('gedung.edit', compact('gedung') + app(FacilitySelectionService::class)->formData('gedung', $gedung));
    }

    public function update(Request $request, Gedung $gedung)
    {
        $this->saveGedung($request, $gedung);

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung berhasil diperbarui.');
    }

    public function destroy(Gedung $gedung)
    {
        $photo = $gedung->foto;
        try {
            $deleted = DB::transaction(function () use ($gedung) {
                $owner = Gedung::whereKey($gedung->id_gedung)->lockForUpdate()->firstOrFail();
                if ($owner->kamars()->exists() || ! app(FacilitySelectionService::class)->deleteUnusedInventory($owner)) return false;
                return $owner->delete();
            });
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() !== '23000') throw $exception;
            $deleted = false;
        }
        if (! $deleted) return redirect()->route('gedung.index')->with('error', 'Gedung masih memiliki kamar atau fasilitas yang digunakan pada laporan dan pengecekan.');
        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        return redirect()
            ->route('gedung.index')
            ->with('success', 'Data gedung berhasil dihapus.');
    }

    private function saveGedung(Request $request, ?Gedung $gedung = null): void
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'peruntukan' => ['required', 'in:putra,putri,campuran,tamu'],
            'jumlah_lantai' => ['required', 'integer', 'min:1', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ] + FacilitySelectionService::rules('gedung'), [
            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'Gunakan gambar berformat JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $attributes = [
            'nama_gedung' => $data['nama'],
            'jenis_penghuni' => $data['peruntukan'],
            'jumlah_lantai' => $data['jumlah_lantai'],
            'keterangan' => $data['deskripsi'] ?? null,
        ];
        $previousPhoto = $gedung?->foto;
        $newPhoto = null;

        if ($request->hasFile('foto')) {
            $newPhoto = $request->file('foto')->store('gedung', 'public');
            if (! $newPhoto) {
                throw ValidationException::withMessages(['foto' => 'Gambar gagal disimpan. Silakan coba kembali.']);
            }
            $attributes['foto'] = $newPhoto;
        }

        try {
            DB::transaction(function () use ($gedung, $attributes, $request, $data) {
                if ($gedung) {
                    $owner = Gedung::whereKey($gedung->id_gedung)->lockForUpdate()->firstOrFail();
                    $owner->update($attributes);
                } else {
                    $owner = Gedung::create($attributes);
                }
                if ($request->has('fasilitas_present') || $request->has('fasilitas')) {
                    app(FacilitySelectionService::class)->sync($owner, $data['fasilitas'] ?? []);
                }
            });
        } catch (Throwable $exception) {
            if ($newPhoto) {
                Storage::disk('public')->delete($newPhoto);
            }
            throw $exception;
        }

        if ($newPhoto && $previousPhoto) {
            Storage::disk('public')->delete($previousPhoto);
        }
    }
}
