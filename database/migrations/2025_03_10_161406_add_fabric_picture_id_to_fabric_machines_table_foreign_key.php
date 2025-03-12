<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // attract: Привязываемся к таблице с рисунками, чтобы иметь текущий рисунок на стегальной машине

    private const TABLE_NAME = 'fabric_machines';
    private const COLUMN_NAME = 'curr_picture';         // текущий рисунок на стегальной машине
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->foreignId(self::COLUMN_NAME)->nullable(false)->default(0)->comment('Текущая рисунок на стегальной машине')
                ->references('id')
                ->on('fabric_pictures')
                ->nullOnDelete();
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
