<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'model_construct_procedures';

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->text('text_vba')->nullable()->comment('Текст процедуры для VBA');
            $table->text('text_parser')->nullable()->comment('Текст процедуры для парсера');
            $table->text('text_reserved')->nullable()->comment('Текст процедуры для резервирования');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            // Проверяем наличие каждого столбца перед удалением
            if (Schema::hasColumn(self::TABLE_NAME, 'text_vba')) {
                $table->dropColumn('text_vba');
            }

            if (Schema::hasColumn(self::TABLE_NAME, 'text_parser')) {
                $table->dropColumn('text_parser');
            }

            if (Schema::hasColumn(self::TABLE_NAME, 'text_reserved')) {
                $table->dropColumn('text_reserved');
            }

            //$table->dropColumn(['text_vba', 'text_parser', 'text_reserved']);
        });
    }
};
