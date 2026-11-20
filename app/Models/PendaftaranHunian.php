<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendaftaranHunian extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_hunian';
    protected $primaryKey = 'id_pendaftaran';
    public $timestamps = false;

    protected $fillable = [
        'id_client',
        'id_periode',
        'id_tipeKamar',
        'id_kamar',
        'id_admin',
        'tanggal_daftar',
        'status_pendaftaran',
        'tanggal_verifikasi',
        'catatan_verifikasi',
        'tanggal_masuk',
        'rencana_keluar',
        'jadwal_kunci_mulai',
        'jadwal_kunci_selesai',
        'lokasi_kunci',
        'penanggung_jawab',
        'kontak_penanggung_jawab',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'datetime',
            'tanggal_verifikasi' => 'datetime',
            'tanggal_masuk' => 'date',
            'rencana_keluar' => 'date',
            'jadwal_kunci_mulai' => 'datetime',
            'jadwal_kunci_selesai' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'id_periode', 'id_periode');
    }

    public function tipeKamar(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipeKamar', 'id_tipeKamar');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin', 'id_pengguna');
    }

    public function berkasPendaftarans(): HasMany
    {
        return $this->hasMany(BerkasPendaftaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function penempatanKamars(): HasMany
    {
        return $this->hasMany(PenempatanKamar::class, 'id_pendaftaran', 'id_pendaftaran');
    }
}
