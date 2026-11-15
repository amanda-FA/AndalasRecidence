<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;

    protected $fillable = [
        'id_role',
        'nama_lengkap',
        'email',
        'no_hp',
        'username',
        'password',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id_pengguna', 'id_pengguna');
    }

    public function laporanKerusakans(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_pengguna', 'id_pengguna');
    }

    public function penugasanPerbaikans(): HasMany
    {
        return $this->hasMany(PenugasanPerbaikan::class, 'id_pengguna', 'id_pengguna');
    }

    public function pengecekans(): HasMany
    {
        return $this->hasMany(Pengecekan::class, 'id_pengguna', 'id_pengguna');
    }

    public function hasRole(string|array $roles): bool
    {
        $roleList = (array) $roles;
        return in_array($this->role?->nama_role, $roleList, true);
    }

    public function hasAnyRole(string|array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function assignRole(string|Role $role): static
    {
        if (is_string($role)) {
            $roleModel = Role::where('nama_role', $role)->first();
            if ($roleModel) {
                $this->id_role = $roleModel->id_role;
                $this->save();
            }
        } elseif ($role instanceof Role) {
            $this->id_role = $role->id_role;
            $this->save();
        }

        return $this;
    }

    public function syncRoles(array $roles): static
    {
        $firstRole = reset($roles);
        if ($firstRole) {
            $this->assignRole($firstRole);
        }

        return $this;
    }

    // Accessor to provide ->name compatibility with Laravel defaults
    public function getNameAttribute(): string
    {
        return $this->nama_lengkap ?? '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // No-op: kolom remember_token tidak ada pada skema ERD tabel pengguna
    }

    public function getRememberTokenName(): string
    {
        return '';
    }
}
