<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBankSampah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'bank_sampah_id',
        'jenis_transaksi',
        'berat',
        'total_rupiah',
        'tanggal',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berat' => 'decimal:2',
        'total_rupiah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankSampah()
    {
        return $this->belongsTo(BankSampah::class);
    }
}
