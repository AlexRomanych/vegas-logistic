<?php

use App\Services\SharedService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private const TABLE_NAME = 'cells_groups';
    private const TYPE_COLUMN_NAME = 'type';
    private const URL_COLUMN_NAME = 'url';

    public function up(): void
    {

        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            // __ Тип записи для схемы производства:
            // __ 'cell' - Производственная ячейка,
            // __ 'node' - Логический узел (типа "Загрузка на складе" или "Стартовая точка")
            // __ Пока только такие значения
            $table->string(self::TYPE_COLUMN_NAME)
                ->nullable(false)
                ->default('cell')
                ->comment('Тип записи (ПЯ/лог. узел и т.д.)');

            // __ url для VueRouter для перехода в соответствующий компонент
            $table->string(self::URL_COLUMN_NAME)
                ->nullable(true)
                ->comment('url для VueRouter для перехода в соответствующий компонент');

        });


        // __ Пересоздадим последовательность для id
        SharedService::setSequence(self::TABLE_NAME);
        // $tableName = self::TABLE_NAME;
        // $sql = "SELECT setval(
        //     pg_get_serial_sequence(?, 'id'),
        //     (SELECT COALESCE(MAX(id), 0) FROM {$tableName})
        // );";
        //
        // DB::statement($sql, [$tableName]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(self::TYPE_COLUMN_NAME);
            $table->dropColumn(self::URL_COLUMN_NAME);
        });
    }
};
