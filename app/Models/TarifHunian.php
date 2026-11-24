<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifHunian extends Model
{
    protected $table = 'tarif_hunian';

    protected $primaryKey = 'id_tarif';

    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'id_tipeKamar',
        'kategori_tarif',
        'satuan_tarif',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function gedung()
    {
        return $this->belongsTo(
            Gedung::class,
            'id_gedung',
            'id_gedung'
        );
    }

    public function tipeKamar()
    {
        return $this->belongsTo(
            TipeKamar::class,
            'id_tipeKamar',
            'id_tipeKamar'
        );
    }
}