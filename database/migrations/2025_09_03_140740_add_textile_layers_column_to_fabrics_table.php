<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    private const TABLE_NAME = 'fabrics';
    private const COLUMN_NAME = 'textile_layers_amount';


    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedSmallInteger(self::COLUMN_NAME)->nullable(false)->default(1)->comment('Количество слоев ткани, шт.');
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
