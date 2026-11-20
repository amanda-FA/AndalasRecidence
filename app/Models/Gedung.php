<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gedung extends Model
{
    use HasFactory;

    protected $table = 'gedung';
    protected $primaryKey = 'id_gedung';
    public $timestamps = false;

    protected $fillable = [
        'nama_gedung',
        'jenis_penghuni',
        'jumlah_lantai',
        'keterangan',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_lantai' => 'integer',
        ];
    }

    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'id_gedung', 'id_gedung');
    }

    public function fasilitas(): HasMany
    {
        return $this->hasMany(Fasilitas::class, 'id_gedung', 'id_gedung')->where('aktif', true);
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    // Accessors for UI compatibility
    public function getIdAttribute(): int
    {
        return $this->id_gedung;
    }

    public function getNamaAttribute(): string
    {
        return $this->nama_gedung ?? '';
    }

    public function getPeruntukanAttribute(): string
    {
        return $this->jenis_penghuni ?? 'putra';
    }

    public function getStatusAttribute(): string
    {
        return 'aktif';
    }

    public function getKlasterWilayahAttribute(): string
    {
        return 'Kampus Limau Manis';
    }

    public function getDeskripsiAttribute(): ?string
    {
        return $this->keterangan;
    }

    public function getPeruntukanLabelAttribute(): string
    {
        return match ($this->jenis_penghuni) {
            'putra' => 'Khusus Putra',
            'putri' => 'Khusus Putri',
            'campuran' => 'Campuran / Pascasarjana',
            'tamu' => 'Guest House / Tamu',
            default => ucfirst($this->jenis_penghuni ?? 'Reguler'),
        };
    }
}
