<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBerkas extends Model
{
    use HasFactory;

    protected $table = 'jenis_berkas';
    protected $primaryKey = 'id_jenis_berkas';
    public $timestamps = false;

    protected $fillable = ['nama_berkas'];

    public function berkasPendaftarans(): HasMany
    {
        return $this->hasMany(BerkasPendaftaran::class, 'id_jenis_berkas', 'id_jenis_berkas');
    }
}
