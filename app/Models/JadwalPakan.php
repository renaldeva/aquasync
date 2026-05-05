<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class JadwalPakan extends Model
{
    protected $table    = 'jadwal_pakan';
    protected $fillable = ['kolam_id','nama_jadwal','waktu_pakan','jumlah_pakan','satuan','jenis_pakan','frekuensi','hari_aktif','status','flag_status','created_by'];
    protected $casts    = ['hari_aktif' => 'array'];
 
    public function kolam()   { return $this->belongsTo(Kolam::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function riwayat() { return $this->hasMany(RiwayatPakan::class); }
}