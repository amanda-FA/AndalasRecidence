<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodePendaftaran extends Model
{
    use HasFactory;

    protected $table = 'periode_pendaftaran';
    protected $primaryKey = 'id_periode';
    public $timestamps = false;

    protected $fillable = [
        'nama_periode',
        'tahun_akademik',
        'tanggal_buka',
        'tanggal_tutup',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_buka' => 'date',
            'tanggal_tutup' => 'date',
        ];
    }

    public function pendaftaranHunians(): HasMany
    {
        return $this->hasMany(PendaftaranHunian::class, 'id_periode', 'id_periode');
    }
}
