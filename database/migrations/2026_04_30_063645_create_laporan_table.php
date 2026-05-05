<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->enum('tipe', ['kualitas_air', 'pakan', 'pengurasan', 'panen', 'bulanan', 'mingguan']);
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->foreignId('kolam_id')->nullable()->constrained('kolam')->nullOnDelete();
            $table->json('konten')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->enum('status', ['draft', 'published', 'approved', 'rejected'])->default('draft');
            $table->enum('flag_status', ['none', 'flagged', 'approved', 'rejected'])->default('none');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('laporan'); }
};