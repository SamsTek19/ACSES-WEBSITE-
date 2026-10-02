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
            $table->string('memories_link', 255)->nullable();
        });

        DB::table('events')
            ->whereIn('title', [
                'Annual Hackathon 2025: AI for Good',
                'ACSES Annual Dinner and Awards Night',
                'Kakalika Freshers Akwaaba Night',
            ])
            ->whereNotNull('cta_url')
            ->update([
                'memories_link' => DB::raw('cta_url'),
                'cta_url' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('events')
            ->whereNull('cta_url')
            ->whereNotNull('memories_link')
            ->update(['cta_url' => DB::raw('memories_link')]);

        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn('memories_link');
        });
    }
};
