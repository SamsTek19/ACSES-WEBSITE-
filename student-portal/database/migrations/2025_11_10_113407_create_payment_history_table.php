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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->integer('history_id', true);
            $table->integer('due_id');
            $table->integer('student_id');
            $table->string('old_status', 50);
            $table->string('new_status', 50);
            $table->string('academic_year', 9);
            $table->enum('action_type', ['assign', 'verify', 'reset', 'payment_submission']);
            $table->integer('changed_by');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('due_id');
            $table->index('student_id');
            $table->index('changed_by');
            $table->index('academic_year', 'idx_payment_history_academic_year');

            $table->foreign('due_id')->references('due_id')->on('dues')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('changed_by')->references('user_id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('payment_history');
    }
};
