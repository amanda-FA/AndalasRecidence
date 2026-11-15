<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    protected $primaryKey = 'id_fasilitas';
    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'id_kamar',
        'nama_fasilitas',
        'kondisi',
    ];

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function laporanKerusakans(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function detailPengecekans(): HasMany
    {
        return $this->hasMany(DetailPengecekan::class, 'id_fasilitas', 'id_fasilitas');
    }
}
