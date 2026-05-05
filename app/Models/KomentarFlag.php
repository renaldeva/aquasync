<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class KomentarFlag extends Model
{
    protected $table    = 'komentar_flag';
    protected $fillable = ['user_id','target_type','target_id','jenis','isi_komentar','status','dibalas_oleh','isi_balasan','dibalas_at'];
    protected $casts    = ['dibalas_at' => 'datetime'];
 
    public function user()     { return $this->belongsTo(User::class); }
    public function penjawab() { return $this->belongsTo(User::class, 'dibalas_oleh'); }
}