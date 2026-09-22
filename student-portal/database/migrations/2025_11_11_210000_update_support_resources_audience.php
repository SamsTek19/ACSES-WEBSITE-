<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_resources', function (Blueprint $table) {
            if (! Schema::hasColumn('support_resources', 'content_type')) {
                $table->string('content_type', 50)->default('general')->after('resource_type');
            }

            if (! Schema::hasColumn('support_resources', 'target_classes')) {
                $table->json('target_classes')->nullable()->after('icon');
            }

            if (! Schema::hasColumn('support_resources', 'target_years')) {
                $table->json('target_years')->nullable()->after('target_classes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_resources', function (Blueprint $table) {
            if (Schema::hasColumn('support_resources', 'target_years')) {
                $table->dropColumn('target_years');
            }

            if (Schema::hasColumn('support_resources', 'target_classes')) {
                $table->dropColumn('target_classes');
            }

            if (Schema::hasColumn('support_resources', 'content_type')) {
                $table->dropColumn('content_type');
            }
        });
    }
};
