<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'harga_per_kg',
        'satuan',
        'deskripsi',
    ];

    public function transaksis()
    {
        return $this->hasMany(TransaksiBankSampah::class);
    }
}
