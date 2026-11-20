<?php

namespace Database\Seeders;

use App\Models\JenisClient;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class JenisClientSeeder extends Seeder
{
    private const LEGACY_TYPES = [
        'Mahasiswa Reguler' => JenisClient::CATEGORIES['reguler'],
        'Mahasiswa KIP-K' => JenisClient::CATEGORIES['kipk'],
        'Non-Mahasiswa / Tamu' => JenisClient::CATEGORIES['guest'],
        'Tamu / Umum' => JenisClient::CATEGORIES['guest'],
        'Mahasiswa Internasional' => JenisClient::CATEGORIES['international_private'],
        'Mahasiswa Pascasarjana' => JenisClient::CATEGORIES['international_private'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            foreach (JenisClient::whereNotIn('jenis', array_values(JenisClient::CATEGORIES))->get() as $old) {
                $name = self::LEGACY_TYPES[$old->jenis] ?? throw new \RuntimeException('Jenis client belum memiliki pemetaan: '.$old->jenis);
                $target = JenisClient::where('jenis', $name)->first();
                if ($target) {
                    Client::where('id_jenis_client', $old->id_jenis_client)->update(['id_jenis_client' => $target->id_jenis_client]);
                    $old->delete();
                } else {
                    $old->update(['jenis' => $name]);
                }
            }
            foreach (JenisClient::CATEGORIES as $name) {
                JenisClient::firstOrCreate(['jenis' => $name]);
            }
            $kip = JenisClient::where('jenis', JenisClient::CATEGORIES['kipk'])->value('id_jenis_client');
            Client::where('id_jenis_client', $kip)->update(['status_KIPK' => 1]);
            Client::where('id_jenis_client', '!=', $kip)->update(['status_KIPK' => 0]);
        });
    }
}
