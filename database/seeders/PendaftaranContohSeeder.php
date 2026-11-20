<?php

namespace Database\Seeders;

use App\Models\{BerkasPendaftaran, Client, JenisBerkas, JenisClient, Kamar, PendaftaranHunian, Pengguna, PeriodePendaftaran, Role};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Storage};

class PendaftaranContohSeeder extends Seeder
{
    private function selectedRoom(string $gender, ?int $type = null, int $offset = 0): Kamar
    {
        $allowed = $gender === 'perempuan' ? ['putri','campuran','tamu'] : ['putra','campuran','tamu'];
        $rooms = Kamar::with('tipeKamar')->where('status_kamar','tersedia')
            ->whereHas('gedung',fn($q) => $q->whereIn('jenis_penghuni',$allowed))
            ->when($type,fn($q) => $q->where('id_tipeKamar',$type))
            ->withCount(['penempatanKamars as occupied' => fn($q) => $q->current()])->orderBy('id_kamar')->get()
            ->filter(fn($room) => $room->occupied < ($room->getRelation('tipeKamar')?->kapasitas ?? 0))->values();
        $groups = $rooms->groupBy('id_gedung')->values();
        $group = $groups->isNotEmpty() ? $groups[$offset % $groups->count()] : collect();
        $room = $group->isNotEmpty() ? $group->values()[$offset % $group->count()] : null;
        if (! $room) throw new \RuntimeException('Kamar tersedia belum ada untuk data contoh pendaftaran.');
        return $room;
    }

    public function run(): void
    {
        $this->call([JenisClientSeeder::class, JenisBerkasSeeder::class]);
        $image = public_path('images/asramaunand.png');
        if (! is_file($image)) $image = public_path('images/logo_andalasresidence.png');
        if (! is_file($image)) throw new \RuntimeException('Gambar untuk lampiran belum tersedia.');
        DB::transaction(function () use ($image) {
            // Existing demonstration accounts retain their email and verification results.
            $aliases = ['reguler' => 'Fajar Ramadhan', 'kipk' => 'Annisa Putri Maharani', 'summer_course' => 'Kevin Satria Wijaya', 'guest' => 'Andi Prasetyo'];
            foreach ($aliases as $code => $name) {
                $user = Pengguna::where('email','uji.verifikasi.'.$code.'@andalas.test')->first();
                if (! $user) continue;
                if (str_starts_with($user->nama_lengkap,'Uji Verifikasi ')) $user->update(['nama_lengkap' => $name]);
                $client = $user->client;
                if ($client && str_starts_with($client->nomor_identitas ?? '', 'UJI-')) $client->update(['nomor_identitas' => '20261100'.$client->id_client]);
                if ($client && $client->canChooseRoom()) {
                    foreach ($client->pendaftaranHunians()->whereNull('id_kamar')->get() as $record) {
                        $record->update(['id_kamar' => $this->selectedRoom($client->jenis_kelamin, $record->id_tipeKamar)->id_kamar]);
                    }
                }
            }
            $period = PeriodePendaftaran::firstOrCreate(['nama_periode' => 'Pendaftaran Hunian Semester Ganjil 2026/2027'], ['tahun_akademik' => '2026/2027','tanggal_buka' => today()->subDays(30),'tanggal_tutup' => today()->addDays(90),'status' => 'dibuka']);
            $role = Role::firstOrCreate(['nama_role' => 'Client']);
            $people = [
                ['Nadia Putri Azzahra','reguler','perempuan','Padang'],
                ['Rizky Aditya Pratama','reguler','laki-laki','Bukittinggi'],
                ['Salsabila Rahma Putri','kipk','perempuan','Padang Panjang'],
                ['Farhan Akbar Saputra','kipk','laki-laki','Solok'],
                ['Alya Nur Safitri','summer_course','perempuan','Pekanbaru'],
                ['Dimas Ardiansyah','summer_course','laki-laki','Jambi'],
                ['Ratna Wulandari','guest','perempuan','Payakumbuh'],
                ['Budi Santoso','guest','laki-laki','Padang'],
            ];
            foreach ($people as $index => [$name,$category,$gender,$city]) {
                $slug = strtolower(str_replace(' ','.',$name));
                $user = Pengguna::firstOrCreate(['email' => $slug.'@example.test'], ['id_role' => $role->id_role,'nama_lengkap' => $name,'username' => 'contoh_'.str_replace('.','_',$slug),'password' => 'password','status_aktif' => 1]);
                $client = Client::firstOrCreate(['id_pengguna' => $user->id_pengguna], ['id_jenis_client' => JenisClient::where('jenis',JenisClient::CATEGORIES[$category])->value('id_jenis_client'),'jenis_kelamin' => $gender,'status_KIPK' => $category === 'kipk','nomor_identitas' => $category === 'guest' ? 'TM-2026-'.str_pad($index+1,3,'0',STR_PAD_LEFT) : '26115020'.str_pad($index+1,2,'0',STR_PAD_LEFT),'pekerjaan' => $category === 'guest' ? 'Dosen Tamu' : 'Mahasiswa','alamat_asal' => $city.', Sumatera']);
                $room = $client->canChooseRoom() ? $this->selectedRoom($gender, null, $index) : null;
                $record = PendaftaranHunian::firstOrCreate(['id_client' => $client->id_client,'id_periode' => $period->id_periode], ['id_kamar' => $room?->id_kamar,'id_tipeKamar' => $room?->id_tipeKamar,'tanggal_daftar' => now()->subDays(2)->addMinutes($index*45),'status_pendaftaran' => 'menunggu_verifikasi']);
                foreach (JenisBerkas::all() as $kind) {
                    $path = 'pendaftaran/contoh/'.$user->id_pengguna.'/'.$kind->id_jenis_berkas.'.png';
                    if (! Storage::disk('local')->exists($path) && ! Storage::disk('local')->put($path,file_get_contents($image))) throw new \RuntimeException('Lampiran contoh gagal disimpan.');
                    BerkasPendaftaran::firstOrCreate(['id_pendaftaran' => $record->id_pendaftaran,'id_jenis_berkas' => $kind->id_jenis_berkas], ['path_file' => $path,'tanggal_upload' => $record->tanggal_daftar->toDateString()]);
                }
            }
        });
    }
}
