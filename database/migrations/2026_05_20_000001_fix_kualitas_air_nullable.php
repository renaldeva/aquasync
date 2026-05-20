<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Simpan definisi view dulu
        $viewDef = DB::select("SELECT definition FROM pg_views WHERE viewname = 'v_latest_ph'");
        $definition = $viewDef[0]->definition ?? null;

        // 2. Drop view yang bergantung pada kolom ph_value
        DB::statement('DROP VIEW IF EXISTS v_latest_ph');

        // 3. Alter kolom jadi nullable langsung via raw SQL (bypass doctrine)
        DB::statement('ALTER TABLE kualitas_air ALTER COLUMN ph_value DROP NOT NULL');

        // 4. Buat ulang view kalau tadi ada
        if ($definition) {
            DB::statement("CREATE OR REPLACE VIEW v_latest_ph AS {$definition}");
        }
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_latest_ph');
        DB::statement('ALTER TABLE kualitas_air ALTER COLUMN ph_value SET NOT NULL');

        // Buat ulang view (salin definisi asli di sini kalau perlu)
        DB::statement('
            CREATE OR REPLACE VIEW v_latest_ph AS
            SELECT DISTINCT ON (kolam_id)
                kolam_id,
                ph_value,
                status_ph,
                recorded_at
            FROM kualitas_air
            ORDER BY kolam_id, recorded_at DESC
        ');
    }
};