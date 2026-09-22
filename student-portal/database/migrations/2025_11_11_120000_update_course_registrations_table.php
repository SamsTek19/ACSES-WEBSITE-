<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->string('level')->nullable()->after('student_id');
            $table->string('semester')->nullable()->after('level');
            $table->string('programme')->nullable()->after('semester');
            $table->json('major_courses')->nullable()->after('programme');
            $table->json('elective_courses')->nullable()->after('major_courses');
            $table->text('comments')->nullable()->after('elective_courses');
            $table->json('document_paths')->nullable()->after('comments');
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'level',
                'semester',
                'programme',
                'major_courses',
                'elective_courses',
                'comments',
                'document_paths',
            ]);
        });
    }
};
