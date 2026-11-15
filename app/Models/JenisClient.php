<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisClient extends Model
{
    use HasFactory;

    protected $table = 'jenis_client';
    protected $primaryKey = 'id_jenis_client';
    public $timestamps = false;

    protected $fillable = [
        'jenis',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'id_jenis_client', 'id_jenis_client');
    }
}
