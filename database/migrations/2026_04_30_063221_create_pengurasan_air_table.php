<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengurasan_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();
            $table->integer('durasi_menit')->nullable();
            $table->decimal('volume_liter', 10, 2)->nullable();
            $table->enum('trigger_type', ['otomatis', 'manual', 'terjadwal'])->default('otomatis');
            $table->enum('penyebab', ['turbidity_tinggi', 'ph_tidak_normal', 'terjadwal', 'manual'])->nullable();
            $table->enum('status', ['berlangsung', 'selesai', 'gagal'])->default('berlangsung');
            $table->text('keterangan')->nullable();
            $table->string('device_id', 50)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pengurasan_air'); }
};