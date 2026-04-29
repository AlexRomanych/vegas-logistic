<?php

use App\Models\Order\OrderLine;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'order_line_pivot_material';

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

            // Relations: Связь с Материалами
            $table->string('material_code_1c', CODE_1C_LENGTH)
                ->nullable(false)
                ->comment('Материал');
            $table->foreign('material_code_1c')
                ->references(CODE_1C)
                ->on('materials')
                ->cascadeOnDelete();

            $table->double('amount_per_pic')->nullable()->comment('Количество на единицу');
            $table->string('unit')->nullable()->comment('Единица измерения');

            $table->jsonb('vars')->nullable()->comment('Переменные, которые получили в процедуре расчета');

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
