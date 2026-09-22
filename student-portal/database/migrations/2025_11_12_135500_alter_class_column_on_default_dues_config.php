<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `default_dues_config` MODIFY `class` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `default_dues_config` MODIFY `class` ENUM('Cyber Security','Computer Science','Information System') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
    }
};
