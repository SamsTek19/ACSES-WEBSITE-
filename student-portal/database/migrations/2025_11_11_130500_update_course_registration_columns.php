<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('course_registrations', 'document_paths')) {
            Schema::table('course_registrations', function (Blueprint $table) {
                $table->json('document_paths')->nullable()->after('approved_at');
            });
        }

        $deprecated = [
            'level',
            'semester',
            'programme',
            'major_courses',
            'elective_courses',
            'comments',
        ];

        foreach ($deprecated as $column) {
            if (Schema::hasColumn('course_registrations', $column)) {
                Schema::table('course_registrations', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('course_registrations', 'document_paths')) {
            Schema::table('course_registrations', function (Blueprint $table) {
                $table->dropColumn('document_paths');
            });
        }

        if (! Schema::hasColumn('course_registrations', 'level')) {
            Schema::table('course_registrations', function (Blueprint $table) {
                $table->string('level')->nullable()->after('student_id');
                $table->string('semester')->nullable()->after('level');
                $table->string('programme')->nullable()->after('semester');
                $table->json('major_courses')->nullable()->after('programme');
                $table->json('elective_courses')->nullable()->after('major_courses');
                $table->text('comments')->nullable()->after('elective_courses');
            });
        }
    }
};
