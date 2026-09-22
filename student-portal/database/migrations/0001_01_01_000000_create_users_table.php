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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('user_id', true);
                $table->string('username', 50)->unique();
                $table->string('fullname', 100);
                $table->string('email', 100)->unique();
                $table->string('password', 255);
                $table->string('phone_number', 20)->nullable();
                $table->string('index_number', 20)->nullable()->unique();
                $table->enum('class', ['Cyber Security', 'Computer Science', 'Information System']);
                $table->enum('year', ['1', '2', '3', '4']);
                $table->string('department', 100)->nullable();
                $table->enum('role', ['admin', 'student', 'faculty'])->default('student');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->string('profile_picture', 255)->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->boolean('is_seller')->default(false);
                $table->string('pending_email', 255)->nullable();
                $table->string('email_verification_token', 64)->nullable();
                $table->string('ec_access', 6)->nullable();

                $table->index(['user_id', 'is_seller'], 'user_seller_idx');
                $table->index('ec_access', 'ec_access_idx');
                $table->index('class', 'idx_users_class');
                $table->index('year', 'idx_users_year');

                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
