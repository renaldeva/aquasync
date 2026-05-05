<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Laporan extends Model
{
    protected $table    = 'laporan';
    protected $fillable = ['judul','tipe','periode_mulai','periode_selesai','kolam_id','konten','file_path','status','flag_status','dibuat_oleh','disetujui_oleh','disetujui_at'];
    protected $casts    = ['periode_mulai' => 'date', 'periode_selesai' => 'date', 'konten' => 'array', 'disetujui_at' => 'datetime'];
 
    public function kolam()    { return $this->belongsTo(Kolam::class); }
    public function pembuat()  { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function penyetuju(){ return $this->belongsTo(User::class, 'disetujui_oleh'); }
}