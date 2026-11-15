<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKamar extends Model
{
    use HasFactory;

    protected $table = 'tipe_kamar';
    protected $primaryKey = 'id_tipeKamar';
    public $timestamps = false;

    protected $fillable = [
        'nama_tipe',
        'kapasitas',
        'tarif',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'tarif' => 'decimal:2',
        ];
    }

    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'id_tipeKamar', 'id_tipeKamar');
    }

    public function pendaftaranHunians(): HasMany
    {
        return $this->hasMany(PendaftaranHunian::class, 'id_tipeKamar', 'id_tipeKamar');
    }
}
