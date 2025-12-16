<?php

use App\Models\Models\ModelManufactureType;
use App\Models\Models\ModelManufactureStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private const TABLE_NAME = 'models';
    private const MODEL_MANUFACTURE_TYPE_CODE1C = 'model_manufacture_type_code1C';
    private const MODEL_TYPE_CODE1C = 'model_type_code_1c';

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            // __ Связь со статусом модели ('Выпускается', 'Архивный' и т.д.)
            $table->foreignIdFor(ModelManufactureStatus::class)
                ->nullable(true)
                ->comment('Статус модели')
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();


            // __ Связь с типом модели ('матрас', 'наматрасник' и т.д.)
            $table->string(self::MODEL_TYPE_CODE1C, 9)
                ->nullable(true)
                ->comment('Тип модели');

            $table->foreign(self::MODEL_TYPE_CODE1C)
                ->references(CODE_1C)
                ->on('model_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();


            // __ Связь с типом производства модели ('Производство матрасов', 'Производство подушек' и т.д.)
            $table->string(self::MODEL_MANUFACTURE_TYPE_CODE1C, 9)
                ->nullable(true)
                ->comment('Тип производства модели');

            $table->foreign(self::MODEL_MANUFACTURE_TYPE_CODE1C)
                ->references(CODE_1C)
                ->on('model_manufacture_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            $table->dropForeign([self::MODEL_MANUFACTURE_TYPE_CODE1C]);
            $table->dropColumn(self::MODEL_MANUFACTURE_TYPE_CODE1C);

            $table->dropForeignIdFor(ModelManufactureStatus::class);
        });
    }
};
