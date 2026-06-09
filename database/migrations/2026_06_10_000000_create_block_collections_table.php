<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE = 'block_collections';

    public function up(): void

    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string(CODE_1C, CODE_1C_LENGTH)->nullable(false)->unique()->comment('Код из 1С');
            $table->string('name')->nullable(false)->unique()->comment('Название группы');
            $table->string('unit')->nullable()->comment('Единица измерения');
            $table->integer('height')->nullable(false)->default(0)->comment('Высота блока коллекции, см');
            $table->string('kdb')->nullable()->comment('Конструкторская документация блоков (КДБ)');
            $table->integer('line')->nullable(false)->default(0)->comment('Линия производства блоков');
            $table->integer('priority')->nullable(false)->default(0)->comment('Приоритет выполнения на линии');
        });

        $this->addCommonColumns(self::TABLE);

        DB::table(self::TABLE)->insert([
            [CODE_1C => '000000000', 'name' => 'Без группы', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000030100', 'name' => '(V) Smartpocket R1000 140 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000033065', 'name' => '(V) TFK R1000 180 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000034580', 'name' => '(V) TPS Mini R1000 100 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000033796', 'name' => '(V) TPS Mini R1000 80 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000040243', 'name' => 'БП (V) Pocket Spring Sample R1000 160 (1,7)', 'line' => 0, 'unit' => 'мп.',],
            [CODE_1C => '000029994', 'name' => 'БП (V) Smartpocket R1000 110 B (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000029995', 'name' => 'БП (V) Smartpocket R1000 140 B (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000045023', 'name' => 'БП (V) TPS R1000 120 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000029893', 'name' => 'БП (V) TPS R1000 120 (1,8)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001914', 'name' => 'БП Medizone R1000 140 Z (H2)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001871', 'name' => 'БП Medizone R1000 140 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001915', 'name' => 'БП Medizone R1000 140 Z (H4)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000019090', 'name' => 'БП Medizone R2000 Mini 80 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001900', 'name' => 'БП Medizone R2200 140 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001880', 'name' => 'БП Medizone X1200 140 Z (H4)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001890', 'name' => 'БП Medizone X3000 140 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000044551', 'name' => 'БП Multipocket R2000 Mini 80 (1,3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000046812', 'name' => 'БП Multipocket X2200 Mini 80 (1,3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000041405', 'name' => 'БП NanoPocket 25 (1,2)', 'line' => 0, 'unit' => 'мп',],
            [CODE_1C => '000040250', 'name' => 'БП Pocket R1000 160 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000044350', 'name' => 'БП Pocket R1000 160 (1,7) А БК', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000046752', 'name' => 'БП Pocket R1000 160 (1,7) АтД БК', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000046761', 'name' => 'БП Pocket R1000 160 (1,9) АтД БК', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000039269', 'name' => 'БП Pocket R1500 160 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000039469', 'name' => 'БП Pocket R800 140Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000039470', 'name' => 'БП Pocket R800 140Z (H4)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000041626', 'name' => 'БП Pocket RX1000 180 (1,7) А', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000022733', 'name' => 'БП Smartpocket R1000 140 Z (H2)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000000817', 'name' => 'БП TFK Mini 60 (1,6)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000022740', 'name' => 'БП TFK R1000 140 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000026568', 'name' => 'БП TFK R1000 180 (1,7)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000023649', 'name' => 'БП TFK R2200 140 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000023621', 'name' => 'БП TFK X1200 140 Z (H4)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000023658', 'name' => 'БП TFK X3000 140 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000001544', 'name' => 'БП TPS 120 Z (H3)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000034660', 'name' => 'БП Ultrapocket R4400 120 (0,9)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000000743', 'name' => 'БП Боннель 110 (2,2)', 'line' => 0, 'unit' => 'шт.',],
            [CODE_1C => '000017343', 'name' => 'БП Форматочные', 'line' => 0, 'unit' => 'шт.',],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
