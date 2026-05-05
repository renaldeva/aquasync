<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->date('tanggal_rencana');
            $table->decimal('estimasi_berat', 10, 2)->nullable();
            $table->integer('estimasi_jumlah')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'selesai', 'ditunda'])->default('pending');
            $table->enum('flag_status', ['none', 'flagged', 'approved', 'rejected'])->default('none');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_panen'); }
};