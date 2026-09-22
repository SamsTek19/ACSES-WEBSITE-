<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_resources', function (Blueprint $table) {
            if (! Schema::hasColumn('support_resources', 'resource_type')) {
                $table->string('resource_type', 20)->default('link')->after('category');
            }

            if (! Schema::hasColumn('support_resources', 'file_path')) {
                $table->string('file_path')->nullable()->after('cta_url');
            }

            if (! Schema::hasColumn('support_resources', 'visibility')) {
                $table->string('visibility', 20)->default('student')->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_resources', function (Blueprint $table) {
            if (Schema::hasColumn('support_resources', 'visibility')) {
                $table->dropColumn('visibility');
            }

            if (Schema::hasColumn('support_resources', 'file_path')) {
                $table->dropColumn('file_path');
            }

            if (Schema::hasColumn('support_resources', 'resource_type')) {
                $table->dropColumn('resource_type');
            }
        });
    }
};
