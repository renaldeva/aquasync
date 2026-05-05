<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kolam', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kolam', 20)->unique();
            $table->string('nama_kolam', 100);
            $table->enum('jenis', ['pembesaran', 'pendederan', 'induk']);
            $table->decimal('kapasitas_liter', 10, 2)->nullable();
            $table->integer('jumlah_ikan')->default(0);
            $table->date('tanggal_tebar')->nullable();
            $table->enum('status', ['aktif', 'kosong', 'maintenance'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kolam'); }
};