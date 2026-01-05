<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'model_manufacture_types';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(CODE_1C, CODE_1C_LENGTH)->primary()->comment('Код 1C');
            $table->string('name')->comment('Название вида производства');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        // __ Добавляем для средних плановых изделий
        DB::table(self::TABLE_NAME)->insert([
            [CODE_1C => AVERAGE_M_PREFIX . '0000', 'name' => 'Производство плановых матрасов', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            [CODE_1C => AVERAGE_A_PREFIX . '0000', 'name' => 'Производство плановых аксессуаров', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            [CODE_1C => '000000005', 'name' => 'Производство матрасов', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000003', 'name' => 'Производство стеганных полотен', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000006', 'name' => 'Производство подушек', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000008', 'name' => 'Производство постельного белья', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000009', 'name' => 'Производство стеганых одеял', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000010', 'name' => 'Производство наматрасников', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000016', 'name' => 'Производство полуфабрикатов', 'created_at' => now(), 'updated_at' => now(),],
            [CODE_1C => '000000017', 'name' => 'Производство подматрасников', 'created_at' => now(), 'updated_at' => now(),],
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
