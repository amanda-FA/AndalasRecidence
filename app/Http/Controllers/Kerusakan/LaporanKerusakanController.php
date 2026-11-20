<?php

namespace App\Http\Controllers\Kerusakan;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\LaporanKerusakan;
use App\Models\PenempatanKamar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LaporanKerusakanController extends Controller
{
    public function index(Request $request): View
    {
        $placement = PenempatanKamar::forUser($request->user()->getAuthIdentifier())->current()->with('kamar.gedung')->first();
        $models = LaporanKerusakan::where('id_pengguna', $request->user()->getAuthIdentifier())
            ->with(['pengguna', 'fasilitas.kamar.gedung', 'fasilitas.gedung', 'penugasanPerbaikans' => fn ($query) => $query->orderByDesc('id_perbaikan'), 'penugasanPerbaikans.pengguna'])
            ->orderByDesc('tanggal_lapor')->orderByDesc('id_laporan')->get();
        $availableIds = $placement ? $this->availableFacilities($placement)->pluck('id_fasilitas') : collect();
        $facilities = Fasilitas::whereIn('id_fasilitas', $availableIds->merge($models->pluck('id_fasilitas'))->unique())
            ->with(['kamar.gedung', 'gedung'])->get()->mapWithKeys(function ($facility) use ($availableIds) {
                return [$facility->id_fasilitas => [
                    'nama_fasilitas' => $facility->nama_fasilitas,
                    'id_kamar' => $facility->id_kamar,
                    'id_gedung' => $facility->id_gedung,
                    'jenis_fasilitas' => $facility->id_kamar === null ? 'bersama' : 'kamar',
                    'lokasi' => $facility->kamar ? 'Kamar '.$facility->kamar->nomor_kamar.' · Lantai '.$facility->kamar->lantai : 'Fasilitas bersama',
                    'gedung' => $facility->gedung?->nama_gedung ?? $facility->kamar?->gedung?->nama_gedung ?? 'Gedung belum tercatat',
                    'available' => $availableIds->contains($facility->id_fasilitas),
                ]];
            })->all();
        $reports = $models->map(function ($report): array {
            $technician = $report->penugasanPerbaikans->first()?->pengguna?->nama_lengkap;
            $photo = $report->foto && $this->photoDisk($report->foto) ? route('kerusakan.photo', $report) : null;

            return [
                'id_laporan' => $report->id_laporan,
                'id_pengguna' => $report->id_pengguna,
                'id_fasilitas' => $report->id_fasilitas,
                'deskripsi' => $report->deskripsi,
                'foto' => $photo,
                'tanggal_lapor' => $report->tanggal_lapor->toDateString(),
                'status_perbaikan' => $report->status_perbaikan,
                'tanggal_selesai' => $report->tanggal_selesai?->toDateString(),
                'teknisi' => $technician,
                // Skema saat ini tidak menyimpan tanggal disposisi/pengerjaan atau foto per tahap.
                'tanggal_disposisi' => null,
                'tanggal_pengerjaan' => null,
                'photos' => $photo ? [[
                    'src' => $photo, 'label' => 'Foto laporan', 'title' => 'Kondisi yang dilaporkan',
                    'description' => 'Foto yang dilampirkan pada laporan kerusakan.',
                    'date' => $report->tanggal_lapor->toDateString(), 'source' => $report->pengguna?->nama_lengkap ?? 'Pelapor',
                ]] : [],
            ];
        })->all();
        $statuses = [
            'diajukan' => ['label' => 'Laporan Diajukan', 'stage' => 1, 'warna' => 'bg-surface-container-low text-secondary'],
            'ditugaskan' => ['label' => 'Disposisi & Teknisi', 'stage' => 2, 'warna' => 'bg-primary-fixed text-on-primary-fixed-variant'],
            'dalam_pengerjaan' => ['label' => 'Dalam Pengerjaan', 'stage' => 3, 'warna' => 'bg-[#fff3df] text-[#99620a]'],
            'selesai' => ['label' => 'Selesai', 'stage' => 4, 'warna' => 'bg-success-container text-success'],
        ];
        foreach ($models->pluck('status_perbaikan')->unique() as $value) {
            $statuses[$value] ??= ['label' => Str::headline($value), 'stage' => 0, 'warna' => 'bg-surface-container-low text-on-surface-variant'];
        }
        $room = $placement?->kamar;
        $currentLocation = $room ? 'Kamar '.$room->nomor_kamar.' · '.$room->gedung?->nama_gedung : null;

        return view('kerusakan.index', compact('reports', 'facilities', 'statuses', 'currentLocation') + [
            'canReport' => $placement && $availableIds->isNotEmpty(),
            'reporter' => $request->user()->nama_lengkap,
        ]);
    }

    private function availableFacilities(PenempatanKamar $placement): Builder
    {
        return Fasilitas::where('aktif', true)->where(fn (Builder $query) => $query->where('id_kamar', $placement->id_kamar)
            ->orWhere(fn (Builder $shared) => $shared->whereNull('id_kamar')->where('id_gedung', $placement->kamar->id_gedung)));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jenis_fasilitas' => ['required', Rule::in(['kamar', 'bersama'])],
            'id_fasilitas' => ['required', 'integer', 'exists:fasilitas,id_fasilitas'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:2000', function ($attribute, $value, $fail) {
                if (mb_strlen(trim($value)) < 10) $fail('Jelaskan kerusakan minimal 10 karakter.');
            }],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'jenis_fasilitas.required' => 'Pilih jenis fasilitas terlebih dahulu.',
            'jenis_fasilitas.in' => 'Pilih fasilitas kamar atau fasilitas bersama.',
            'id_fasilitas.required' => 'Pilih fasilitas yang bermasalah.',
            'id_fasilitas.exists' => 'Fasilitas yang dipilih tidak ditemukan.',
        ]);
        $photoPath = null;
        try {
            $report = DB::transaction(function () use ($request, $data, &$photoPath): LaporanKerusakan {
                $placement = PenempatanKamar::forUser($request->user()->getAuthIdentifier())->current()->lockForUpdate()->first();
                if (! $placement) {
                    throw ValidationException::withMessages(['id_fasilitas' => 'Laporan baru memerlukan penempatan kamar aktif.']);
                }
                $facility = $this->availableFacilities($placement)->whereKey($data['id_fasilitas'])->first();
                if (! $facility) {
                    throw ValidationException::withMessages(['id_fasilitas' => 'Pilih fasilitas kamar atau fasilitas bersama di gedung tempat kamu tinggal.']);
                }
                $type = $facility->id_kamar === null ? 'bersama' : 'kamar';
                if ($type !== $data['jenis_fasilitas']) {
                    throw ValidationException::withMessages(['id_fasilitas' => 'Fasilitas yang dipilih tidak sesuai dengan jenis fasilitas.']);
                }
                if ($request->hasFile('foto')) {
                    $photoPath = $request->file('foto')->store('laporan-kerusakan', 'local');
                    if ($photoPath === false) {
                        throw ValidationException::withMessages(['foto' => 'Foto belum berhasil disimpan. Silakan kirim ulang laporan.']);
                    }
                }
                return LaporanKerusakan::create([
                    'id_pengguna' => $request->user()->getAuthIdentifier(),
                    'id_fasilitas' => $data['id_fasilitas'],
                    'deskripsi' => trim($data['deskripsi']),
                    'foto' => $photoPath,
                    'tanggal_lapor' => today('Asia/Jakarta')->toDateString(),
                    'status_perbaikan' => 'diajukan',
                    'tanggal_selesai' => null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($photoPath) Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return redirect()->route('kerusakan.index')
            ->with('success', 'Laporan kerusakan berhasil diajukan')
            ->with('created_report_id', $report->id_laporan);
    }

    private function photoDisk(string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) return $disk;
        }

        return null;
    }

    public function photo(Request $request, LaporanKerusakan $laporan): StreamedResponse
    {
        abort_unless((int) $laporan->id_pengguna === (int) $request->user()->getAuthIdentifier(), 404);
        $disk = $laporan->foto ? $this->photoDisk($laporan->foto) : null;
        abort_unless($disk, 404);

        return Storage::disk($disk)->response($laporan->foto, null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
