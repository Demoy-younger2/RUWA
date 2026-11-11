<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'nama_peserta',
        'nik',
        'warga_id',
        'tanggal_pemeriksaan',
        'usia_bulan_tahun',
        'berat_badan',
        'tinggi_badan',
        'tensi_darah',
        'gula_darah',
        'status_kesehatan',
        'imunisasi_atau_vitamin',
        'catatan_kader',
        'nama_kader',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'gula_darah' => 'decimal:2',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
