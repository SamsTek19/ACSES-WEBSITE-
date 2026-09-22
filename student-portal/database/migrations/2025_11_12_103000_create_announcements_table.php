<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('target_type')->default('all')->after('priority');
            $table->json('target_filters')->nullable()->after('target_type');
            $table->unsignedInteger('delivered_count')->default(0)->after('target_filters');
            $table->timestamp('sent_at')->nullable()->after('delivered_count');

            $table->index('target_type');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex(['target_type']);
            $table->dropIndex(['sent_at']);
            $table->dropColumn(['target_type', 'target_filters', 'delivered_count', 'sent_at']);
        });
    }
};
