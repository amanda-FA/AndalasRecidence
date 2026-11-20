<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'berkas_pendaftaran';
    protected $primaryKey = 'id_berkas';
    public $timestamps = false;

    protected $fillable = [
        'id_pendaftaran',
        'id_jenis_berkas',
        'path_file',
        'tanggal_upload',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'date',
        ];
    }

    public function pendaftaranHunian(): BelongsTo
    {
        return $this->belongsTo(PendaftaranHunian::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function jenisBerkas(): BelongsTo
    {
        return $this->belongsTo(JenisBerkas::class, 'id_jenis_berkas', 'id_jenis_berkas');
    }
}
