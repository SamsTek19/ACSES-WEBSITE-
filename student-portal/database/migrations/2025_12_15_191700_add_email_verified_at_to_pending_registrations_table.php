<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('year');
        });

        // Update existing pending registrations to have email verified
        // (they were created before this feature, so we trust them)
        \Illuminate\Support\Facades\DB::table('pending_registrations')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });
    }
};
