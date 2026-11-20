<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Fasilitas;
use App\Models\LaporanKerusakan;
use App\Models\PendaftaranHunian;
use App\Models\Pengguna;
use App\Models\PenempatanKamar;
use App\Models\PenugasanPerbaikan;
use App\Models\PeriodePendaftaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Data pengembangan opsional; sengaja tidak dipanggil dari DatabaseSeeder. */
class FadhilBackendDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Data uji Fadhil hanya boleh dijalankan pada environment local atau testing.');
        }

        $result = DB::transaction(function (): array {
            $today = Carbon::today('Asia/Jakarta')->toDateString();
            $fadhil = Pengguna::query()->where('email', 'client@unand.ac.id')->lockForUpdate()->firstOrFail();
            $admin = Pengguna::query()->where('email', 'admin@unand.ac.id')->firstOrFail();
            $technician = Pengguna::query()->where('email', 'teknisi@unand.ac.id')->firstOrFail();
            if ($fadhil->role?->nama_role !== 'Client'
                || $admin->role?->nama_role !== 'Admin Layanan'
                || $technician->role?->nama_role !== 'Teknisi') {
                throw new RuntimeException('Role akun Client, Admin Layanan, atau Teknisi tidak sesuai.');
            }
            $client = Client::query()->where('id_pengguna', $fadhil->id_pengguna)->lockForUpdate()->sole();
            $room = DB::table('kamar')
                ->join('gedung', 'gedung.id_gedung', '=', 'kamar.id_gedung')
                ->where('gedung.nama_gedung', 'Gedung Asrama Menpera (D)')
                ->where('kamar.nomor_kamar', '1A.01')
                ->select('kamar.*')->lockForUpdate()->sole();

            $activePlacements = DB::table('penempatan_kamar')
                ->join('pendaftaran_hunian', 'pendaftaran_hunian.id_pendaftaran', '=', 'penempatan_kamar.id_pendaftaran')
                ->where('pendaftaran_hunian.id_client', $client->id_client)
                ->where('penempatan_kamar.status_penempatan', 'aktif')
                ->select('penempatan_kamar.*')->lockForUpdate()->get();
            if ($activePlacements->count() > 1) {
                throw new RuntimeException('Fadhil memiliki lebih dari satu penempatan aktif. Periksa data sebelum menjalankan seeder.');
            }
            $placement = $activePlacements->first();
            if ($placement && ($placement->id_kamar !== $room->id_kamar || $placement->tanggal_keluar !== null)) {
                throw new RuntimeException('Fadhil sudah memiliki penempatan aktif lain. Seeder tidak memindahkan atau menimpa penempatan tersebut.');
            }

            if (! $placement) {
                $capacity = DB::table('tipe_kamar')->where('id_tipeKamar', $room->id_tipeKamar)->value('kapasitas');
                $occupants = DB::table('penempatan_kamar')->where('id_kamar', $room->id_kamar)->where('status_penempatan', 'aktif')->count();
                if ($room->status_kamar !== 'tersedia' || $occupants >= $capacity) {
                    throw new RuntimeException('Kamar 1A.01 Menpera (D) tidak tersedia atau sudah penuh.');
                }
                $period = PeriodePendaftaran::query()->where('status', 'dibuka')
                    ->whereDate('tanggal_buka', '<=', $today)->whereDate('tanggal_tutup', '>=', $today)
                    ->orderByDesc('tanggal_buka')->first();
                if (! $period) {
                    throw new RuntimeException('Buat atau buka periode pendaftaran yang mencakup hari ini sebelum menjalankan seeder.');
                }
                $registration = PendaftaranHunian::firstOrCreate([
                    'id_client' => $client->id_client,
                    'catatan_verifikasi' => '[DEMO-BE-FADHIL] Pendaftaran uji backend untuk kamar 1A.01 Menpera (D).',
                ], [
                    'id_periode' => $period->id_periode,
                    'id_tipeKamar' => $room->id_tipeKamar,
                    'id_admin' => $admin->id_pengguna,
                    'tanggal_daftar' => $today,
                    'status_pendaftaran' => 'terverifikasi',
                    'tanggal_verifikasi' => $today,
                ]);
                if (PenempatanKamar::query()->where('id_pendaftaran', $registration->id_pendaftaran)->exists()) {
                    throw new RuntimeException('Penempatan demo sudah pernah diakhiri. Seeder tidak mengaktifkan kembali riwayat penempatan.');
                }
                $placement = PenempatanKamar::create([
                    'id_pendaftaran' => $registration->id_pendaftaran,
                    'id_kamar' => $room->id_kamar,
                    'id_admin' => $admin->id_pengguna,
                    'tanggal_masuk' => $today,
                    'tanggal_keluar' => null,
                    'status_penempatan' => 'aktif',
                ]);
            }

            $samples = [
                ['facility' => 'Lampu utama kamar', 'condition' => 'Rusak Ringan', 'status' => 'diajukan', 'description' => '[Data uji Fadhil] Lampu utama kamar 1A.01 berkedip dan sesekali padam sejak pagi.'],
                ['facility' => 'Kunci pintu kamar', 'condition' => 'Rusak Ringan', 'status' => 'ditugaskan', 'description' => '[Data uji Fadhil] Kunci pintu kamar 1A.01 sulit diputar. Mohon pemeriksaan mekanisme pengunci.'],
                ['facility' => 'Lemari pakaian', 'condition' => 'Dalam Perbaikan', 'status' => 'dalam_pengerjaan', 'description' => '[Data uji Fadhil] Engsel lemari kamar 1A.01 longgar. Teknisi sedang mengencangkan engsel dan menyetel pintu.'],
                ['facility' => 'Meja belajar', 'condition' => 'Baik', 'status' => 'selesai', 'description' => '[Data uji Fadhil] Kaki meja belajar kamar 1A.01 goyang. Baut telah dikencangkan dan meja sudah stabil kembali.'],
            ];
            $reportIds = [];
            foreach ($samples as $sample) {
                $facility = Fasilitas::firstOrCreate([
                    'id_gedung' => $room->id_gedung,
                    'id_kamar' => $room->id_kamar,
                    'nama_fasilitas' => $sample['facility'],
                ], ['kondisi' => $sample['condition']]);
                $report = LaporanKerusakan::firstOrCreate([
                    'id_pengguna' => $fadhil->id_pengguna,
                    'id_fasilitas' => $facility->id_fasilitas,
                    'deskripsi' => $sample['description'],
                ], [
                    'foto' => null,
                    'tanggal_lapor' => $today,
                    'status_perbaikan' => $sample['status'],
                    'tanggal_selesai' => $sample['status'] === 'selesai' ? $today : null,
                ]);
                if (in_array($report->status_perbaikan, ['ditugaskan', 'dalam_pengerjaan', 'selesai'], true)) {
                    PenugasanPerbaikan::firstOrCreate(
                        ['id_laporan' => $report->id_laporan],
                        ['id_pengguna' => $technician->id_pengguna],
                    );
                }
                $reportIds[] = $report->id_laporan;
            }

            return [
                'database' => DB::connection()->getDatabaseName(),
                'id_pengguna' => $fadhil->id_pengguna,
                'id_client' => $client->id_client,
                'id_pendaftaran' => $placement->id_pendaftaran,
                'id_penempatan' => $placement->id_penempatan,
                'id_kamar' => $room->id_kamar,
                'id_laporan' => implode(', ', $reportIds),
            ];
        });

        $this->command?->info('Data backend Fadhil siap: 1 penempatan aktif di 1A.01 Menpera (D), 4 laporan kerusakan.');
        $this->command?->table(['Data', 'Nilai'], collect($result)->map(fn ($value, $key) => [$key, $value])->values()->all());
    }
}
