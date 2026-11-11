<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kategori',
        'judul',
        'isi',
        'lokasi',
        'foto',
        'status',
        'tanggapan',
        'ditanggapi_oleh',
        'is_anonim',
    ];

    protected $casts = [
        'is_anonim' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
