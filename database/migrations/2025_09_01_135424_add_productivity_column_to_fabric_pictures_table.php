<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    private const TABLE_NAME = 'fabric_pictures';
    private const COLUMN_NAME = 'productivity';


    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->float(self::COLUMN_NAME)->nullable(false)->default(0)->comment('Производительность, м.п./час на основной СМ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(self::COLUMN_NAME);
        });
    }
};
