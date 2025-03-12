<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'fabric_picture_schemas';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(0);
            $table->string('schema_name')->nullable(false)->unique()->comment('Название схемы расположения иголок');
            $table->string('schema')->nullable(false)->unique()->comment('Схема расположения иголок');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('image')->nullable()->comment('Изображение');        // Тут может быть и ссылка на картинку
            $table->timestamps();
        });

        DB::table(self::TABLE_NAME)->insert([
            ['id' => 0, 'schema_name' => 'Нет данных', 'schema' =>''],
            ['id' => 1, 'schema_name' => '1 ряд', 'schema' =>'1'],
            ['id' => 2, 'schema_name' => '1/2 ряд', 'schema' =>'1/2'],
            ['id' => 3, 'schema_name' => '1/3 ряд', 'schema' =>'1/3'],
            ['id' => 4, 'schema_name' => '2/3 ряд', 'schema' =>'2/3'],
            ['id' => 5, 'schema_name' => '3 ряд', 'schema' =>'3'],
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
