<?php

namespace App\Http\Controllers\Pendaftaran;

use App\Http\Controllers\Controller;
use App\Models\{BerkasPendaftaran, Gedung, JenisBerkas, JenisClient, PendaftaranHunian, PeriodePendaftaran};
use App\Services\RegistrationVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\{Rule, ValidationException};

class VerifikasiPendaftaranController extends Controller
{
    public const STATUSES = ['menunggu_verifikasi' => 'Menunggu Verifikasi', 'perlu_perbaikan' => 'Perlu Perbaikan', 'terverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak', 'batal' => 'Batal'];

    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'jenis' => ['nullable', 'integer', 'exists:jenis_client,id_jenis_client'], 'periode' => ['nullable', 'integer', 'exists:periode_pendaftaran,id_periode'], 'status' => ['nullable', Rule::in([...array_keys(self::STATUSES), 'ditolak_batal'])]]);
        $query = PendaftaranHunian::with(['client.pengguna', 'client.jenisClient', 'periode', 'tipeKamar', 'kamar.gedung', 'kamar.tipeKamar', 'penempatanKamars.kamar.gedung', 'penempatanKamars.kamar.tipeKamar']);
        if ($filters['q'] ?? null) $query->where(fn ($q) => $q->whereHas('client.pengguna', fn ($u) => $u->where('nama_lengkap', 'like', '%'.$filters['q'].'%'))->orWhere('id_pendaftaran', $filters['q']));
        if ($filters['jenis'] ?? null) $query->whereHas('client', fn ($q) => $q->where('id_jenis_client', $filters['jenis']));
        if ($filters['periode'] ?? null) $query->where('id_periode', $filters['periode']);
        $counts = (clone $query)->reorder()->select('status_pendaftaran')->selectRaw('COUNT(*) AS total')->groupBy('status_pendaftaran')->pluck('total', 'status_pendaftaran');
        if ($filters['status'] ?? null) {
            if ($filters['status'] === 'ditolak_batal') $query->whereIn('status_pendaftaran', ['ditolak','batal']);
            elseif ($filters['status'] === 'menunggu_verifikasi') $query->whereIn('status_pendaftaran', ['menunggu_verifikasi','menunggu','diajukan']);
            else $query->where('status_pendaftaran', $filters['status']);
        }
        return view('verifikasi-pendaftaran.index', [
            'waiting' => (clone $query)->whereIn('status_pendaftaran', RegistrationVerificationService::PENDING)
                ->orderBy('tanggal_daftar')->orderBy('id_pendaftaran')->paginate(10, ['*'], 'antrean_page')->withQueryString()->fragment('verification-waiting'),
            'history' => (clone $query)->whereNotIn('status_pendaftaran', RegistrationVerificationService::PENDING)
                ->orderByRaw('COALESCE(tanggal_verifikasi, tanggal_daftar) DESC')->orderByDesc('id_pendaftaran')
                ->paginate(10, ['*'], 'riwayat_page')->withQueryString()->fragment('verification-history'),
            'counts' => $counts, 'statuses' => self::STATUSES + ['ditolak_batal' => 'Ditolak / Batal'], 'kinds' => JenisClient::all(), 'periods' => PeriodePendaftaran::orderByDesc('tanggal_buka')->get(), 
        ]);
    }

    public function show(PendaftaranHunian $pendaftaran)
    {
        $pendaftaran->load(['client.pengguna', 'client.jenisClient', 'periode', 'tipeKamar', 'kamar.gedung', 'kamar.tipeKamar', 'admin', 'berkasPendaftarans.jenisBerkas', 'penempatanKamars.kamar.gedung']);
        return view('verifikasi-pendaftaran.show', ['registration' => $pendaftaran, 'types' => JenisBerkas::all(), 'statuses' => self::STATUSES]);
    }

    public function document(PendaftaranHunian $pendaftaran, BerkasPendaftaran $berkas)
    {
        abort_unless($berkas->id_pendaftaran === $pendaftaran->id_pendaftaran, 404);
        abort_unless(Storage::disk('local')->exists($berkas->path_file), 404);
        return response()->file(Storage::disk('local')->path($berkas->path_file), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function classifyDocument(Request $request, PendaftaranHunian $pendaftaran, BerkasPendaftaran $berkas)
    {
        abort_unless($berkas->id_pendaftaran === $pendaftaran->id_pendaftaran, 404);
        $data = $request->validate(['id_jenis_berkas' => ['required', 'integer', 'exists:jenis_berkas,id_jenis_berkas']]);
        DB::transaction(function () use ($pendaftaran, $berkas, $data) {
            $registration = PendaftaranHunian::whereKey($pendaftaran->id_pendaftaran)->lockForUpdate()->firstOrFail();
            $document = $registration->berkasPendaftarans()->whereKey($berkas->id_berkas)->lockForUpdate()->firstOrFail();
            if ($document->id_jenis_berkas !== null) {
                throw ValidationException::withMessages(['id_jenis_berkas' => 'Jenis berkas ini sudah ditetapkan. Muat ulang halaman.']);
            }
            $document->update($data);
        });
        return redirect()->route('verifikasi-pendaftaran.show', $pendaftaran)->with('success', 'Jenis berkas lama berhasil disimpan.');
    }

    public function verify(PendaftaranHunian $pendaftaran)
    {
        if (! in_array($pendaftaran->status_pendaftaran, RegistrationVerificationService::PENDING, true)) {
            throw ValidationException::withMessages(['verifikasi' => 'Pendaftaran sudah diproses. Muat ulang halaman.']);
        }
        if ($pendaftaran->client->requiresAdminPlacement()) return redirect()->route('verifikasi-pendaftaran.penempatan', $pendaftaran);
        return redirect()->route('verifikasi-pendaftaran.jadwal', $pendaftaran);
    }

    public function schedule(PendaftaranHunian $pendaftaran)
    {
        abort_if($pendaftaran->client->requiresAdminPlacement(), 403);
        if (! in_array($pendaftaran->status_pendaftaran, RegistrationVerificationService::PENDING, true)) return redirect()->route('verifikasi-pendaftaran.show', $pendaftaran)->with('error', 'Pendaftaran sudah diproses.');
        $pendaftaran->load(['client.pengguna', 'client.jenisClient', 'kamar.gedung', 'kamar.tipeKamar']);
        if (! $pendaftaran->kamar) return redirect()->route('verifikasi-pendaftaran.show', $pendaftaran)->with('error', 'Pilihan kamar client belum tercatat.');
        return view('verifikasi-pendaftaran.jadwal', [
            'registration' => $pendaftaran,
            'start' => $pendaftaran->tanggal_masuk?->toDateString() ?? today()->toDateString(),
            'end' => $pendaftaran->rencana_keluar?->toDateString() ?? today()->addMonths(6)->toDateString(),
        ]);
    }

    public function saveSchedule(Request $request, PendaftaranHunian $pendaftaran, RegistrationVerificationService $service)
    {
        abort_if($pendaftaran->client->requiresAdminPlacement(), 403);
        $request->validate(['id_kamar' => ['prohibited'], 'id_gedung' => ['prohibited'], 'nomor_bed' => ['prohibited'], 'pilihan_bed' => ['prohibited']]);
        $data = $this->validatedSchedule($request);
        $service->verify($pendaftaran, $request->user()->id_pengguna, $data);
        return redirect()->route('verifikasi-pendaftaran.index')->with('success', 'Pendaftaran berhasil diverifikasi dan jadwal hunian berhasil disimpan.');
    }

    public function placement(Request $request, PendaftaranHunian $pendaftaran, RegistrationVerificationService $service)
    {
        abort_unless($pendaftaran->client->requiresAdminPlacement(), 403);
        if (! in_array($pendaftaran->status_pendaftaran, RegistrationVerificationService::PENDING, true)) return redirect()->route('verifikasi-pendaftaran.show', $pendaftaran)->with('error', 'Pendaftaran sudah diproses.');
        $input = $request->validate(['id_gedung' => ['nullable', 'integer', 'exists:gedung,id_gedung'], 'tanggal_masuk' => ['nullable', 'date'], 'rencana_keluar' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'], 'tersedia' => ['nullable', 'boolean']]);
        $start = $input['tanggal_masuk'] ?? today()->toDateString();
        $end = $input['rencana_keluar'] ?? today()->addMonths(6)->toDateString();
        $buildings = Gedung::whereIn('jenis_penghuni', $service->allowedBuildingTypes($pendaftaran->client))->whereHas('kamars')->orderBy('nama_gedung')->get();
        $building = $buildings->firstWhere('id_gedung', $input['id_gedung'] ?? null) ?? $buildings->first();
        $rooms = $building ? $building->kamars()->with('tipeKamar')->orderBy('nomor_kamar')->get()->map(function ($room) use ($service, $start, $end) {
            $room->setAttribute('occupied_beds', $service->occupiedBeds($room, $start, $end));
            return $room;
        }) : collect();
        if ($request->boolean('tersedia')) $rooms = $rooms->filter(fn ($room) => $room->status_kamar === 'tersedia' && count($room->occupied_beds) < ($room->getRelation('tipeKamar')?->kapasitas ?? 0));
        return view('verifikasi-pendaftaran.penempatan', ['registration' => $pendaftaran, 'buildings' => $buildings, 'building' => $building, 'groups' => $rooms->groupBy('id_tipeKamar'), 'start' => $start, 'end' => $end,
            'hasAvailableBed' => $rooms->contains(fn ($room) => $room->status_kamar === 'tersedia' && count($room->occupied_beds) < ($room->getRelation('tipeKamar')?->kapasitas ?? 0)),
        ]);
    }

    public function assign(Request $request, PendaftaranHunian $pendaftaran, RegistrationVerificationService $service)
    {
        abort_unless($pendaftaran->client->requiresAdminPlacement(), 403);
        $request->validate(['pilihan_bed' => ['nullable', 'regex:/^\d+:\d+$/']]);
        if ($request->filled('pilihan_bed')) {
            [$room, $bed] = explode(':', $request->input('pilihan_bed'));
            $request->merge(['id_kamar' => (int) $room, 'nomor_bed' => (int) $bed]);
        }
        $roomData = $request->validate([
            'id_gedung' => ['required', 'integer', 'exists:gedung,id_gedung'], 'id_kamar' => ['required', 'integer', 'exists:kamar,id_kamar'], 'nomor_bed' => ['required', 'integer', 'min:1'],
        ]);
        $data = $roomData + $this->validatedSchedule($request);
        $service->verify($pendaftaran, $request->user()->id_pengguna, $data);
        return redirect()->route('verifikasi-pendaftaran.index')->with('success', 'Pendaftaran terverifikasi dan penempatan kamar berhasil disimpan.');
    }

    private function validatedSchedule(Request $request): array
    {
        $data = $request->validate([
            'tanggal_masuk' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'], 'rencana_keluar' => ['required', 'date_format:Y-m-d', 'after:tanggal_masuk'],
            'jadwal_kunci_mulai' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:today'], 'jadwal_kunci_selesai' => ['required', 'date_format:Y-m-d\TH:i', 'after:jadwal_kunci_mulai'],
            'lokasi_kunci' => ['required', 'string', 'max:255'], 'penanggung_jawab' => ['required', 'string', 'max:150'], 'kontak_penanggung_jawab' => ['required', 'string', 'max:30'],
        ]);
        if (substr($data['jadwal_kunci_mulai'], 0, 10) < $data['tanggal_masuk'] || substr($data['jadwal_kunci_selesai'], 0, 10) > $data['rencana_keluar']) {
            throw ValidationException::withMessages(['jadwal_kunci_mulai' => 'Jadwal pengambilan kunci harus berada dalam periode hunian.']);
        }
        return $data;
    }

    public function decision(Request $request, PendaftaranHunian $pendaftaran)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['perlu_perbaikan', 'ditolak'])], 'catatan' => ['required', 'string', 'max:2000']]);
        DB::transaction(function () use ($request, $pendaftaran, $data) {
            $record = PendaftaranHunian::whereKey($pendaftaran->id_pendaftaran)->lockForUpdate()->firstOrFail();
            if (! in_array($record->status_pendaftaran, RegistrationVerificationService::PENDING, true)) throw ValidationException::withMessages(['status' => 'Pendaftaran sudah diproses.']);
            $record->update(['status_pendaftaran' => $data['status'], 'catatan_verifikasi' => $data['catatan'], 'id_admin' => $request->user()->id_pengguna, 'tanggal_verifikasi' => now()]);
        });
        if ($data['status'] === 'perlu_perbaikan') {
            return redirect()->route('verifikasi-pendaftaran.index')->with('success', 'Pendaftaran berhasil dikembalikan.');
        }
        return back()->with('success', 'Hasil pemeriksaan berhasil disimpan.');
    }
}
