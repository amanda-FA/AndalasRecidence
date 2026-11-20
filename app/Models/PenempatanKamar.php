<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PenempatanKamar extends Model
{
    use HasFactory;

    protected $table = 'penempatan_kamar';
    protected $primaryKey = 'id_penempatan';
    public $timestamps = false;

    protected $fillable = [
        'id_pendaftaran',
        'id_kamar',
        'id_admin',
        'tanggal_masuk',
        'tanggal_keluar',
        'status_penempatan',
        'nomor_bed', 'rencana_keluar', 'jadwal_kunci_mulai', 'jadwal_kunci_selesai', 'lokasi_kunci', 'penanggung_jawab', 'kontak_penanggung_jawab',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
            'nomor_bed' => 'integer', 'rencana_keluar' => 'date',
            'jadwal_kunci_mulai' => 'datetime', 'jadwal_kunci_selesai' => 'datetime',
        ];
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('pendaftaranHunian.client', fn (Builder $client) => $client->where('id_pengguna', $userId));
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('status_penempatan', 'aktif')->whereNull('tanggal_keluar')
            ->where(fn ($q) => $q->whereNull('rencana_keluar')->orWhereDate('rencana_keluar', '>=', today('Asia/Jakarta')->toDateString()))
            ->whereDate('tanggal_masuk', '<=', now('Asia/Jakarta')->toDateString())
            ->orderByDesc('tanggal_masuk')->orderByDesc('id_penempatan');
    }

    public function pendaftaranHunian(): BelongsTo
    {
        return $this->belongsTo(PendaftaranHunian::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin', 'id_pengguna');
    }

    public function pengajuanCheckout(): HasOne
    {
        return $this->hasOne(PengajuanCheckout::class, 'id_penempatan', 'id_penempatan');
    }
}
