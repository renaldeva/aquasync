<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mqtt_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 50)->nullable();
            $table->string('topic', 150);
            $table->json('payload')->nullable();
            $table->smallInteger('qos')->default(0);
            $table->enum('direction', ['incoming', 'outgoing'])->default('incoming');
            $table->boolean('processed')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
 
            $table->index(['device_id', 'created_at']);
            $table->index('topic');
        });
    }
    public function down(): void { Schema::dropIfExists('mqtt_logs'); }
};