<?php

use App\Models\Order\OrderLine;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'order_line_material_pivot';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {

            $table->id();

            // Relations: Связь с Записью в Заявке
            $table->foreignIdFor(OrderLine::class)
                ->nullable(false)
                ->index()
                ->comment('Запись в Заявке (строка)')
                ->constrained()
                ->cascadeOnDelete();

            // Relations: Связь с Материалами (Тот, что получаем на выходе процедуры)
            $table->string('material_code_1c', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Материал');
            $table->foreign('material_code_1c')
                ->references(CODE_1C)
                ->on('materials')
                ->nullOnDelete();

            // __ Останется при удалении материала
            // __ Что приходит на вход процедуры
            $table->string('material_name_specification')->nullable()->comment('Название материала в спецификациях');
            // __ Что получаем на выходе
            $table->string('material_name_expense')->nullable()->comment('Название материала в расходе');


            $table->string('material_code_1c_copy', CODE_1C_LENGTH)
                ->nullable()
                ->comment('Копия ссылки на материал');

            $table->double('expense_per_pic')->nullable(false)->default(0.0)->comment('Расход на единицу');
            $table->double('expense')->nullable(false)->default(0.0)->comment('Общий расход');
            $table->double('rest_per_pic')->nullable(false)->default(0.0)->comment('Остаток на единицу');
            $table->double('rest')->nullable(false)->default(0.0)->comment('Общий остаток');

            $table->string('unit')->nullable()->comment('Единица измерения');
            $table->string('detail')->nullable()->comment('Название детали из спецификации');
            $table->string('procedure')->nullable()->comment('Название процедуры, если есть');
            $table->string('object_name')->nullable()->comment('Название объекта процедуры, к которому она была применена');
            $table->smallInteger('position')->nullable()->comment('Позиция в списке записей спецификации');


            $table->jsonb('scopes')->nullable(false)->default('[]')->comment('Переменные, которые получили в процедуре расчета');
            $table->jsonb('outputs')->nullable(false)->default('[]')->comment('Выходные свойства материала');

            // Relations: Связь с Процедурой (для отслеживания расчетов) - чтобы быстро получить процедуру для проверки
            //$table->string('procedure_code_1c', CODE_1C_LENGTH)
            //    ->nullable()
            //    ->comment('Процедура расчета');
            //$table->foreign('procedure_code_1c')
            //    ->references(CODE_1C)
            //    ->on('model_construct_procedures')
            //    ->nullOnDelete();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        {
            Schema::dropIfExists(self::TABLE_NAME);
        }
    }
};
