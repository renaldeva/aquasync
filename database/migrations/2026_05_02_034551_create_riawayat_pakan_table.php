<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('riwayat_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pakan_id')->nullable()->constrained('jadwal_pakan')->nullOnDelete();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->timestamp('waktu_eksekusi');
            $table->decimal('jumlah_aktual', 8, 2)->nullable();
            $table->enum('status', ['sukses', 'gagal', 'terlambat'])->default('sukses');
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
 
            $table->index(['kolam_id', 'waktu_eksekusi']);
        });
    }
    public function down(): void { Schema::dropIfExists('riwayat_pakan'); }
};
