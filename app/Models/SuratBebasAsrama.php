<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratBebasAsrama extends Model
{
    use HasFactory;

    protected $table = 'surat_bebas_asrama';
    protected $primaryKey = 'id_surat_bebas_asrama';
    public $timestamps = false;

    protected $fillable = [
        'id_pengajuan',
        'jenis_surat',
        'nomor_surat',
        'tanggal_terbit',
        'file_surat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
        ];
    }

    public function pengajuanCheckout(): BelongsTo
    {
        return $this->belongsTo(PengajuanCheckout::class, 'id_pengajuan', 'id_pengajuan');
    }
}
