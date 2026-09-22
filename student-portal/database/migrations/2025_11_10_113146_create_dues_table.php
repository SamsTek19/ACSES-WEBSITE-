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
        Schema::create('dues', function (Blueprint $table) {
            $table->integer('due_id', true);
            $table->integer('student_id');
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->string('academic_year', 9);
            $table->enum('payment_status', ['owing', 'pending_verification', 'paid'])->default('owing');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('verification_date')->nullable();
            $table->integer('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('network', 50)->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('payment_notes')->nullable();
            $table->string('recorded_by', 50)->nullable();
            $table->text('rejection_reason')->nullable();

            $table->index('student_id');
            $table->index('verified_by');
            $table->index('academic_year', 'idx_dues_academic_year');

            $table->foreign('verified_by')->references('user_id')->on('users');

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues');
    }
};
