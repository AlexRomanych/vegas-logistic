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
        Schema::table('blocks', function (Blueprint $table) {
            // CODE_1C — ваша константа (например, 'code_1c')
            $table->unique(CODE_1C);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            // При откате миграции удаляем уникальный индекс
            // Передаем массивом, чтобы Laravel сам вычислил имя индекса
            $table->dropUnique([CODE_1C]);
        });
    }
};
