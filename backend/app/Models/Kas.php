<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';

    protected $fillable = [
        'tingkat',
        'rt',
        'rw',
        'user_id',
        'kategori',
        'jenis',
        'jumlah',
        'tanggal',
        'keterangan',
        'pihak_terkait',
        'metode',
        'tagihan_id',
        'bukti_transaksi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanWarga::class, 'tagihan_id');
    }
}
