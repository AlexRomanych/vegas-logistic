<?php

use App\Models\Materials\MaterialGroup;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'material_categories';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(CODE_1C, CODE_1C_LENGTH)->primary()->comment('Код 1C');
            $table->string('name')->nullable(false)->comment('Название Категории материалов');

            $table->boolean('is_shown')->nullable(false)->default(true)->comment('Показывать в списке');
            $table->boolean('is_deleted')->nullable(false)->default(false)->comment('Софт удаление');
            $table->boolean('is_collapsed')->nullable(false)->default(false)->comment('Схлопывать или разворачивать при запуске');
            $table->boolean('apply_alt_unit')->nullable(false)->default(false)->comment('Применять альтернативную единицу измерения к Категории материалов');

            $table->unsignedInteger('order')->nullable(false)->default(0)->comment('Позиция в списке');

            // Relations: Связь с Группой материалов
            $table->string('material_group_code_1c', CODE_1C_LENGTH)
                ->nullable(false)
                ->default(UNDEFINED_MATERIALS_GROUP_CODE1C)
                ->comment('Группа материала');
            $table->foreign('material_group_code_1c')
                ->references(CODE_1C)
                ->on('material_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // $table->foreignIdFor(MaterialGroup::class)
            //     ->nullable(false)
            //     ->default(UNDEFINED_MATERIALS_GROUP_CODE1C)
            //     ->comment('Группа материала')
            //     ->constrained()
            //     ->cascadeOnDelete();

            $table->softDeletes();

            $table->jsonb('meta_extended')->nullable()->comment('Метаданные ++');

        });

        $this->addCommonColumns(self::TABLE_NAME);

        // __ Создаем дефолтную категорию материалов, куда будет добавляться материал, если не найдем его категорию
        DB::table(self::TABLE_NAME)->insert([
            CODE_1C => UNDEFINED_MATERIALS_CATEGORY_CODE1C,
            'name' => UNDEFINED_MATERIALS_CATEGORY_NAME,
            'created_at' => now(),
            'updated_at' => now(),
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
