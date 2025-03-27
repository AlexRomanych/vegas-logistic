<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private const TABLE_NAME='fabric_machines';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(0);
            $table->string('name')->nullable(false)->unique()->comment('Название машины');
            $table->string('short_name')->nullable()->unique()->comment('Сокращенное название машины');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');

            $table->timestamps();

            // attract: Привязываемся к таблице с схемами, чтобы иметь текущую схему на стегальной машине
            // attract: К рисунку также привязываемся, но отдельной миграцией
            $table->foreignId('curr_schema')->nullable(false)->default(0)->comment('Текущая схема игл на стегальной машине')
                ->references('id')
                ->on('fabric_picture_schemas')
                ->nullOnDelete();
        });

        // Добавляем в таблицу имена машин
        DB::table(self::TABLE_NAME)->insert([
            ['id' => 0, 'name' => 'Машина не определена', 'short_name' => 'Нет данных'],
            ['id' => 1, 'name' => 'LEGACY-4', 'short_name' => 'Американец'],
            ['id' => 2, 'name' => 'CHAINTRONIC', 'short_name' => 'Немец'],
            ['id' => 3, 'name' => 'HY-W-DGW', 'short_name' => 'Китаец'],
            ['id' => 4, 'name' => 'МТ-94', 'short_name' => 'Кореец'],
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
