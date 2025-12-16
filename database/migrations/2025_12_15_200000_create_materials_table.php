<?php

use App\Enums\MaterialUnits;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use App\Models\Materials\MaterialCategory;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'materials';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(CODE_1C, CODE_1C_LENGTH)->primary()->comment('Код 1C');
            $table->string('name')->nullable(false)->comment('Название материала');
            $table->string('unit')->nullable()->default(MaterialUnits::UNDEFINED)->comment('Единица измерения');
            $table->string('supplier')->comment('Поставщик');

            $table->string('alt_unit')->nullable()->comment('Альтернативная Единица измерения');
            $table->float('alt_multiplier')->nullable(false)->default(1.0)->comment('Альтернативная Единица измерения');
            $table->boolean('apply_alt_unit')->nullable(false)->default(false)->comment('Применять альтернативную единицу измерения к материалу');

            $table->unsignedInteger('order')->nullable(false)->default(0)->comment('Позиция в списке');

            $table->boolean('is_deleted')->nullable(false)->default(false)->comment('Софт удаление');
            $table->boolean('is_shown')->nullable(false)->default(true)->comment('Показывать в списке');

            // $table->foreignIdFor(MaterialCategory::class)
            //     ->nullable(false)
            //     ->default(UNDEFINED_MATERIALS_CATEGORY_CODE1C)
            //     ->comment('Категория материала')
            //     ->constrained()
            //     ->cascadeOnDelete();

            // Relations: Связь с Категорией
            $table->string('material_category_code_1c',CODE_1C_LENGTH)
                ->nullable(false)
                ->default(UNDEFINED_MATERIALS_CATEGORY_CODE1C)
                ->comment('Категория материала');
            $table->foreign('material_category_code_1c')
                ->references(CODE_1C)
                ->on('material_categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();


            $table->softDeletes();

            $table->jsonb('meta_extended')->nullable()->comment('Метаданные ++');
        });

        $this->addCommonColumns(self::TABLE_NAME);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
