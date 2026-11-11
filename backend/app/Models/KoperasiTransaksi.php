<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoperasiTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'koperasi_anggota_id',
        'jenis',
        'nominal',
        'tanggal',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function anggota()
    {
        return $this->belongsTo(KoperasiAnggota::class, 'koperasi_anggota_id');
    }
}
