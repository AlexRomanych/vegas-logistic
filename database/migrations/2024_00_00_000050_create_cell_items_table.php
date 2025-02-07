<?php

use App\Classes\Unit;
use App\Models\Manufacture\CellNorm;
use App\Models\Manufacture\CellsGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cell_items', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('code1C')->comment('Код 1С');
            $table->string('name')->nullable(false)->unique()->comment('Название ПЯ');
            $table->integer('no')->nullable(false)->unique()->comment('Номер ПЯ');
            $table->string('unit')->nullable(false)->comment('Единица измерения производительности ПЯ');
            $table->float('manufacture_duration')->nullable()->comment('Длительность производства ПЯ');
            $table->string('url')->nullable()->unique()->comment('Уникальный url для vue-router');
            $table->string('description')->nullable()->comment('Описание');

            $table->string('source')->nullable()->comment('Источник данных (откуда берется информация о принадлежности изделия к ПЯ)');
            $table->timestamps();

            // attract У таблицы ПЯ есть норма, а сама ПЯ принадлежит группе ПЯ
            $table->foreignIdFor(CellNorm::class)->nullable()->constrained()->cascadeOnDelete();    //Внешний ключ, указывающий на таблицу с нормами
            $table->foreignIdFor(CellsGroup::class)->nullable()->constrained()->cascadeOnDelete();  //Внешний ключ, указывающий на таблицу с группами
        });



        // attract: Заполняем таблицу ПЯ на 20.12.2024
//        DB::table('cell_items')->insert([
//            ['code1C' => 'ПЯ001', 'no' => 1, 'name' => 'Стежка Китаец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ002', 'no' => 2, 'name' => 'Стежка Немец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ003', 'no' => 3, 'name' => 'Стежка Американец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ004', 'no' => 4, 'name' => 'Стежка Кореец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ005', 'no' => 5, 'name' => 'Стежка одноиголка', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ006', 'no' => 6, 'name' => 'Раскрой (панели чехла)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ007', 'no' => 7, 'name' => 'Раскрой (дет. чехла)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ008', 'no' => 8, 'name' => 'Оверлок', 'unit' => Unit::PRODUCTIVITY_METERS_PER_SECOND, 'source' => ''],
//            ['code1C' => 'ПЯ009', 'no' => 9, 'name' => 'Пошив (автоматы)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С'],
//            ['code1C' => 'ПЯ010', 'no' => 10, 'name' => 'Пошив (УШМ)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С'],
//            ['code1C' => 'ПЯ011', 'no' => 11, 'name' => 'Обшивка 1 (hard)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С'],
//            ['code1C' => 'ПЯ012', 'no' => 12, 'name' => 'Обшивка 1 (lite)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С'],
//            ['code1C' => 'ПЯ013', 'no' => 13, 'name' => 'Сборка Lamit', 'unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'source' => 'Справочник моделей в 1С'],
//            ['code1C' => 'ПЯ014', 'no' => 14, 'name' => 'Сборка Столы', 'unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'source' => 'Справочник моделей в 1С'],
//            ['code1C' => 'ПЯ015', 'no' => 15, 'name' => 'Упаковка', 'unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'source' => ''],
//            ['code1C' => 'ПЯ016', 'no' => 16, 'name' => 'НПБ(производство)', 'unit' => Unit::PRODUCTIVITY_SQUARE_METERS_PER_HOUR, 'source' => ''],
//        ]);

        DB::table('cell_items')->insert([
            ['code1C' => 'ПЯ001', 'no' => 1, 'name' => 'Стежка Китаец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => '', 'cells_group_id' => 3, 'cell_norm_id' => 1, 'url' => 'stitch/china'],
            ['code1C' => 'ПЯ002', 'no' => 2, 'name' => 'Стежка Немец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => '', 'cells_group_id' => 3, 'cell_norm_id' => 2, 'url' => 'stitch/german'],
            ['code1C' => 'ПЯ003', 'no' => 3, 'name' => 'Стежка Американец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => '', 'cells_group_id' => 3, 'cell_norm_id' => 3, 'url' => 'stitch/american'],
            ['code1C' => 'ПЯ004', 'no' => 4, 'name' => 'Стежка Кореец', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => '', 'cells_group_id' => 3, 'cell_norm_id' => 4, 'url' => 'stitch/korean'],
            ['code1C' => 'ПЯ005', 'no' => 5, 'name' => 'Стежка одноиголка', 'unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'source' => '', 'cells_group_id' => 3, 'cell_norm_id' => 5, 'url' => 'stitch/one_needle'],
            ['code1C' => 'ПЯ006', 'no' => 6, 'name' => 'Раскрой (панели чехла)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => '', 'cells_group_id' => null, 'cell_norm_id' => 6, 'url' => 'cutting/cover_panel'],
            ['code1C' => 'ПЯ007', 'no' => 7, 'name' => 'Раскрой (дет. чехла)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => '', 'cells_group_id' => null, 'cell_norm_id' => 7, 'url' => 'cutting/cover_panel_child'],
            ['code1C' => 'ПЯ008', 'no' => 8, 'name' => 'Оверлок', 'unit' => Unit::PRODUCTIVITY_METERS_PER_SECOND, 'source' => '', 'cells_group_id' => null, 'cell_norm_id' => 8, 'url' => 'overloсk'],
            ['code1C' => 'ПЯ009', 'no' => 9, 'name' => 'Пошив (автоматы)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С', 'cells_group_id' => 1, 'cell_norm_id' => 9, 'url' => 'sewing/auto'],
            ['code1C' => 'ПЯ010', 'no' => 10, 'name' => 'Пошив (УШМ)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С', 'cells_group_id' => 1, 'cell_norm_id' => 10, 'url' => 'sewing/universal'],
            ['code1C' => 'ПЯ011', 'no' => 11, 'name' => 'Обшивка 1 (hard)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С', 'cells_group_id' => 1, 'cell_norm_id' => 11, 'url' => 'sewing/hard'],
            ['code1C' => 'ПЯ012', 'no' => 12, 'name' => 'Обшивка 1 (lite)', 'unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'source' => 'Справочник моделей в 1С', 'cells_group_id' => 1, 'cell_norm_id' => 12, 'url' => 'sewing/light'],
            ['code1C' => 'ПЯ013', 'no' => 13, 'name' => 'Сборка Lamit', 'unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'source' => 'Справочник моделей в 1С', 'cells_group_id' => 2, 'cell_norm_id' => 13, 'url' => 'assembly/lamit'],
            ['code1C' => 'ПЯ014', 'no' => 14, 'name' => 'Сборка Столы', 'unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'source' => 'Справочник моделей в 1С', 'cells_group_id' => 2, 'cell_norm_id' => 14, 'url' => 'assembly/table'],
            ['code1C' => 'ПЯ015', 'no' => 15, 'name' => 'Упаковка', 'unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'source' => '', 'cells_group_id' => null, 'cell_norm_id' => 15, 'url' => 'pack'],
            ['code1C' => 'ПЯ016', 'no' => 16, 'name' => 'НПБ(производство)', 'unit' => Unit::PRODUCTIVITY_SQUARE_METERS_PER_HOUR, 'source' => '', 'cells_group_id' => null, 'cell_norm_id' => 15, 'url' => 'blocks'],
        ]);


    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_items');
    }
};
