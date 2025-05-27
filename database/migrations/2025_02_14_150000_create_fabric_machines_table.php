<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME='fabric_machines';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->nullable(false)->unique()->comment('Название машины');
            $table->string('short_name')->nullable()->unique()->comment('Сокращенное название машины');

            // attract: Привязываемся к таблице с схемами, чтобы иметь текущую схему на стегальной машине
            // attract: К рисунку также привязываемся, но отдельной миграцией
            $table->foreignId('curr_schema')->nullable(false)->default(0)->comment('Текущая схема игл на стегальной машине')
                ->references('id')
                ->on('fabric_picture_schemas')
                ->nullOnDelete();
        });

        // attract: Добавляем общие атрибуты, которые привязываются к таблицам
        $this->addCommonColumns(self::TABLE_NAME);

        // Добавляем в таблицу имена машин
        DB::table(self::TABLE_NAME)->insert([
            ['id' => 0, 'name' => 'Машина не определена', 'short_name' => 'Нет данных', 'active' => true],
            ['id' => 1, 'name' => 'LEGACY-4', 'short_name' => 'Американец', 'active' => true],
            ['id' => 2, 'name' => 'CHAINTRONIC', 'short_name' => 'Немец', 'active' => true],
            ['id' => 3, 'name' => 'HY-W-DGW', 'short_name' => 'Китаец', 'active' => true],
            ['id' => 4, 'name' => 'МТ-94', 'short_name' => 'Кореец', 'active' => true],
            ['id' => 5, 'name' => 'Одноиголка', 'short_name' => 'Одноиголка', 'active' => false],
        ]);

//        DB::table(self::TABLE_NAME)->insert([
//            ['id' => 0, 'name' => 'Машина не определена', 'short_name' => 'Нет данных'],
//            ['id' => 1, 'name' => 'LEGACY-4 (Американец)', 'short_name' => 'Американец'],
//            ['id' => 2, 'name' => 'CHAINTRONIC (Немец)', 'short_name' => 'Немец'],
//            ['id' => 3, 'name' => 'HY-W-DGW (Китаец)', 'short_name' => 'Китаец'],
//            ['id' => 4, 'name' => 'МТ-94 (Кореец)', 'short_name' => 'Кореец'],
//        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
