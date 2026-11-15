<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'penugasan_perbaikan';
    protected $primaryKey = 'id_perbaikan';
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'id_laporan',
        'nomor_identitas',
        'pekerjaan',
        'jenis_kelamin',
        'status_KIPK',
        'alamat_asal',
    ];

    protected function casts(): array
    {
        return [
            'status_KIPK' => 'boolean',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function laporanKerusakan(): BelongsTo
    {
        return $this->belongsTo(LaporanKerusakan::class, 'id_laporan', 'id_laporan');
    }
}
