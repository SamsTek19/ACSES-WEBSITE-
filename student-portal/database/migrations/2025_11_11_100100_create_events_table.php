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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->string('category')->nullable();
            $table->string('cta_url')->nullable();
            $table->integer('created_by')->nullable();
            $table->boolean('display_on_timeline')->default(false);
            $table->unsignedTinyInteger('timeline_order')->nullable();
            $table->timestamps();

            $table->index(['start_at', 'end_at']);
            $table->index('category');
            $table->index(['display_on_timeline', 'timeline_order']);
            $table->index('created_by');

            $table->foreign('created_by')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
