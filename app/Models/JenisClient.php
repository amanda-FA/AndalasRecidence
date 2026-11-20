<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisClient extends Model
{
    public const CATEGORIES = [
        'kipk' => 'Maba Lokal KIP-K',
        'reguler' => 'Maba Lokal NON KIP-K',
        's1_non_maba' => 'Mahasiswa S1 Non Maba',
        'international_private' => 'Internasional/S2/S3 pribadi',
        'international_funded' => 'Internasional/S2/S3 ditanggung',
        'guest' => 'Non Mahasiswa (UMUM)',
        'summer_course' => 'Summer Course',
    ];

    public const ADMIN_PLACEMENT_CATEGORIES = ['kipk', 'international_funded', 'summer_course'];

    use HasFactory;

    protected $table = 'jenis_client';
    protected $primaryKey = 'id_jenis_client';
    public $timestamps = false;

    protected $fillable = [
        'jenis',
    ];

    public function requiresAdminPlacement(): bool
    {
        return in_array(array_search($this->jenis, self::CATEGORIES, true), self::ADMIN_PLACEMENT_CATEGORIES, true);
    }

    public function canChooseRoom(): bool
    {
        return in_array($this->jenis, self::CATEGORIES, true) && ! $this->requiresAdminPlacement();
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'id_jenis_client', 'id_jenis_client');
    }
}
