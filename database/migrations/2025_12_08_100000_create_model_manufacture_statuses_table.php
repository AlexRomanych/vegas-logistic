<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'model_manufacture_statuses';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(0);
            $table->string('name')->comment('Название статуса');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            ['id' => 0, 'name' => 'Вариант исполнения', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 1, 'name' => 'Снята с производства', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 2, 'name' => 'Устаревшее название', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 3, 'name' => 'Архив', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 4, 'name' => 'Выпускается', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 5, 'name' => 'Разовый выпуск', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 6, 'name' => 'Без признака', 'created_at' => now(), 'updated_at' => now(),],
            ['id' => 7, 'name' => 'На паузе', 'created_at' => now(), 'updated_at' => now(),],
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
