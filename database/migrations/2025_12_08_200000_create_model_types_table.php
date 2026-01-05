<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'model_types';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(CODE_1C, CODE_1C_LENGTH);
            $table->primary(CODE_1C);
            $table->string('name')->comment('Тип модели');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([

            [CODE_1C => AVERAGE_M_PREFIX . '0000', 'name' => 'Плановый Матрас', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => AVERAGE_A_PREFIX . '0000', 'name' => 'Плановый Аксессуар', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000001', 'name' => 'Матрас', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000002', 'name' => 'Наматрасник модифицирующий', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000003', 'name' => 'Наматрасник защитный', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000004', 'name' => 'Подушка', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000005', 'name' => 'Основание', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000006', 'name' => 'Постельное белье', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000009', 'name' => 'Одеяло', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000010', 'name' => 'Бокс-спринг', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000011', 'name' => 'Кровать', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000012', 'name' => 'Чехол', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000013', 'name' => 'Нетканые материалы Airfoam', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000014', 'name' => 'Нетканые материалы Бикокос', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000015', 'name' => 'Нетканые материалы Термовойлок', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000016', 'name' => 'Нетканые материалы Кокос', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000017', 'name' => 'ППУ крой', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000020', 'name' => 'Полотно стеганное', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000023', 'name' => 'Нетканые материалы AIRFELT', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000024', 'name' => 'Чехол для подушки', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000026', 'name' => 'Деталь полотно стеганное', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000027', 'name' => 'Рекламная продукция', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000028', 'name' => 'Защитное изделие', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000029', 'name' => 'Декор', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000030', 'name' => 'Блоки независимых пружин', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000031', 'name' => 'Ручка', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000032', 'name' => 'Тесьма', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000033', 'name' => 'Тумбочка', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000034', 'name' => 'Диван', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000035', 'name' => 'Коврик декоративный', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000036', 'name' => 'Пижама', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000037', 'name' => 'Кресло', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000038', 'name' => 'Пуф', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000039', 'name' => 'Нетканые материалы Лен', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000040', 'name' => 'Нетканые материалы Hemp', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000041', 'name' => 'Подматрасник', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000042', 'name' => 'Нетканые материалы Наполнитель', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000043', 'name' => 'Нетканые материалы AIRFIBER', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000044', 'name' => 'Нетканые материалы Утеплитель', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000045', 'name' => 'Субстраты кокосового волокна', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000046', 'name' => 'Нетканые материалы Сизаль', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000047', 'name' => 'Нетканые материалы Шерсть', 'created_at' => now(), 'updated_at' => now(),],
        ]);

    }


    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
