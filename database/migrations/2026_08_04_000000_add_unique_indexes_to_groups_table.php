<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'model_manufacture_groups';

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            // Вариант А: Если уникальными должны стать оба поля по отдельности
            $table->unique('name');
            $table->unique('group_number');

            // Вариант Б: Если уникальной должна быть только связка обоих полей вместе
            $table->unique(['name', 'group_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            // Откат для Варианта А (Laravel автоматически генерирует имена индексов по имени таблицы и колонок)
            $table->dropUnique(['name']);
            $table->dropUnique(['group_number']);

            // Откат для Варианта Б:
            $table->dropUnique(['name', 'group_number']);
        });
    }
};
