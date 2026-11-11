<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_aplikasi',
        'nama_rw',
        'nama_kelurahan',
        'nama_kecamatan',
        'nama_kota',
        'kode_pos',
        'nama_ketua_rw',
        'sambutan_rw',
        'kontak_rw',
        'email_rw',
        'alamat_sekretariat',
    ];
}
