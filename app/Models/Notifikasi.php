<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Notifikasi extends Model
{
    protected $table      = 'notifikasi';
    public    $timestamps = false;
    protected $fillable   = ['user_id','judul','pesan','tipe','referensi_type','referensi_id','is_read','read_at'];
    protected $casts      = ['is_read' => 'boolean', 'read_at' => 'datetime', 'created_at' => 'datetime'];
 
    public function user() { return $this->belongsTo(User::class); }
 
    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }
 
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $refType = null, ?int $refId = null): self
    {
        return self::create([
            'user_id'        => $userId,
            'judul'          => $judul,
            'pesan'          => $pesan,
            'tipe'           => $tipe,
            'referensi_type' => $refType,
            'referensi_id'   => $refId,
        ]);
    }
}