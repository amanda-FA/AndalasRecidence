<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanCheckout extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN = 'diajukan';
    public const STATUS_AUDIT_GO = 'audit_go';
    public const STATUS_MENUNGGU_PERSETUJUAN = 'menunggu_persetujuan';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    protected $table = 'pengajuan_checkout';
    protected $primaryKey = 'id_pengajuan';
    public $timestamps = false;

    protected $fillable = [
        'id_penempatan',
        'id_fasilitator',
        'tanggal_pengajuan',
        'tanggal_rencanaKeluar',
        'alasan',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'nominal_deposit',
        'status_deposit',
        'denda_kerusakan',
        'status_pengajuan',
        'tangal_verifikasi',
        'catatan',
        'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_rencanaKeluar' => 'date',
            'tangal_verifikasi' => 'date',
            'nominal_deposit' => 'decimal:2',
            'denda_kerusakan' => 'decimal:2',
        ];
    }

    public function isApproved(): bool
    {
        return in_array($this->status_pengajuan, [self::STATUS_DISETUJUI, 'selesai'], true);
    }

    public function isRejected(): bool
    {
        return $this->status_pengajuan === self::STATUS_DITOLAK;
    }

    public function penempatanKamar(): BelongsTo
    {
        return $this->belongsTo(PenempatanKamar::class, 'id_penempatan', 'id_penempatan');
    }

    public function fasilitator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_fasilitator', 'id_pengguna');
    }

    public function pengecekan(): HasOne
    {
        return $this->hasOne(Pengecekan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function suratCheckout(): HasOne
    {
        return $this->hasOne(SuratCheckout::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function suratBebasAsrama(): HasOne
    {
        return $this->hasOne(SuratBebasAsrama::class, 'id_pengajuan', 'id_pengajuan');
    }
}
