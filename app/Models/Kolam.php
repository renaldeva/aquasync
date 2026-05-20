<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kolam extends Model
{
    protected $table = 'kolam';

    protected $fillable = [
        'kode_kolam',
        'nama_kolam',
        'jenis',
        'kapasitas_liter',
        'jumlah_ikan',
        'tanggal_tebar',
        'status',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'tanggal_tebar' => 'date'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function kualitasAir()
    {
        return $this->hasMany(KualitasAir::class);
    }

    public function latestKualitasAir()
    {
        return $this->hasOne(KualitasAir::class)
            ->latestOfMany('recorded_at');
    }

    public function jadwalPakan()
    {
        return $this->hasMany(JadwalPakan::class);
    }

    public function pengurasan()
    {
        return $this->hasMany(Pengurasan::class);
    }

    public function panen()
    {
        return $this->hasMany(Panen::class);
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DEVICE IOT
    |--------------------------------------------------------------------------
    */
    public function iotDevice()
    {
        return $this->hasOne(
            IotDevice::class,
            'kolam_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS BADGE
    |--------------------------------------------------------------------------
    */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->latestKualitasAir?->status_ph) {

            'normal' => 'aman',

            'asam',
            'basa' => 'perlu_perhatian',

            'kritis' => 'kritis',

            default => 'tidak_ada_data',
        };
    }
}