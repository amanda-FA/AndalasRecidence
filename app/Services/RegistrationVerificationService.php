<?php

namespace App\Services;

use App\Models\{Client, JenisBerkas, Kamar, PendaftaranHunian, PenempatanKamar};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationVerificationService
{
    public const PENDING = ['menunggu_verifikasi', 'menunggu', 'diajukan', 'perlu_perbaikan'];
    public const SCHEDULE_FIELDS = ['tanggal_masuk', 'rencana_keluar', 'jadwal_kunci_mulai', 'jadwal_kunci_selesai', 'lokasi_kunci', 'penanggung_jawab', 'kontak_penanggung_jawab'];

    public function overlaps($query, string $start, string $end)
    {
        return $query->where('status_penempatan', 'aktif')->whereDate('tanggal_masuk', '<=', $end)
            ->where(fn ($q) => $q->whereDate('tanggal_keluar', '>=', $start)->orWhere(fn ($open) => $open->whereNull('tanggal_keluar')
                ->where(fn ($planned) => $planned->whereNull('rencana_keluar')->orWhereDate('rencana_keluar', '>=', $start))));
    }

    public function occupiedBeds(Kamar $room, string $start, string $end): array
    {
        $placements = $this->overlaps(PenempatanKamar::where('id_kamar', $room->id_kamar), $start, $end)->get();
        $beds = $placements->pluck('nomor_bed')->filter()->map(fn ($id) => (int) $id)->all();
        $room->setAttribute('unrecorded_beds', $placements->whereNull('nomor_bed')->count());
        // Unknown historical bed positions must not be guessed when assigning a physical bed.
        if ($room->unrecorded_beds) return range(1, max(1, (int) $room->getRelation('tipeKamar')?->kapasitas));
        return $beds;
    }

    public function allowedBuildingTypes(Client $client): array
    {
        return match (strtolower($client->jenis_kelamin ?? '')) {
            'l', 'laki-laki', 'laki laki', 'putra' => ['putra', 'campuran', 'tamu'],
            'p', 'perempuan', 'putri' => ['putri', 'campuran', 'tamu'],
            default => [],
        };
    }

    public function verify(PendaftaranHunian $registration, int $adminId, ?array $assignment = null): void
    {
        DB::transaction(function () use ($registration, $adminId, $assignment) {
            $client = Client::whereKey($registration->id_client)->lockForUpdate()->firstOrFail();
            $record = PendaftaranHunian::whereKey($registration->id_pendaftaran)->lockForUpdate()->firstOrFail();
            if (! in_array($record->status_pendaftaran, self::PENDING, true)) {
                throw ValidationException::withMessages(['verifikasi' => 'Pendaftaran sudah diproses. Muat ulang halaman.']);
            }
            if ($record->berkasPendaftarans()->whereNull('id_jenis_berkas')->exists()) {
                throw ValidationException::withMessages(['berkas' => 'Tentukan jenis semua berkas lama pada halaman detail sebelum memverifikasi pendaftaran.']);
            }
            $types = JenisBerkas::pluck('id_jenis_berkas');
            if ($types->isEmpty() || $types->diff($record->berkasPendaftarans()->pluck('id_jenis_berkas'))->isNotEmpty()) {
                throw ValidationException::withMessages(['berkas' => 'Berkas persyaratan belum lengkap.']);
            }
            foreach ($record->berkasPendaftarans as $document) {
                if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($document->path_file)) {
                    throw ValidationException::withMessages(['berkas' => 'Salah satu file berkas belum tersedia.']);
                }
            }
            if ($client->requiresAdminPlacement()) {
                if (! $assignment) throw ValidationException::withMessages(['penempatan' => 'Pilih gedung, kamar, dan bed terlebih dahulu.']);
                $room = Kamar::with(['gedung', 'tipeKamar'])->whereKey($assignment['id_kamar'])->lockForUpdate()->firstOrFail();
                $capacity = $room->getRelation('tipeKamar')?->kapasitas ?? 0;
                if ($room->id_gedung != $assignment['id_gedung'] || ! in_array($room->gedung->jenis_penghuni, $this->allowedBuildingTypes($client), true)
                    || $room->status_kamar !== 'tersedia' || $assignment['nomor_bed'] > $capacity || $assignment['nomor_bed'] < 1) {
                    throw ValidationException::withMessages(['id_kamar' => 'Kamar atau bed tidak sesuai dengan peruntukan dan kapasitas.']);
                }
                $busy = $this->occupiedBeds($room, $assignment['tanggal_masuk'], $assignment['rencana_keluar']);
                if ($room->unrecorded_beds) throw ValidationException::withMessages(['nomor_bed' => 'Posisi bed penghuni lama belum tercatat. Pilih kamar lain atau lengkapi data bed lama terlebih dahulu.']);
                if (count($busy) >= $capacity || in_array((int) $assignment['nomor_bed'], $busy, true)) {
                    throw ValidationException::withMessages(['nomor_bed' => 'Bed sudah terisi pada periode tersebut. Pilih bed lain.']);
                }
                $other = PenempatanKamar::whereHas('pendaftaranHunian', fn ($q) => $q->where('id_client', $client->id_client));
                if ($this->overlaps($other, $assignment['tanggal_masuk'], $assignment['rencana_keluar'])->exists()) {
                    throw ValidationException::withMessages(['penempatan' => 'Client sudah mempunyai penempatan pada rentang tanggal tersebut.']);
                }
                PenempatanKamar::create(\Illuminate\Support\Arr::except($assignment, ['id_gedung']) + ['id_pendaftaran' => $record->id_pendaftaran, 'id_admin' => $adminId, 'status_penempatan' => 'aktif']);
                $record->id_tipeKamar = $room->id_tipeKamar;
                $record->id_kamar = $room->id_kamar;
            } else {
                if (! $assignment || array_diff(self::SCHEDULE_FIELDS, array_keys($assignment))) {
                    throw ValidationException::withMessages(['jadwal' => 'Lengkapi periode hunian dan jadwal pengambilan kunci terlebih dahulu.']);
                }
                $room = Kamar::with(['gedung', 'tipeKamar'])->whereKey($record->id_kamar)->lockForUpdate()->first();
                if (! $room) throw ValidationException::withMessages(['verifikasi' => 'Pilihan kamar client belum tercatat.']);
                $capacity = $room->getRelation('tipeKamar')?->kapasitas ?? 0;
                $occupied = $this->overlaps(PenempatanKamar::where('id_kamar', $room->id_kamar), $assignment['tanggal_masuk'], $assignment['rencana_keluar'])->count();
                if ($room->status_kamar !== 'tersedia' || ! in_array($room->gedung->jenis_penghuni, $this->allowedBuildingTypes($client), true) || $occupied >= $capacity) {
                    throw ValidationException::withMessages(['verifikasi' => 'Kamar pilihan client sudah tidak tersedia atau tidak sesuai peruntukan.']);
                }
                $record->id_tipeKamar = $room->id_tipeKamar;
                $record->fill(\Illuminate\Support\Arr::only($assignment, self::SCHEDULE_FIELDS));
            }
            $record->fill(['id_admin' => $adminId, 'status_pendaftaran' => 'terverifikasi', 'tanggal_verifikasi' => now(), 'catatan_verifikasi' => null])->save();
        });
    }
}
