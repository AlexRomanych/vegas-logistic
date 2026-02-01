<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'model_sewing_operation_pivot';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Внешние ключи
            $table->string('model_code_1c', CODE_1C_LENGTH)->nullable(false)->comment('Связь с моделью');
            $table->foreign('model_code_1c')
                ->references(CODE_1C)
                ->on('models')
                ->cascadeOnDelete();

            $table->foreignId('sewing_operation_id')
                ->nullable(false)
                ->comment('Связь со швейной операцией')
                ->constrained('sewing_operations', 'id')
                ->cascadeOnDelete();

            $table->float('ratio')->nullable()->comment('Коэффициент');
            $table->string('condition')->nullable()->comment('Дополнительные условия');

            // __ Уникальный индекс: одна модель—операция
            $table->unique(['model_code_1c', 'sewing_operation_id']);

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
