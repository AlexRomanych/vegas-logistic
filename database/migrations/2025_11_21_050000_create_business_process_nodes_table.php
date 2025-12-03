<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    private const TABLE_NAME = 'business_process_nodes';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Название операционного узла
            $table->string('name')
                ->nullable(false)
                ->unique(true)
                ->comment('Название бизнес узла');

            // __ Тип узла для схемы бизнес-процесса:
            // __ 'cell' - Производственная ячейка,
            // __ 'node' - Логический узел (типа "Загрузка на складе" или "Стартовая точка")
            // __ 'hide' - Скрытый узел
            // __ и т.д.
            $table->string('type')
                ->nullable(false)
                ->default('node')
                ->comment('Тип бизнес узла (cell, node, hide ...)');

            // __ Признак того, что у узла есть модуль управления (например, "Загрузка на складе", "Стежка")
            $table->boolean('has_module')
                ->nullable(false)
                ->default(false)
                ->comment('Наличие у узла модуля управления');

            // __ url для VueRouter для перехода в соответствующий компонент
            $table->string('route_name')
                ->nullable()
                ->comment('url для VueRouter для перехода в соответствующий компонент');

            // __ Тут может быть, например, признак старта узла (если параллельный процесс, то стартовать, если хоть один узел закончен или ожидать всех) (bool
            $table->string('node_start')->nullable()->comment('Старт узла');

            // __ Разрешить обработку (для случаев, когда, например, нужно запретить или разрешить обработку
            // __ данного узла в процессе оптимизации)
            $table->boolean('allow_action')
                ->nullable()
                ->comment('Разрешить обработку');

            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность');
            $table->unsignedSmallInteger('status')->nullable()->comment('Статус');
            $table->unsignedSmallInteger('order')->nullable()->comment('Порядок'); // Номер по порядку

            // __ Описание бизнес узла
            $table->string('description')
                ->nullable()
                ->comment('Описание бизнес узла');

            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->json('meta')->nullable()->comment('Метаданные');
            $table->jsonb('meta_extended')->nullable()->comment('Метаданные jsonb');

            $table->timestamps();
        });

        // Добавляем начальные данные
        DB::table(self::TABLE_NAME)->insert([
            ['name' => 'Поступление заявки', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Внесение заявки КС в 1С', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Обработка КБ', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Обработка ООП', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Обработка ОЗ', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Начало производства', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Раскрой', 'type' => 'node', 'route_name' => 'manufacture.cell.cutting', 'has_module' => true, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Пошив', 'type' => 'node', 'route_name' => 'manufacture.cell.sewing', 'has_module' => true, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Сборка', 'type' => 'node', 'route_name' => 'manufacture.cell.assembly', 'has_module' => true, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Упаковка', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Передача на склад ГП', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Загрузка на складе ГП', 'type' => 'node', 'route_name' => '', 'has_module' => true, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Стежка', 'type' => 'node', 'route_name' => 'manufacture.cell.fabrics', 'has_module' => true, 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Производство ПБ', 'type' => 'node', 'route_name' => '', 'has_module' => false, 'created_at' => now(), 'updated_at' => now(),],
        ]);


    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
