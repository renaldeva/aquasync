<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Notifikasi extends Model
{
    protected $table      = 'notifikasi';
    public    $timestamps = false;                // kolom hanya created_at, tidak ada updated_at
    protected $fillable   = ['user_id','judul','pesan','tipe','referensi_type','referensi_id','is_read','read_at','created_at'];
    protected $casts      = ['is_read' => 'boolean', 'read_at' => 'datetime', 'created_at' => 'datetime'];
 
    public function user() { return $this->belongsTo(User::class); }
 
    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }
 
    /**
     * Kirim notifikasi ke satu user
     */
    public static function kirim(
        int     $userId,
        string  $judul,
        string  $pesan,
        string  $tipe    = 'info',
        ?string $refType = null,
        ?int    $refId   = null
    ): self {
        return self::create([
            'user_id'        => $userId,
            'judul'          => $judul,
            'pesan'          => $pesan,
            'tipe'           => $tipe,
            'referensi_type' => $refType,
            'referensi_id'   => $refId,
            'is_read'        => false,
            'created_at'     => now(),
        ]);
    }
 
    /**
     * Kirim notifikasi ke semua user dengan role tertentu
     */
    public static function kirimKeRole(
        string  $role,
        string  $judul,
        string  $pesan,
        string  $tipe    = 'info',
        ?string $refType = null,
        ?int    $refId   = null
    ): void {
        User::where('role', $role)->each(
            fn($user) => self::kirim($user->id, $judul, $pesan, $tipe, $refType, $refId)
        );
    }
}