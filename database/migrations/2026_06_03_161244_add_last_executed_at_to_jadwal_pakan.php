<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('jadwal_pakan', function (Blueprint $table) {
            $table->timestamp('last_executed_at')->nullable()->after('flag_status');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pakan', function (Blueprint $table) {
            $table->dropColumn('last_executed_at');
        });
    }
};