<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'user_id',
        'warga_id',
        'jenis_surat',
        'keperluan',
        'keterangan_tambahan',
        'lampiran',
        'status',
        'catatan_rt',
        'catatan_rw',
        'disetujui_rt_oleh',
        'disetujui_rw_oleh',
        'tanggal_diajukan',
        'tanggal_disetujui_rt',
        'tanggal_disetujui_rw',
    ];

    protected $casts = [
        'tanggal_diajukan' => 'datetime',
        'tanggal_disetujui_rt' => 'datetime',
        'tanggal_disetujui_rw' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'disetujui_rt' => 'bg-blue-100 text-blue-800 border-blue-300',
            'disetujui_rw', 'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Menunggu Persetujuan RT',
            'disetujui_rt' => 'Disetujui RT (Menunggu RW)',
            'disetujui_rw', 'selesai' => 'Selesai / Terbit',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }
}
