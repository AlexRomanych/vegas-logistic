<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;
    private const TABLE_NAME = 'model_construct_items';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // Relations: Связь со Спецификацией
            $table->string('construct_code_1c', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Спецификация');
            $table->foreign('construct_code_1c')
                ->references(CODE_1C)
                ->on('model_constructs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Relations: Связь с Материалами
            $table->string('material_code_1c', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Материал');
            $table->foreign('material_code_1c')
                ->references(CODE_1C)
                ->on('materials')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // __ Копия кода 1С Материала (При обновлении Материала и каскадном nullOnDelete, след материала)
            $table->string('material_code_1c_copy', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Копия кода 1С Материала');

            // __ Название материала (избыточно, так как есть в материале, пока оставляем)
            $table->string('material_name')->nullable(false)->comment('Название материала');

            // __ Единица измерения (избыточно, так как есть в материале, пока оставляем)
            $table->string('material_unit')->nullable()->comment('Единица измерения');

            $table->string('detail')->nullable()->comment('Деталь');
            $table->decimal('detail_height',8,4)->nullable()->comment('Высота детали');

            // Relations: Связь с Процедурой расчета
            $table->string('procedure_code_1c', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Процедура расчета');
            $table->foreign('procedure_code_1c')
                ->references(CODE_1C)
                ->on('model_construct_procedures')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // __ Копия кода 1С Процедуры расчета (При обновлении Процедуры расчета и каскадном nullOnDelete, след процедуры)
            $table->string('procedure_code_1c_copy', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Копия кода 1С Процедуры расчета');

            // __ Название процедуры расчета (избыточно, так как есть в Процедуре, пока оставляем)
            $table->string('procedure_name')->nullable()->comment('Название процедуры расчета');


            $table->decimal('amount')->nullable()->comment('Количество');
            $table->unsignedSmallInteger('position')->nullable()->comment('Позиция');


        });

        $this->addCommonColumns(self::TABLE_NAME);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
