<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Pengurasan extends Model
{
    protected $table    = 'pengurasan_air';
    protected $fillable = ['kolam_id','waktu_mulai','waktu_selesai','durasi_menit','volume_liter','trigger_type','penyebab','status','keterangan','device_id'];
    protected $casts    = ['waktu_mulai' => 'datetime', 'waktu_selesai' => 'datetime'];
 
    public function kolam() { return $this->belongsTo(Kolam::class); }
}