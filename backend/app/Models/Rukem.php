<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rukem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_almarhum',
        'warga_id',
        'tanggal_meninggal',
        'jam_meninggal',
        'nama_ahli_waris',
        'no_hp_ahli_waris',
        'alamat_duka',
        'rt',
        'rw',
        'lokasi_pemakaman',
        'jam_pemakaman',
        'jumlah_santunan',
        'status_santunan',
        'tanggal_penyerahan_santunan',
        'petugas_penyerah',
        'fasilitas_disiapkan',
    ];

    protected $casts = [
        'tanggal_meninggal' => 'date',
        'tanggal_penyerahan_santunan' => 'date',
        'jumlah_santunan' => 'decimal:2',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
