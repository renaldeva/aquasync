<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class RiwayatPakan extends Model
{
    protected $table      = 'riwayat_pakan';
    public    $timestamps = false;
    protected $fillable   = ['jadwal_pakan_id','kolam_id','waktu_eksekusi','jumlah_aktual','status','keterangan'];
    protected $casts      = ['waktu_eksekusi' => 'datetime'];
 
    public function jadwalPakan() { return $this->belongsTo(JadwalPakan::class); }
    public function kolam()       { return $this->belongsTo(Kolam::class); }
}