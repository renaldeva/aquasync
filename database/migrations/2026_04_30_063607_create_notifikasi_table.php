<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->text('pesan');
            $table->enum('tipe', ['info', 'warning', 'danger', 'success', 'flag'])->default('info');
            $table->string('referensi_type', 30)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
 
            $table->index(['user_id', 'is_read']);
        });
    }
    public function down(): void { Schema::dropIfExists('notifikasi'); }
};