<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->string('nama_jadwal', 100)->nullable();
            $table->time('waktu_pakan');
            $table->decimal('jumlah_pakan', 8, 2)->nullable();
            $table->string('satuan', 20)->default('gram');
            $table->string('jenis_pakan', 50)->nullable();
            $table->enum('frekuensi', ['harian', 'mingguan', 'custom'])->default('harian');
            $table->json('hari_aktif')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'pending'])->default('aktif');
            $table->enum('flag_status', ['none', 'flagged', 'approved', 'rejected'])->default('none');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_pakan'); }
};