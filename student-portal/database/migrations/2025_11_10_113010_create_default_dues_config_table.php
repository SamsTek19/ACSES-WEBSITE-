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
        Schema::create('default_dues_config', function (Blueprint $table) {
            $table->integer('config_id', true);
            $table->enum('class', ['Cyber Security', 'Computer Science', 'Information System']);
            $table->enum('year', ['1', '2', '3', '4']);
            $table->string('target_group', 20)->default('existing');
            $table->decimal('amount', 10, 2);
            $table->string('description', 255);
            $table->integer('due_date_offset')->default(30);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('created_by')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unique(['class', 'year'], 'class_year_unique');
            $table->index('created_by');
            $table->index(['class', 'year', 'target_group'], 'idx_default_dues_group');

            $table->foreign('created_by')->references('user_id')->on('users');

            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_dues_config');
    }
};
