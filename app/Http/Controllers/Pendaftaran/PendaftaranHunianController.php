<?php

namespace App\Http\Controllers\Pendaftaran;

use App\Http\Controllers\Controller;
use App\Models\JenisBerkas;
use App\Models\JenisClient;
use App\Models\Kamar;
use App\Models\PendaftaranHunian;
use App\Models\PeriodePendaftaran;
use App\Models\TipeKamar;
use App\Services\FileStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class PendaftaranHunianController extends Controller
{
    private function bolehMemilihTipe($client): bool
    {
        return $client->canChooseRoom();
    }

    private function kategoriValid($client): bool
    {
        return $client->jenisClient !== null
            && in_array($client->jenisClient->jenis, JenisClient::CATEGORIES, true);
    }

    private function gedungDiizinkan($client): array
    {
        if (!$this->bolehMemilihTipe($client)) {
            return [];
        }

        return $client->jenisClient->eligibleGedungs()
            ->pluck('gedung.id_gedung')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function queryTipe(
        $client,
        ?string $jenisKelamin = null
    ): Builder {
        $gedungDiizinkan = $this->gedungDiizinkan($client);

        $jenisPenghuni = match ($jenisKelamin) {
            'laki-laki' => ['putra', 'campuran', 'tamu'],
            'perempuan' => ['putri', 'campuran', 'tamu'],
            default => null,
        };

        return TipeKamar::query()
            ->whereHas('kamars.gedung', function (Builder $query) use (
                $gedungDiizinkan,
                $jenisPenghuni
            ) {
                $query->whereIn('gedung.id_gedung', $gedungDiizinkan);

                if ($jenisPenghuni !== null) {
                    $query->whereIn('jenis_penghuni', $jenisPenghuni);
                }
            });
    }

    public function create(Request $request)
    {
        $client = $request->user()
            ->client()
            ->with(['pengguna', 'jenisClient'])
            ->first();

        abort_unless(
            $client,
            403,
            'Profil client belum tersedia. Hubungi Admin Layanan.'
        );

        abort_unless($this->kategoriValid($client), 403, 'Kategori Client belum valid. Hubungi Admin Layanan.');

        $hariIni = today()->toDateString();

        $periodeTerdaftar = PendaftaranHunian::where('id_client', $client->id_client)
            ->pluck('id_periode');

        $periodes = PeriodePendaftaran::where('status', 'dibuka')
            ->whereDate('tanggal_buka', '<=', $hariIni)
            ->whereDate('tanggal_tutup', '>=', $hariIni)
            ->whereNotIn('id_periode', $periodeTerdaftar)
            ->orderByDesc('tanggal_buka')
            ->get();

        $bolehMemilihTipe = $this->bolehMemilihTipe($client);

        $tipeKamars = $bolehMemilihTipe
            ? $this->queryTipe($client)
                ->with('kamars.gedung')
                ->orderBy('nama_tipe')
                ->get()
            : collect();

        // Informasi untuk menyaring dropdown ketika
        // client memilih jenis kelamin di formulir.
        $genderTipe = [];
        $gedungDiizinkan = $this->gedungDiizinkan($client);

        foreach ($tipeKamars as $tipe) {
            $gender = [];

            foreach ($tipe->kamars as $kamar) {
                $gedung = $kamar->gedung;

                if (
                    !$gedung
                    || !in_array(
                        (int) $gedung->id_gedung,
                        $gedungDiizinkan,
                        true
                    )
                ) {
                    continue;
                }

                $gender = array_merge(
                    $gender,
                    match ($gedung->jenis_penghuni) {
                        'putra' => ['laki-laki'],
                        'putri' => ['perempuan'],
                        'campuran', 'tamu' => [
                            'laki-laki',
                            'perempuan',
                        ],
                        default => [],
                    }
                );
            }

            $genderTipe[$tipe->id_tipeKamar] = array_values(
                array_unique($gender)
            );
        }

        $roomChoices = $bolehMemilihTipe ? Kamar::with(['gedung', 'tipeKamar'])
            ->where('status_kamar', 'tersedia')
            ->whereHas('gedung', fn ($q) => $q->whereIn('gedung.id_gedung', $gedungDiizinkan))
            ->withCount(['penempatanKamars as occupied' => fn ($q) => $q->current()])
            ->orderBy('nomor_kamar')->get()->filter(fn ($room) => $room->occupied < ($room->getRelation('tipeKamar')?->kapasitas ?? 0))
            ->map(fn ($room) => [
                'id' => $room->id_kamar, 'building_id' => $room->id_gedung, 'building' => $room->gedung->nama_gedung,
                'type_id' => $room->id_tipeKamar, 'type' => $room->getRelation('tipeKamar')->nama_tipe,
                'number' => $room->nomor_kamar, 'gender' => $room->gedung->jenis_penghuni,
                'available' => $room->getRelation('tipeKamar')->kapasitas - $room->occupied,
            ])->values() : collect();

        $jenisBerkas = JenisBerkas::orderBy('id_jenis_berkas')->get(['id_jenis_berkas', 'nama_berkas']);

        return view('pendaftaran.create', compact(
            'client',
            'periodes',
            'tipeKamars',
            'bolehMemilihTipe',
            'genderTipe',
            'jenisBerkas',
            'roomChoices'
        ));
    }

    public function store(
        Request $request,
        FileStorageService $fileStorage
    ) {
        $client = $request->user()
            ->client()
            ->with('jenisClient')
            ->first();

        abort_unless(
            $client,
            403,
            'Profil client belum tersedia.'
        );

        if (!$this->kategoriValid($client)) {
            throw ValidationException::withMessages([
                'kategori' => 'Kategori Client belum valid. Hubungi Admin Layanan.',
            ]);
        }

        $bolehMemilihTipe = $this->bolehMemilihTipe($client);

        $data = $request->validate([
            'id_periode' => [
                'required',
                'integer',
                Rule::exists('periode_pendaftaran', 'id_periode'),
            ],
            'id_tipeKamar' => [
                Rule::requiredIf($bolehMemilihTipe),
                Rule::prohibitedIf(!$bolehMemilihTipe),
                'nullable',
                'integer',
                Rule::exists('tipe_kamar', 'id_tipeKamar'),
            ],
            'id_kamar' => [Rule::requiredIf($bolehMemilihTipe), Rule::prohibitedIf(!$bolehMemilihTipe), 'nullable', 'integer', 'exists:kamar,id_kamar'],
            'id_gedung' => [Rule::requiredIf($bolehMemilihTipe), Rule::prohibitedIf(!$bolehMemilihTipe), 'nullable', 'integer', 'exists:gedung,id_gedung'],
            'nomor_identitas' => ['required', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => [
                'required',
                Rule::in(['laki-laki', 'perempuan']),
            ],
            'alamat_asal' => ['required', 'string', 'max:2000'],
            'berkas' => ['required', 'array', 'min:1', 'max:5'],
            'id_jenis_berkas' => ['required', 'array', 'min:1', 'max:5'],
            'id_jenis_berkas.*' => ['required', 'integer', 'distinct', Rule::exists('jenis_berkas', 'id_jenis_berkas')],
            'berkas.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'extensions:pdf,jpg,jpeg,png',
                'max:10240',
            ],
            'persetujuan' => ['accepted'],
        ], [
            'id_kamar.required' => 'Pilih kamar yang akan didaftarkan.',
            'id_gedung.required' => 'Pilih gedung hunian.',
            'id_tipeKamar.required' => 'Pilih tipe kamar.',
            'id_tipeKamar.prohibited' =>
                'Tipe kamar kategori ini ditentukan Admin Layanan.',
            'berkas.required' =>
                'Lampirkan minimal satu berkas persyaratan.',
            'berkas.max' => 'Maksimal lima berkas per pengajuan.',
            'berkas.*.max' => 'Ukuran setiap berkas maksimal 10 MB.',
            'id_jenis_berkas.required' => 'Pilih jenis untuk setiap berkas yang dilampirkan.',
            'id_jenis_berkas.*.required' => 'Pilih jenis berkas.',
            'id_jenis_berkas.*.exists' => 'Jenis berkas yang dipilih tidak tersedia.',
            'persetujuan.accepted' =>
                'Setujui pernyataan sebelum mengirim.',
        ]);

        if (array_keys($request->file('berkas', [])) !== array_keys($data['id_jenis_berkas'])) {
            throw ValidationException::withMessages([
                'id_jenis_berkas' => 'Setiap lampiran harus memiliki satu pilihan jenis berkas.',
            ]);
        }

        $requiredTypes = JenisBerkas::pluck('id_jenis_berkas');
        if ($requiredTypes->isEmpty() || $requiredTypes->diff(collect($data['id_jenis_berkas'])->map(fn ($id) => (int) $id))->isNotEmpty()) {
            throw ValidationException::withMessages(['berkas' => 'Lengkapi semua berkas: FC Kartu Keluarga, FC KTP, dan Hasil Tes Kesehatan.']);
        }

        $paths = [];

        try {
            DB::transaction(function () use (
                $client,
                $data,
                $request,
                $fileStorage,
                $bolehMemilihTipe,
                &$paths
            ) {
                $profil = $client->newQuery()
                    ->whereKey($client->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $profil->load('jenisClient');

                if (
                    !$this->kategoriValid($profil)
                    || $profil->id_jenis_client != $client->id_jenis_client
                    || $this->bolehMemilihTipe($profil) !== $bolehMemilihTipe
                ) {
                    throw ValidationException::withMessages([
                        'kategori' =>
                            'Kategori profil berubah. Muat ulang formulir dan periksa kembali pilihanmu.',
                    ]);
                }

                $periode = PeriodePendaftaran::whereKey($data['id_periode'])
                    ->lockForUpdate()
                    ->first();

                if (
                    !$periode
                    || $periode->status !== 'dibuka'
                    || today()->lt($periode->tanggal_buka)
                    || today()->gt($periode->tanggal_tutup)
                ) {
                    throw ValidationException::withMessages([
                        'id_periode' =>
                            'Periode ini sedang tidak menerima pendaftaran.',
                    ]);
                }

                $sudahMendaftar = PendaftaranHunian::where(
                    'id_client',
                    $profil->id_client
                )
                    ->where('id_periode', $periode->id_periode)
                    ->exists();

                if ($sudahMendaftar) {
                    throw ValidationException::withMessages([
                        'id_periode' =>
                            'Kamu sudah mengajukan pendaftaran pada periode ini.',
                    ]);
                }

                $selectedRoom = null;
                if ($bolehMemilihTipe) {
                    $selectedRoom = Kamar::with(['gedung', 'tipeKamar'])->whereKey($data['id_kamar'])->lockForUpdate()->firstOrFail();
                    $allowedGender = $data['jenis_kelamin'] === 'laki-laki' ? ['putra','campuran','tamu'] : ['putri','campuran','tamu'];
                    if (!$selectedRoom->gedung || $selectedRoom->id_gedung != $data['id_gedung'] || $selectedRoom->id_tipeKamar != $data['id_tipeKamar']
                        || !in_array((int) $selectedRoom->id_gedung, $this->gedungDiizinkan($profil), true)
                        || !in_array($selectedRoom->gedung->jenis_penghuni, $allowedGender, true)
                        || $selectedRoom->status_kamar !== 'tersedia'
                        || $selectedRoom->penempatanKamars()->current()->count() >= ($selectedRoom->getRelation('tipeKamar')?->kapasitas ?? 0)) {
                        throw ValidationException::withMessages(['id_kamar' => 'Pilihan gedung, tipe, atau kamar tidak sesuai atau kamar sudah penuh.']);
                    }
                }

                $profil->update([
                    'nomor_identitas' => $data['nomor_identitas'],
                    'pekerjaan' => $data['pekerjaan'] ?? $profil->pekerjaan,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'alamat_asal' => $data['alamat_asal'],
                ]);

                $pendaftaran = PendaftaranHunian::create([
                    'id_client' => $profil->id_client,
                    'id_periode' => $periode->id_periode,
                    'id_tipeKamar' => $bolehMemilihTipe
                        ? $data['id_tipeKamar']
                        : null,
                    'id_kamar' => $selectedRoom?->id_kamar,
                    'id_admin' => null,
                    'tanggal_daftar' => now(),
                    'status_pendaftaran' => 'menunggu_verifikasi',
                    'tanggal_verifikasi' => null,
                    'catatan_verifikasi' => null,
                ]);

                foreach ($request->file('berkas', []) as $index => $file) {
                    $path = $fileStorage->store(
                        $file,
                        'pendaftaran',
                        'local'
                    );

                    $paths[] = $path;

                    $pendaftaran->berkasPendaftarans()->create([
                        'id_jenis_berkas' => $data['id_jenis_berkas'][$index],
                        'path_file' => $path,
                        'tanggal_upload' => today(),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach ($paths as $path) {
                try {
                    $fileStorage->delete($path, 'local');
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        return redirect()->route('pendaftaran.create')
            ->with(
                'success',
                'Pendaftaran berhasil dikirim dan menunggu verifikasi Admin Layanan.'
            );
    }
}
