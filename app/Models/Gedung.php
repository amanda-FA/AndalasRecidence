<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gedung extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'peruntukan',
        'status',
        'klaster_wilayah',
        'jumlah_lantai',
        'estimasi_kamar',
        'kapasitas_bed',
        'fasilitas',
        'foto',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'fasilitas' => 'array',
            'jumlah_lantai' => 'integer',
            'estimasi_kamar' => 'integer',
            'kapasitas_bed' => 'integer',
        ];
    }

    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }

    public function getPeruntukanLabelAttribute(): string
    {
        return match ($this->peruntukan) {
            'putra' => 'Khusus Putra',
            'putri' => 'Khusus Putri',
            'campuran' => 'Campuran / Pascasarjana',
            'tamu' => 'Guest House / Tamu',
            default => ucfirst($this->peruntukan),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif Operasional',
            'persiapan' => 'Persiapan / Renovasi',
            'draf' => 'Draf Tersimpan',
            default => ucfirst($this->status),
        };
    }
}
