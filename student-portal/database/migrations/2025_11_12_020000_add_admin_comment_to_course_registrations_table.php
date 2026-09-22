<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('course_registrations', 'admin_comment')) {
                $table->text('admin_comment')->nullable()->after('document_paths');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('course_registrations', 'admin_comment')) {
                $table->dropColumn('admin_comment');
            }
        });
    }
};
