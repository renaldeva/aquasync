<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class HasilPanen extends Model
{
    protected $table    = 'hasil_panen';
    protected $fillable = ['jadwal_panen_id','kolam_id','tanggal_panen','total_berat','total_jumlah','rata_rata_berat','harga_per_kg','total_nilai','kualitas','catatan','created_by'];
    protected $casts    = ['tanggal_panen' => 'date'];
 
    public function kolam()      { return $this->belongsTo(Kolam::class); }
    public function jadwalPanen(){ return $this->belongsTo(Panen::class, 'jadwal_panen_id'); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
}