<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoperasiAnggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_anggota',
        'tanggal_daftar',
        'total_simpanan',
        'total_pinjaman',
        'status',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'total_simpanan' => 'decimal:2',
        'total_pinjaman' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksis()
    {
        return $this->hasMany(KoperasiTransaksi::class);
    }
}
