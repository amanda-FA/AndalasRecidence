<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengecekan extends Model
{
    use HasFactory;

    protected $table = 'pengecekan';
    protected $primaryKey = 'id_pengecekan';
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'id_pengajuan',
        'tanggal_cek',
        'kondisi_kamar',
        'catatan',
        'status_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_cek' => 'date',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function pengajuanCheckout(): BelongsTo
    {
        return $this->belongsTo(PengajuanCheckout::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function detailPengecekans(): HasMany
    {
        return $this->hasMany(DetailPengecekan::class, 'id_pengecekan', 'id_pengecekan');
    }
}
