<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('REVOKE ALL PRIVILEGES ON ALL TABLES IN SCHEMA public FROM anon, authenticated');
        DB::statement('ALTER DEFAULT PRIVILEGES IN SCHEMA public REVOKE ALL ON TABLES FROM anon, authenticated');

        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");

        foreach ($tables as $table) {
            $tableName = '"'.str_replace('"', '""', $table->tablename).'"';
            DB::statement("ALTER TABLE public.{$tableName} ENABLE ROW LEVEL SECURITY");
        }
    }

    public function down(): void
    {
        // Keep public API access disabled when rolling back application code.
    }
};