<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'model_manufacture_groups';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(0);
            $table->string('name')->comment('Название Группы');
            $table->unsignedSmallInteger('group_number')->comment('Номер Группы');

        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            ['id' => 0, 'group_number' => 0, 'name' => 'Неопознанные', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 1, 'group_number' => 1, 'name' => 'ППУ',  'created_at' => now(), 'updated_at' => now(),],
            ['id' => 2, 'group_number' => 2, 'name' => 'Обшивка-Скрутка', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 3, 'group_number' => 3, 'name' => 'Обшивка 1', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 4, 'group_number' => 4, 'name' => 'Обшивка 2', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 5, 'group_number' => 5, 'name' => 'AIRFOAM', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 6, 'group_number' => 6, 'name' => 'Smart', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 7, 'group_number' => 7, 'name' => 'FMX',  'created_at' => now(), 'updated_at' => now(),],
            ['id' => 8, 'group_number' => 8, 'name' => 'Сжатие', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 9, 'group_number' => 9, 'name' => 'Скрутка', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 10, 'group_number' => 10, 'name' => 'Монолиты', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 11, 'group_number' => 11, 'name' => 'Детские', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 12, 'group_number' => 12, 'name' => 'Наматрасники', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 13, 'group_number' => 13, 'name' => '2-х этажные', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 14, 'group_number' => 14, 'name' => 'Полоски', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 15, 'group_number' => 15, 'name' => 'MSA HR', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 16, 'group_number' => 16, 'name' => 'MSA 2836', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 17, 'group_number' => 17, 'name' => 'MSA mem', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 18, 'group_number' => 18, 'name' => 'MSA конв.', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 19, 'group_number' => 19, 'name' => 'MSA Latex', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 20, 'group_number' => 20, 'name' => 'MSA Visco', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 21, 'group_number' => 21, 'name' => 'SYNERGY', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 22, 'group_number' => 22, 'name' => 'Тафтинг', 'created_at' => now(), 'updated_at' => now(),],
        ]);

        setSequence(self::TABLE_NAME, 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
