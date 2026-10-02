<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dateTime('start_at')->nullable()->change();
            $table->string('time_label', 80)->nullable();
        });
    }

    public function down(): void
    {
        if (DB::table('events')->whereNull('start_at')->exists()) {
            throw new RuntimeException('Cannot make events.start_at required while unscheduled events exist.');
        }

        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn('time_label');
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->dateTime('start_at')->nullable(false)->change();
        });
    }
};
