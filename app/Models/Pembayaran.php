<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'id_pendaftaran',
        'jumlah',
        'metode_bayar',
        'bukti_bayar',
        'tanggal_bayar',
        'status_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_bayar' => 'date',
        ];
    }

    public function pendaftaranHunian(): BelongsTo
    {
        return $this->belongsTo(PendaftaranHunian::class, 'id_pendaftaran', 'id_pendaftaran');
    }
}
