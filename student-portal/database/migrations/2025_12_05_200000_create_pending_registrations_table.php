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
        if (!Schema::hasTable('pending_registrations')) {
            Schema::create('pending_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('fullname', 100);
                $table->string('username', 50)->unique();
                $table->string('email', 100);
                $table->string('password', 255);
                $table->string('phone_number', 20)->nullable();
                $table->string('index_number', 20);
                $table->enum('class', ['Cyber Security', 'Computer Science', 'Information System']);
                $table->enum('year', ['1', '2', '3', '4']);
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->index('email');
                $table->index('index_number');
                $table->index('status');
                $table->index('class');
                $table->index('year');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
