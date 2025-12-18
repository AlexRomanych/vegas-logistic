<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'materials';

    public function up(): void
    {
        // Relations: Связь с Группой материалов
        Schema::table('materials', function (Blueprint $table) {
            $table->foreign('material_group_code_1c')
                ->references(CODE_1C)
                ->on(self::TABLE_NAME)
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Relations: Связь с Категорией
            $table->foreign('material_category_code_1c')
                ->references(CODE_1C)
                ->on(self::TABLE_NAME)
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        // __ Создаем дефолтную Группу материалов, куда будет добавляться материал, если не найдем его Группу
        DB::table(self::TABLE_NAME)->insert([
            CODE_1C           => UNDEFINED_MATERIALS_GROUP_CODE1C,
            CODE_1C . '_copy' => UNDEFINED_MATERIALS_GROUP_CODE1C,
            'name'            => UNDEFINED_MATERIALS_GROUP_NAME,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // __ Создаем дефолтную Группу материалов для ручной проработки
        DB::table(self::TABLE_NAME)->insert([
            CODE_1C           => PENDING_MATERIALS_GROUP_CODE1C,
            CODE_1C . '_copy' => PENDING_MATERIALS_GROUP_CODE1C,
            'name'            => PENDING_MATERIALS_GROUP_NAME,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // __ Создаем дефолтную Категорию материалов, куда будет добавляться материал, если не найдем его Категорию
        DB::table(self::TABLE_NAME)->insert([
            CODE_1C                  => UNDEFINED_MATERIALS_CATEGORY_CODE1C,
            CODE_1C . '_copy'        => UNDEFINED_MATERIALS_CATEGORY_CODE1C,
            'name'                   => UNDEFINED_MATERIALS_CATEGORY_NAME,
            'material_group_code_1c' => UNDEFINED_MATERIALS_GROUP_CODE1C,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // __ Создаем дефолтную Категорию материалов для ручной проработки
        DB::table(self::TABLE_NAME)->insert([
            CODE_1C                  => PENDING_MATERIALS_CATEGORY_CODE1C,
            CODE_1C . '_copy'        => PENDING_MATERIALS_CATEGORY_CODE1C,
            'name'                   => PENDING_MATERIALS_CATEGORY_NAME,
            'material_group_code_1c' => PENDING_MATERIALS_GROUP_CODE1C,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            //
        });
    }
};
