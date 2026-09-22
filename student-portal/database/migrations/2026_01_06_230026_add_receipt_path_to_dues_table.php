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
        if (Schema::hasTable('dues') && !Schema::hasColumn('dues', 'receipt_path')) {
            Schema::table('dues', function (Blueprint $table) {
                $table->string('receipt_path')->nullable()->after('payment_notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dues', function (Blueprint $table) {
            $table->dropColumn('receipt_path');
        });
    }
};
