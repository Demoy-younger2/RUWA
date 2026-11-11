<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'kategori',
        'tingkat',
        'rt',
        'rw',
        'jumlah_total',
        'jumlah_tersedia',
        'kondisi',
        'lokasi_penyimpanan',
        'keterangan',
    ];

    public function peminjamans()
    {
        return $this->hasMany(PeminjamanAset::class);
    }
}
