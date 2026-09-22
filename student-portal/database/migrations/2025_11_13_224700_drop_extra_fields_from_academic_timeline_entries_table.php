<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_timeline_entries', function (Blueprint $table): void {
            $table->dropColumn([
                'summary',
                'description',
                'ends_at',
                'cta_label',
                'cta_url',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('academic_timeline_entries', function (Blueprint $table): void {
            $table->string('summary', 255)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('cta_label', 80)->nullable();
            $table->string('cta_url')->nullable();
        });
    }
};
