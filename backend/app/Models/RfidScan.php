<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rfid_uid',
        'device_id',
        'status',
        'action',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
