<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    const POSITION_COLUMN_NAME = 'position';

    public function up(): void
    {
        Schema::table('fabric_orders', function (Blueprint $table) {
            $table->bigInteger(self::POSITION_COLUMN_NAME)->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabric_orders', function (Blueprint $table) {
            $table->dropColumn(self::POSITION_COLUMN_NAME);
        });
    }
};
