<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'client';
    protected $primaryKey = 'id_client';
    public $timestamps = false;

    protected $fillable = [
        'id_jenis_client',
        'id_pengguna',
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

    public function jenisClient(): BelongsTo
    {
        return $this->belongsTo(JenisClient::class, 'id_jenis_client', 'id_jenis_client');
    }

    public function requiresAdminPlacement(): bool
    {
        return $this->jenisClient?->requiresAdminPlacement() ?? false;
    }

    public function canChooseRoom(): bool
    {
        return $this->jenisClient?->canChooseRoom() ?? false;
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function pendaftaranHunians(): HasMany
    {
        return $this->hasMany(PendaftaranHunian::class, 'id_client', 'id_client');
    }
}
