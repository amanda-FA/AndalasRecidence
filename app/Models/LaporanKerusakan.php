<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanKerusakan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakan';
    protected $primaryKey = 'id_laporan';
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'id_fasilitas',
        'deskripsi',
        'foto',
        'tanggal_lapor',
        'status_perbaikan',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lapor' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function penugasanPerbaikans(): HasMany
    {
        return $this->hasMany(PenugasanPerbaikan::class, 'id_laporan', 'id_laporan');
    }
}
