<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PenempatanKamar extends Model
{
    use HasFactory;

    protected $table = 'penempatan_kamar';
    protected $primaryKey = 'id_penempatan';
    public $timestamps = false;

    protected $fillable = [
        'id_pendaftaran',
        'id_kamar',
        'id_admin',
        'tanggal_masuk',
        'tanggal_keluar',
        'status_penempatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
        ];
    }

    public function pendaftaranHunian(): BelongsTo
    {
        return $this->belongsTo(PendaftaranHunian::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin', 'id_pengguna');
    }

    public function pengajuanCheckout(): HasOne
    {
        return $this->hasOne(PengajuanCheckout::class, 'id_penempatan', 'id_penempatan');
    }
}
