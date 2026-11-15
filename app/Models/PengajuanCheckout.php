<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanCheckout extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_checkout';
    protected $primaryKey = 'id_pengajuan';
    public $timestamps = false;

    protected $fillable = [
        'id_penempatan',
        'id_fasilitator',
        'tanggal_pengajuan',
        'tanggal_rencanaKeluar',
        'alasan',
        'status_pengajuan',
        'tangal_verifikasi',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_rencanaKeluar' => 'date',
            'tangal_verifikasi' => 'date',
        ];
    }

    public function penempatanKamar(): BelongsTo
    {
        return $this->belongsTo(PenempatanKamar::class, 'id_penempatan', 'id_penempatan');
    }

    public function fasilitator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_fasilitator', 'id_pengguna');
    }

    public function pengecekan(): HasOne
    {
        return $this->hasOne(Pengecekan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function suratCheckout(): HasOne
    {
        return $this->hasOne(SuratCheckout::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function suratBebasAsrama(): HasOne
    {
        return $this->hasOne(SuratBebasAsrama::class, 'id_pengajuan', 'id_pengajuan');
    }
}
