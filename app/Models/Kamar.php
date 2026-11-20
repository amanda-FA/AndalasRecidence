<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';
    protected $primaryKey = 'id_kamar';
    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'id_tipeKamar',
        'nomor_kamar',
        'lantai',
        'status_kamar',
    ];

    protected function casts(): array
    {
        return [
            'lantai' => 'integer',
        ];
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    public function tipeKamar(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipeKamar', 'id_tipeKamar');
    }

    public function fasilitas(): HasMany
    {
        return $this->hasMany(Fasilitas::class, 'id_kamar', 'id_kamar')->where('aktif', true);
    }

    public function penempatanKamars(): HasMany
    {
        return $this->hasMany(PenempatanKamar::class, 'id_kamar', 'id_kamar');
    }

    // Accessors for UI compatibility
    public function getIdAttribute(): int
    {
        return $this->id_kamar;
    }

    public function getGedungIdAttribute(): int
    {
        return $this->id_gedung;
    }

    public function getStatusAttribute(): string
    {
        return $this->status_kamar ?? 'tersedia';
    }

    public function getKapasitasBedAttribute(): int
    {
        return $this->tipeKamar?->kapasitas ?? 4;
    }

    public function getBedTerisiAttribute(): int
    {
        return $this->penempatanKamars()->current()->count();
    }

    public function getBedTersediaAttribute(): int
    {
        return max(0, $this->kapasitas_bed - $this->bed_terisi);
    }

    public function getTarifAttribute(): float
    {
        return (float) ($this->tipeKamar?->tarif ?? 0);
    }

    public function getTipeKamarAttribute(): string
    {
        return $this->tipeKamar?->nama_tipe ?? 'Reguler';
    }
}
