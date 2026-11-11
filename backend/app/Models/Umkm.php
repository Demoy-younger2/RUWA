<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_usaha',
        'nama_pemilik',
        'kategori',
        'deskripsi',
        'harga_mulai',
        'no_whatsapp',
        'alamat',
        'rt',
        'rw',
        'foto_produk',
        'status',
    ];

    protected $casts = [
        'harga_mulai' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
