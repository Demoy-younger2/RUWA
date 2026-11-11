<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanWarga extends Model
{
    use HasFactory;

    protected $table = 'tagihan_wargas';

    protected $fillable = [
        'kode_tagihan',
        'judul',
        'tingkat',
        'rt',
        'user_id',
        'nama_warga',
        'nik',
        'nomor_rumah',
        'periode',
        'nominal',
        'kategori',
        'status',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'metode_pembayaran',
        'rekening_id',
        'bukti_bayar',
        'catatan',
        'diverifikasi_oleh',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rekening()
    {
        return $this->belongsTo(RekeningKas::class, 'rekening_id');
    }

    public function kas()
    {
        return $this->hasOne(Kas::class, 'tagihan_id');
    }
}
