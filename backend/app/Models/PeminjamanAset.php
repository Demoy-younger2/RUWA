<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanAset extends Model
{
    use HasFactory;

    protected $fillable = [
        'aset_id',
        'user_id',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'keperluan',
        'status',
        'disetujui_oleh',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
