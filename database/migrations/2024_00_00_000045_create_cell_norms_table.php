<?php

use App\Classes\Unit;
use App\Models\Manufacture\CellItem;
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
        Schema::create('cell_norms', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('unit')->nullable(false)->comment('Единица измерения производительности ПЯ');
            $table->string('params')->nullable()->comment('Входные параметры для расчета производительности динамической нормы');
            $table->float('correction')->nullable(false)->default(1.0)->comment('Коэффициент для расчета производительности динамической нормы');
            $table->string('formula')->nullable()->comment('Формула для расчета производительности динамической нормы');
            $table->string('formula_php')->nullable()->comment('Формула для расчета производительности динамической нормы, адаптированная под PHP');
            $table->timestamps();

            $table->foreignIdFor(CellItem::class)->nullable(false)->comment('Привязка нормы к ПЯ');
        });

        //        public const PRODUCTIVITY_SQUARE_METERS_PER_HOUR = 'м2/ч';
        //        public const PRODUCTIVITY_METERS_PER_HOUR = 'м/ч';
        //        public const PRODUCTIVITY_METERS_PER_SECOND = 'м/с';
        //        public const PRODUCTIVITY_PICS_PER_HOUR = 'шт/ч';
        //        public const PRODUCTIVITY_METERS_LINEAR_PER_HOUR = 'мп/ч';

        // attract: Заполняем таблицу ПЯ на 20.12.2024
        DB::table('cell_norms')->insert([
            ['unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'cell_item_id' => 1],
            ['unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'cell_item_id' => 2],
            ['unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'cell_item_id' => 3],
            ['unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'cell_item_id' => 4],
            ['unit' => Unit::PRODUCTIVITY_METERS_LINEAR_PER_HOUR, 'cell_item_id' => 5],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'cell_item_id' => 6],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'cell_item_id' => 7],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_SECOND, 'cell_item_id' => 8],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'cell_item_id' => 9],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'cell_item_id' => 10],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'cell_item_id' => 11],
            ['unit' => Unit::PRODUCTIVITY_METERS_PER_HOUR, 'cell_item_id' => 12],
            ['unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'cell_item_id' => 13],
            ['unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'cell_item_id' => 14],
            ['unit' => Unit::PRODUCTIVITY_PICS_PER_HOUR, 'cell_item_id' => 15],
            ['unit' => Unit::PRODUCTIVITY_SQUARE_METERS_PER_HOUR, 'cell_item_id' => 16],
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_norms');
    }
};
