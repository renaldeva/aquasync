<?php

namespace App\Models;
 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 
class User extends Authenticatable
{
    use Notifiable;
 
    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar', 'is_active'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['is_active' => 'boolean'];
 
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isOwner(): bool { return $this->role === 'owner'; }
 
    public function notifikasi()    { return $this->hasMany(Notifikasi::class); }
    public function komentarFlag()  { return $this->hasMany(KomentarFlag::class); }
}