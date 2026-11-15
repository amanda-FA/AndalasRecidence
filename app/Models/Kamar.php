<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'gedung_id',
        'nomor_kamar',
        'lantai',
        'sayap',
        'tipe_kamar',
        'kapasitas_bed',
        'bed_terisi',
        'tarif',
        'status',
        'rfid_enabled',
        'fasilitas',
        'foto',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'lantai' => 'integer',
            'kapasitas_bed' => 'integer',
            'bed_terisi' => 'integer',
            'tarif' => 'integer',
            'rfid_enabled' => 'boolean',
            'fasilitas' => 'array',
        ];
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class);
    }

    public function getBedTersediaAttribute(): int
    {
        return max(0, $this->kapasitas_bed - $this->bed_terisi);
    }

    public function getOkupansiPersenAttribute(): float
    {
        if ($this->kapasitas_bed <= 0) {
            return 0;
        }

        return round(($this->bed_terisi / $this->kapasitas_bed) * 100, 1);
    }
}
