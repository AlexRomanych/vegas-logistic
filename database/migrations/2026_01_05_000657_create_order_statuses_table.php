<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'order_statuses';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Название статуса
            $table->string('name')
                ->nullable(false)
                ->unique()
                ->comment('Название статуса');

            // __ Позиция (порядок) статуса
            $table->integer('position')
                ->nullable(false)
                ->default(0)
                ->comment('Позиция (порядок) статуса');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            ['name' => 'Создание плановой заявки', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Загружена из 1С', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Раскрой: СЗ создано', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Раскрой: СЗ готово к выполнению', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Раскрой: СЗ в процессе', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Раскрой: СЗ выполнено', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Пошив: СЗ создано', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Пошив: СЗ готово к выполнению', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Пошив: СЗ в процессе', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Пошив: СЗ выполнено', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Сборка: СЗ создано', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Сборка: СЗ готово к выполнению', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Сборка: СЗ в процессе', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Сборка: СЗ выполнено', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Передана на склад', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Отгружена', 'created_at' => now(), 'updated_at' => now()],
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
