<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fasilitas extends Model
{
    use HasFactory;

    public const DEFAULT_CHOICES = [
        'gedung' => [
            'Dapur / Pantry', 'Musholla Terpadu', 'Ruang Belajar / Cowork', 'Parkir Berkanopi',
            'CCTV 24 Jam Aktif', 'WiFi Eduroam / IdREN', 'Sistem Hydrant & Tanggap Darurat',
        ],
        'kamar' => [
            'AC Pendingin Ruangan', 'Kamar Mandi Dalam', 'Pemanas Air Listrik', 'Meja & Kursi Belajar',
            'Lemari Pakaian 2 Pintu', 'Kasur Springbed Single', 'WiFi Eduroam High-Speed', 'Balkon Pribadi',
        ],
    ];

    protected $table = 'fasilitas';
    protected $primaryKey = 'id_fasilitas';
    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'id_kamar',
        'nama_fasilitas',
        'kondisi',
        'id_fasilitas_acuan',
        'aktif',
        'lingkup',
    ];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function scopePilihan(Builder $query, ?string $scope = null): Builder
    {
        $query->whereNull('id_gedung')->whereNull('id_kamar')->whereNull('id_fasilitas_acuan')->where('aktif', true);
        return $scope === null ? $query : $query->where('lingkup', $scope);
    }

    public function acuan(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_fasilitas_acuan', 'id_fasilitas');
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function laporanKerusakans(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function detailPengecekans(): HasMany
    {
        return $this->hasMany(DetailPengecekan::class, 'id_fasilitas', 'id_fasilitas');
    }
}
