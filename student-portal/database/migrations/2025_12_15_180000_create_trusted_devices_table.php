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
        if (!Schema::hasTable('trusted_devices')) {
            Schema::create('trusted_devices', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('device_fingerprint', 128)->comment('Hashed fingerprint of browser/device');
                $table->string('device_name', 255)->nullable()->comment('Human-readable device description');
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 512)->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'device_fingerprint']);
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
    }
};
