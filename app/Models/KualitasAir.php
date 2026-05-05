<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class KualitasAir extends Model
{
    protected $table      = 'kualitas_air';
    public    $timestamps = false;
    protected $fillable   = ['kolam_id','ph_value','turbidity_value','water_level','status_ph','status_turbidity','device_id','recorded_at'];
    protected $casts      = ['recorded_at' => 'datetime', 'created_at' => 'datetime'];
 
    public function kolam() { return $this->belongsTo(Kolam::class); }
 
    public static function determinePHStatus(float $ph): string
    {
        if ($ph < 5.0 || $ph > 9.0) return 'kritis';
        if ($ph < 6.5)               return 'asam';
        if ($ph > 8.5)               return 'basa';
        return 'normal';
    }
}