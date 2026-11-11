<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nik',
        'phone',
        'rt',
        'rw',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isRW(): bool
    {
        return $this->role === 'rw';
    }

    public function isRT(): bool
    {
        return $this->role === 'rt';
    }

    public function isSekretarisRW(): bool
    {
        return $this->role === 'sekretaris_rw';
    }

    public function isBendaharaRW(): bool
    {
        return $this->role === 'bendahara_rw';
    }

    public function isSekretarisRT(): bool
    {
        return $this->role === 'sekretaris_rt';
    }

    public function isBendaharaRT(): bool
    {
        return $this->role === 'bendahara_rt';
    }

    public function isSekretaris(): bool
    {
        return in_array($this->role, ['sekretaris_rw', 'sekretaris_rt']);
    }

    public function isBendahara(): bool
    {
        return in_array($this->role, ['bendahara_rw', 'bendahara_rt']);
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    public function hasRole(array|string $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($this->role, $roles);
    }

    public function getRoleTitleAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            'rw' => 'Ketua RW',
            'rt' => 'Ketua RT ' . ($this->rt ?? '01'),
            'sekretaris_rw' => 'Sekretaris RW',
            'bendahara_rw' => 'Bendahara RW',
            'sekretaris_rt' => 'Sekretaris RT ' . ($this->rt ?? '01'),
            'bendahara_rt' => 'Bendahara RT ' . ($this->rt ?? '01'),
            'warga' => 'Warga RT ' . ($this->rt ?? '01'),
            default => ucfirst($this->role),
        };
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300',
            'rw' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300',
            'rt' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
            'sekretaris_rw', 'sekretaris_rt' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
            'bendahara_rw', 'bendahara_rt' => 'bg-teal-100 text-teal-800 border-teal-300 dark:bg-teal-900/40 dark:text-teal-300',
            'warga' => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    // Relationships
    public function warga()
    {
        return $this->hasOne(Warga::class);
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    public function aspirasis()
    {
        return $this->hasMany(Aspirasi::class);
    }

    public function umkms()
    {
        return $this->hasMany(Umkm::class);
    }

    public function peminjamanAsets()
    {
        return $this->hasMany(PeminjamanAset::class);
    }

    public function koperasiAnggota()
    {
        return $this->hasOne(KoperasiAnggota::class);
    }

    public function transaksiBankSampahs()
    {
        return $this->hasMany(TransaksiBankSampah::class);
    }
}
