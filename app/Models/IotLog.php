<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IotLog extends Model
{
    use HasFactory;

    protected $table = 'iot_logs';

    protected $fillable = [
        'device_id',
        'kolam_id',
        'topik',
        'payload',
        'tipe',
        'diterima_pada',
    ];

    protected $casts = [
        'payload'      => 'array',
        'diterima_pada'=> 'datetime',
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}
