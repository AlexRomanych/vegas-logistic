<?php

use App\Models\Manufacture\Cells\Sewing\SewingOperation;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    const TABLE_NAME = 'sewing_operations';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Название операции
            $table->string('name')->nullable(false)->comment('Название операции');
            $table->string('machine')->nullable()->comment('Оборудование');
            $table->string('type')->nullable()->comment('Тип операции (статическая, динамическая и т.д.)');
            $table->integer('time')->nullable(false)->default(0)->comment('Время операции, сек.');

            // $table->foreignIdFor(SewingOperation::class)->nullable()->comment('Связь со схемой');

            $table->string('additional')->nullable()->comment('Для дополнительной информации');

            $table->unique(['name', 'machine']);

        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            ['name' => 'Обметка панелей 100С', 'machine' => 'Оверлок', 'type' => 'dynamic', 'time' => 8],
            ['name' => 'Обметка панелей 200С/300С', 'machine' => 'Оверлок', 'type' => 'dynamic', 'time' => 11],
            ['name' => 'Обметка нестеганых панелей', 'machine' => 'Оверлок', 'type' => 'dynamic', 'time' => 11],
            ['name' => 'Обметка крышки с одноиголки', 'machine' => 'Оверлок', 'type' => 'dynamic', 'time' => 13],
            ['name' => 'Обметка бурлета', 'machine' => 'Оверлок', 'type' => 'dynamic', 'time' => 10],
            ['name' => 'Застрочить 1 угол', 'machine' => 'Угол', 'type' => 'dynamic', 'time' => 7],
            ['name' => 'Вшить молнию в один прием 100С', 'machine' => 'АШМ', 'type' => 'dynamic', 'time' => 28],
            ['name' => 'Вшить молнию в один прием 200С/300С', 'machine' => 'АШМ', 'type' => 'dynamic', 'time' => 30],
            ['name' => 'Вшить молнию в один прием нестеганая ткань', 'machine' => 'АШМ', 'type' => 'dynamic', 'time' => 33],
            ['name' => 'Стачать 3D сетку', 'machine' => 'УШМ', 'type' => 'static', 'time' => 10],
            ['name' => 'Стачать цельный бурлет', 'machine' => 'УШМ', 'type' => 'static', 'time' => 15],
            ['name' => 'Стачать бурлет из 2-ух частей', 'machine' => 'УШМ', 'type' => 'static', 'time' => 30],
            ['name' => 'Стачать бурлет из 4 частей/пилоутопер', 'machine' => 'УШМ', 'type' => 'static', 'time' => 60],
            ['name' => 'Пришить ярлык коллекции', 'machine' => 'УШМ', 'type' => 'static', 'time' => 10],
            ['name' => 'Пришить этикетку', 'machine' => 'УШМ', 'type' => 'static', 'time' => 10],
            ['name' => 'Настрочить вертикальные ручки (1шт)', 'machine' => 'УШМ', 'type' => 'static', 'time' => 20],
            ['name' => 'Настрочить нашивку на бурлет', 'machine' => 'УШМ', 'type' => 'static', 'time' => 60],
            ['name' => 'Притачать одну сторону молнии к бурлету', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 28],
            ['name' => 'Притачать одну сторону молнию к 3D сетке', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 28],
            ['name' => 'Притачать одну сторону молнии к крышке 100С/нестеганая', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 30],
            ['name' => 'Притачать одну сторону молнию к крышке 200С/300С', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 33],
            ['name' => 'Притачать крышку к бурлету /3D сетке', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 30],
            ['name' => 'Настрочить кант-пайпинг к 3D сетке', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 18],
            ['name' => 'Стачать пилоутопер с мембраной/торцевую вставку', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 20],
            ['name' => 'Проложить закрепляющую строчку', 'machine' => 'УШМ', 'type' => 'dynamic', 'time' => 25],
            [
                'name'    => 'Притачать одну сторону молнии к крышке окантовывая 200С/300С',
                'machine' => 'Окантователь',
                'type'    => 'dynamic',
                'time'    => 30
            ],
            [
                'name'    => 'Притачать одну сторону молнию к крышке с одноиголки окантовывая',
                'machine' => 'Окантователь',
                'type'    => 'dynamic',
                'time'    => 35
            ],
            [
                'name'    => 'Притачать бурлет к крышке окантовывая 100С/ нестеганая',
                'machine' => 'Окантователь',
                'type'    => 'dynamic',
                'time'    => 28
            ],
            ['name' => 'Притачать бурлет к крышке окантовывая 200С/300С', 'machine' => 'Окантователь', 'type' => 'dynamic', 'time' => 30],
            ['name' => 'Притачать бурлет к крышке с одноиголки окантовывая', 'machine' => 'Окантователь', 'type' => 'dynamic', 'time' => 35],
            ['name' => 'Притачать бурлет к крышке окантовывая 100С/ нестеганая', 'machine' => 'Обшивка', 'type' => 'dynamic', 'time' => 18],
            ['name' => 'Притачать бурлет к крышке окантовывая 200С/300С', 'machine' => 'Обшивка', 'type' => 'dynamic', 'time' => 20],
            ['name' => 'Притачать бурлет к крышке с одноиголки', 'machine' => 'Обшивка', 'type' => 'dynamic', 'time' => 30],
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
