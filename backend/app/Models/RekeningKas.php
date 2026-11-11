<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekeningKas extends Model
{
    use HasFactory;

    protected $table = 'rekening_kas';

    protected $fillable = [
        'tingkat',
        'rt',
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'tipe',
        'qris_image',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function tagihanList()
    {
        return $this->hasMany(TagihanWarga::class, 'rekening_id');
    }
}
