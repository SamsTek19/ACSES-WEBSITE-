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
        if (!Schema::hasTable('payment_settings')) {
            Schema::create('payment_settings', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('setting_key', 100)->unique();
                $table->string('setting_value', 255);
                $table->text('description')->nullable();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->integer('updated_by')->nullable();

                $table->index('updated_by');
                $table->foreign('updated_by')->references('user_id')->on('users');

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
        Schema::dropIfExists('payment_settings');
    }
};
