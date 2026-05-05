<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Panen extends Model
{
    protected $table    = 'jadwal_panen';
    protected $fillable = ['kolam_id','tanggal_rencana','estimasi_berat','estimasi_jumlah','catatan','status','flag_status','created_by','approved_by','approved_at'];
    protected $casts    = ['tanggal_rencana' => 'date', 'approved_at' => 'datetime'];
 
    public function kolam()        { return $this->belongsTo(Kolam::class); }
    public function creator()      { return $this->belongsTo(User::class, 'created_by'); }
    public function approver()     { return $this->belongsTo(User::class, 'approved_by'); }
    public function hasilPanen()   { return $this->hasOne(HasilPanen::class, 'jadwal_panen_id'); }
    public function komentarFlag() { return $this->hasMany(KomentarFlag::class, 'target_id')->where('target_type','jadwal_panen'); }
}