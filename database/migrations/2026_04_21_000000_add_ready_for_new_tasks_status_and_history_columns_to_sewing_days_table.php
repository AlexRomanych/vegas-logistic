<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sewing_days', function (Blueprint $table) {
            $table
                ->boolean('ready')
                ->nullable(false)
                ->default(false)
                ->comment(
                    'Маяк того, что день с СЗ в статусе `Выполняется` находится в состоянии добавления новых СЗ'
                );
            $table->text('history')->nullable()->comment('История изменений');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sewing_days', function (Blueprint $table) {
            // Удаляем колонки массивом
            $table->dropColumn(['ready', 'history']);
        });
    }
};
