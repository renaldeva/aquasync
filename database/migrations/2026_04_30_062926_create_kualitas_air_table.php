<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kualitas_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->decimal('ph_value', 4, 2);
            $table->decimal('turbidity_value', 8, 2)->nullable();
            $table->decimal('water_level', 8, 2)->nullable();
            $table->enum('status_ph', ['normal', 'asam', 'basa', 'kritis'])->default('normal');
            $table->enum('status_turbidity', ['jernih', 'keruh', 'sangat_keruh'])->default('jernih');
            $table->string('device_id', 50)->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
 
            $table->index(['kolam_id', 'recorded_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('kualitas_air'); }
};