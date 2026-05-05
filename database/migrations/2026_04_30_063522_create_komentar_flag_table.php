<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('komentar_flag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('target_type', ['jadwal_pakan', 'jadwal_panen', 'pengurasan_air', 'kolam', 'laporan']);
            $table->unsignedBigInteger('target_id');
            $table->enum('jenis', ['komentar', 'flag', 'approval']);
            $table->text('isi_komentar');
            $table->enum('status', ['menunggu', 'dibalas', 'selesai', 'ditolak'])->default('menunggu');
            $table->foreignId('dibalas_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('isi_balasan')->nullable();
            $table->timestamp('dibalas_at')->nullable();
            $table->timestamps();
 
            $table->index(['target_type', 'target_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('komentar_flag'); }
};