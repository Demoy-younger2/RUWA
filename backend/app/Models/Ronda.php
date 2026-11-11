<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ronda extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt',
        'rw',
        'hari',
        'tanggal',
        'nama_petugas',
        'user_id',
        'pos_ronda',
        'jam_jaga',
        'status_kehadiran',
        'catatan_patroli',
        'laporan_kejadian',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
