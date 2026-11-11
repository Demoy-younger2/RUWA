<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'modul',
        'tindakan',
        'deskripsi',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat aktivitas baru ke log audit sistem
     */
    public static function record(string $modul, string $tindakan, string $deskripsi, $user = null, array $properties = []): self
    {
        $currentUser = $user ?: Auth::user();

        return self::create([
            'user_id' => $currentUser ? $currentUser->id : null,
            'user_name' => $currentUser ? $currentUser->name : 'Sistem / Anonim',
            'user_role' => $currentUser ? $currentUser->role : 'tamu',
            'modul' => $modul,
            'tindakan' => $tindakan,
            'deskripsi' => $deskripsi,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent() ?? 'Browser',
            'properties' => !empty($properties) ? $properties : null,
        ]);
    }

    /**
     * Helper icon FontAwesome per modul
     */
    public function getIconAttribute(): string
    {
        return match (strtolower($this->modul)) {
            'auth' => 'fa-solid fa-key text-blue-500',
            'warga' => 'fa-solid fa-users text-emerald-500',
            'surat' => 'fa-solid fa-file-signature text-indigo-500',
            'kas' => 'fa-solid fa-wallet text-amber-500',
            'ronda' => 'fa-solid fa-shield-halved text-rose-500',
            'bank sampah' => 'fa-solid fa-recycle text-teal-500',
            'umkm' => 'fa-solid fa-store text-violet-500',
            'posyandu' => 'fa-solid fa-heart-pulse text-rose-400',
            'kegiatan' => 'fa-solid fa-calendar-check text-sky-500',
            'rukem' => 'fa-solid fa-hands-holding-child text-slate-600',
            'aspirasi' => 'fa-solid fa-comments text-orange-500',
            'aset' => 'fa-solid fa-boxes-stacked text-teal-600',
            'koperasi' => 'fa-solid fa-hand-holding-dollar text-emerald-600',
            'pengaturan' => 'fa-solid fa-gear text-slate-700',
            default => 'fa-solid fa-circle-info text-slate-400',
        };
    }

    /**
     * Helper badge color per tindakan
     */
    public function getActionBadgeAttribute(): array
    {
        return match (strtolower($this->tindakan)) {
            'login', 'masuk' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-right-to-bracket'],
            'tambah', 'buat', 'pengajuan' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => 'fa-plus'],
            'verifikasi', 'persetujuan', 'disetujui' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'icon' => 'fa-check-double'],
            'ubah', 'edit', 'update' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'icon' => 'fa-pen'],
            'hapus', 'tolak', 'ditolak' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'icon' => 'fa-ban'],
            'darurat sos', 'panic' => ['bg' => 'bg-rose-600', 'text' => 'text-white font-black animate-pulse', 'icon' => 'fa-triangle-exclamation'],
            'transaksi', 'setor', 'tarik' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'icon' => 'fa-money-bill-transfer'],
            default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => 'fa-circle-dot'],
        };
    }
}
