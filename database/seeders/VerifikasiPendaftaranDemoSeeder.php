<?php

namespace Database\Seeders;

use App\Models\{BerkasPendaftaran, Client, JenisBerkas, JenisClient, Kamar, PendaftaranHunian, Pengguna, PeriodePendaftaran, Role};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Storage};

class VerifikasiPendaftaranDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([JenisClientSeeder::class, JenisBerkasSeeder::class]);
        $image = public_path('images/asramaunand.png');
        if (! is_file($image)) $image = public_path('images/logo_andalasresidence.png');
        if (! is_file($image)) throw new \RuntimeException('Aset gambar untuk lampiran uji tidak ditemukan.');
        DB::transaction(function () use ($image) {
            $role = Role::firstOrCreate(['nama_role' => 'Client']);
            $period = PeriodePendaftaran::firstOrCreate(['nama_periode' => 'Periode Uji Verifikasi 2026/2027'], [
                'tahun_akademik' => '2026/2027', 'tanggal_buka' => today()->subDays(7), 'tanggal_tutup' => today()->addDays(90), 'status' => 'dibuka',
            ]);
            foreach (JenisClient::CATEGORIES as $index => $category) {
                $user = Pengguna::firstOrCreate(['email' => 'uji.verifikasi.'.$index.'@andalas.test'], [
                    'id_role' => $role->id_role, 'nama_lengkap' => 'Uji Verifikasi '.$category, 'username' => 'uji_verifikasi_'.$index,
                    'password' => 'password', 'no_hp' => '081200000001', 'status_aktif' => 1,
                ]);
                $client = Client::firstOrCreate(['id_pengguna' => $user->id_pengguna], [
                    'id_jenis_client' => JenisClient::where('jenis', $category)->value('id_jenis_client'),
                    'nomor_identitas' => 'UJI-'.strtoupper($index), 'pekerjaan' => $index === 'guest' ? 'Tamu' : 'Mahasiswa',
                    'jenis_kelamin' => $index === 'kipk' ? 'perempuan' : 'laki-laki', 'status_KIPK' => $index === 'kipk', 'alamat_asal' => 'Alamat contoh untuk pengujian verifikasi',
                ]);
                $type = null;
                $selectedRoom = null;
                if ($client->canChooseRoom()) {
                    $selectedRoom = Kamar::whereHas('gedung', fn ($q) => $q->whereIn('jenis_penghuni', ['putra','campuran','tamu']))
                        ->where('status_kamar', 'tersedia')->first();
                    $type = $selectedRoom?->id_tipeKamar;
                    if (! $type) throw new \RuntimeException('Tipe kamar dari database belum tersedia untuk data uji.');
                }
                $record = PendaftaranHunian::firstOrCreate(['id_client' => $client->id_client, 'id_periode' => $period->id_periode], [
                    'id_kamar' => $selectedRoom?->id_kamar, 'id_tipeKamar' => $type, 'tanggal_daftar' => now()->subMinutes(array_search($index, array_keys(JenisClient::CATEGORIES), true) * 25), 'status_pendaftaran' => 'menunggu_verifikasi',
                ]);
                foreach (JenisBerkas::all() as $kind) {
                    $path = 'pendaftaran/uji-verifikasi/'.$index.'/'.$kind->id_jenis_berkas.'.png';
                    if (! Storage::disk('local')->exists($path)) {
                        if (! Storage::disk('local')->put($path, file_get_contents($image))) throw new \RuntimeException('Lampiran uji gagal disimpan.');
                    }
                    BerkasPendaftaran::firstOrCreate(['id_pendaftaran' => $record->id_pendaftaran, 'id_jenis_berkas' => $kind->id_jenis_berkas], ['path_file' => $path, 'tanggal_upload' => today()]);
                }
            }
        });
    }
}
