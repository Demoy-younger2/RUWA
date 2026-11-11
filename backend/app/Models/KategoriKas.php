<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKas extends Model
{
    use HasFactory;

    protected $table = 'kategori_kas';

    protected $fillable = [
        'nama',
        'jenis',
        'tingkat',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kasList()
    {
        return $this->hasMany(Kas::class, 'kategori', 'nama');
    }
}
