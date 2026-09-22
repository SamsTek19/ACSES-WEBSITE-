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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->enum('type', ['general', 'security', 'maintenance'])->default('general');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->timestamp('published_at')->nullable();
            $table->integer('author_id')->nullable();
            $table->timestamps();

            $table->index(['type', 'published_at']);
            $table->index(['priority', 'published_at']);
            $table->index('author_id');

            $table->foreign('author_id')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
