<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hasil_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_panen_id')->nullable()->constrained('jadwal_panen')->nullOnDelete();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->date('tanggal_panen');
            $table->decimal('total_berat', 10, 2);
            $table->integer('total_jumlah')->nullable();
            $table->decimal('rata_rata_berat', 8, 2)->nullable();
            $table->decimal('harga_per_kg', 12, 2)->nullable();
            $table->decimal('total_nilai', 15, 2)->nullable();
            $table->enum('kualitas', ['baik', 'sedang', 'buruk'])->default('baik');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('hasil_panen'); }
};