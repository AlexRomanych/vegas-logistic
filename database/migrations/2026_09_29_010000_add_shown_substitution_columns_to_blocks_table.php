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
            $table
                ->boolean('shown')
                ->nullable(false)
                ->default(true)
                ->comment('Показывать или нет в Справочнике Блоков');

            // Relations: Связь с Блоком Подмены
            $table
                ->string('substitution_block_code_1c', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Код Блока для подмены в СЗ');;
            $table
                ->foreign('substitution_block_code_1c')
                ->references(CODE_1C)
                ->on('blocks')
                ->nullOnDelete();

            $table
                ->boolean('substitution')
                ->nullable(false)
                ->default(false)->comment('Заменять текущий Блок другим или нет при создании СЗ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            // __ Сначала сбрасываем внешний ключ
            $table->dropForeign(['substitution_block_code_1c']);

            // __ Затем удаляем добавленные колонки
            $table->dropColumn([
                'shown',
                'substitution_block_code_1c',
                'substitution',
            ]);
        });
    }
};
