<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PilihanFasilitasSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (Fasilitas::DEFAULT_CHOICES as $scope => $names) {
                foreach ($names as $name) {
                    $choice = Fasilitas::pilihan()->where(fn ($query) => $query->where('lingkup', $scope)->orWhereNull('lingkup'))
                        ->whereRaw('LOWER(nama_fasilitas) = ?', [mb_strtolower($name)])->first()
                        ?? Fasilitas::create(['nama_fasilitas' => $name, 'lingkup' => $scope, 'aktif' => true]);
                    if ($choice->lingkup !== $scope) $choice->update(['lingkup' => $scope]);
                    $inventory = Fasilitas::whereNull('id_fasilitas_acuan')->whereRaw('LOWER(nama_fasilitas) = ?', [mb_strtolower($name)]);
                    if ($scope === 'kamar') $inventory->whereNotNull('id_kamar');
                    else $inventory->whereNotNull('id_gedung')->whereNull('id_kamar');
                    $inventory->update(['id_fasilitas_acuan' => $choice->id_fasilitas]);
                }
            }
        });
    }
}
