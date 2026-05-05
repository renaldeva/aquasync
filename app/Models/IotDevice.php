<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IotDevice extends Model {
    protected $table = 'iot_devices';
    protected $fillable = ['device_id', 'nama_device', 'kolam_id', 'tipe', 'status', 'last_ping', 'firmware_version', 'ip_address', 'mqtt_topic', 'konfigurasi'];
    protected $casts = ['last_ping' => 'datetime', 'konfigurasi' => 'array'];
 
    public function kolam() { return $this->belongsTo(Kolam::class); }
 
    public function isOnline(): bool { return $this->status === 'online'; }
 
    public function updateLastPing(): void {
        $this->update(['last_ping' => now(), 'status' => 'online']);
    }
}
