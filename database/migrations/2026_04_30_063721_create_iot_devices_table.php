<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 50)->unique();
            $table->string('nama_device', 100);
            $table->foreignId('kolam_id')->nullable()->constrained('kolam')->nullOnDelete();
            $table->enum('tipe', ['sensor_ph', 'sensor_turbidity', 'sensor_ultrasonic', 'aktuator_pakan', 'aktuator_pengurasan', 'esp32']);
            $table->enum('status', ['online', 'offline', 'error', 'maintenance'])->default('offline');
            $table->timestamp('last_ping')->nullable();
            $table->string('firmware_version', 20)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('mqtt_topic', 150)->nullable();
            $table->json('konfigurasi')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('iot_devices'); }
};