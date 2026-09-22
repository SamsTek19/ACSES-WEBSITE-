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
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('payment_id', true);
            $table->integer('due_id');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'momo', 'bank']);
            $table->string('reference_number', 50)->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();

            $table->index('due_id');
            $table->index('created_by');

            $table->foreign('due_id')->references('due_id')->on('dues')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('users');

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
        Schema::dropIfExists('payments');
    }
};
