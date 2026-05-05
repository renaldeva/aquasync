<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MqttLog extends Model {
    protected $table = 'mqtt_logs';
    public $timestamps = false;
    protected $fillable = ['device_id', 'topic', 'payload', 'qos', 'direction', 'processed', 'error_message'];
    protected $casts = ['payload' => 'array', 'processed' => 'boolean', 'created_at' => 'datetime'];
}
