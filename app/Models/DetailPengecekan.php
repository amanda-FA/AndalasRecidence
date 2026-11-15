<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengecekan extends Model
{
    use HasFactory;

    protected $table = 'detail_pengecekan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_pengecekan',
        'id_fasilitas',
        'kondisi',
        'keterangan',
    ];

    public function pengecekan(): BelongsTo
    {
        return $this->belongsTo(Pengecekan::class, 'id_pengecekan', 'id_pengecekan');
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas', 'id_fasilitas');
    }
}
