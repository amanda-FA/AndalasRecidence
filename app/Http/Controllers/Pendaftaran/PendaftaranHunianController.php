<?php

namespace App\Http\Controllers\Pendaftaran;

use App\Http\Controllers\Controller;
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
        return !$client->status_KIPK
            && in_array($client->jenisClient?->jenis, [
                'Mahasiswa Reguler',
                'Tamu / Umum',
            ], true);
    }

    private function gedungDiizinkan($client): array
    {
        if (!$this->bolehMemilihTipe($client)) {
            return [];
        }

        // Gedung yang menerima maba non-KIPK
        // sekaligus mahasiswa S1 non-maba.
        $gedung = [
            'Gedung Asrama RPX (A)',
            'Gedung Asrama Rusunawa (B)',
            'Gedung Asrama Pupera Putri (C)',
            'Gedung Asrama Menpera (D)',
            'Gedung Asrama RMS (E)',
            'Gedung Asrama Oren (F)',
            'Gedung Asrama Hijau (G)',
            'Gedung Asrama Pupera Putera (H)',
        ];

        if ($client->jenisClient?->jenis === 'Tamu / Umum') {
            $gedung[] = 'Gedung Asrama Nakes';
            $gedung[] = 'Gedung Asrama ASN';
        }

        // Nakes belum dimasukkan untuk Reguler karena
        // profil belum membedakan maba dan non-maba.
        return $gedung;
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
                $query->whereIn('nama_gedung', $gedungDiizinkan);

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

        $hariIni = today()->toDateString();

        $periodes = PeriodePendaftaran::where('status', 'dibuka')
            ->whereDate('tanggal_buka', '<=', $hariIni)
            ->whereDate('tanggal_tutup', '>=', $hariIni)
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
                        $gedung->nama_gedung,
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

        return view('pendaftaran.create', compact(
            'client',
            'periodes',
            'tipeKamars',
            'bolehMemilihTipe',
            'genderTipe'
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
            'nomor_identitas' => ['required', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => [
                'required',
                Rule::in(['laki-laki', 'perempuan']),
            ],
            'alamat_asal' => ['required', 'string', 'max:2000'],
            'berkas' => ['required', 'array', 'min:1', 'max:5'],
            'berkas.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'extensions:pdf,jpg,jpeg,png',
                'max:10240',
            ],
            'persetujuan' => ['accepted'],
        ], [
            'id_tipeKamar.required' => 'Pilih preferensi tipe kamar.',
            'id_tipeKamar.prohibited' =>
                'Tipe kamar kategori ini ditentukan Admin Layanan.',
            'berkas.required' =>
                'Lampirkan minimal satu berkas persyaratan.',
            'berkas.max' => 'Maksimal lima berkas per pengajuan.',
            'berkas.*.max' => 'Ukuran setiap berkas maksimal 10 MB.',
            'persetujuan.accepted' =>
                'Setujui pernyataan sebelum mengirim.',
        ]);

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
                    $this->bolehMemilihTipe($profil)
                    !== $bolehMemilihTipe
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

                if ($bolehMemilihTipe) {
                    $tipeValid = $this->queryTipe(
                        $profil,
                        $data['jenis_kelamin']
                    )
                        ->whereKey($data['id_tipeKamar'])
                        ->exists();

                    if (!$tipeValid) {
                        throw ValidationException::withMessages([
                            'id_tipeKamar' =>
                                'Tipe kamar tidak sesuai dengan kategori atau jenis kelamin pendaftar.',
                        ]);
                    }
                }

                $profil->update([
                    'nomor_identitas' => $data['nomor_identitas'],
                    'pekerjaan' => $data['pekerjaan'] ?? null,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'alamat_asal' => $data['alamat_asal'],
                ]);

                $pendaftaran = PendaftaranHunian::create([
                    'id_client' => $profil->id_client,
                    'id_periode' => $periode->id_periode,
                    'id_tipeKamar' => $bolehMemilihTipe
                        ? $data['id_tipeKamar']
                        : null,
                    'id_admin' => null,
                    'tanggal_daftar' => today(),
                    'status_pendaftaran' => 'menunggu_verifikasi',
                    'tanggal_verifikasi' => null,
                    'catatan_verifikasi' => null,
                ]);

                foreach ($request->file('berkas', []) as $file) {
                    $path = $fileStorage->store(
                        $file,
                        'pendaftaran',
                        'local'
                    );

                    $paths[] = $path;

                    $pendaftaran->berkasPendaftarans()->create([
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