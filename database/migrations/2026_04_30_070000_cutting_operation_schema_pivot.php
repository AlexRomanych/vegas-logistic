<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'cutting_operation_schema_pivot';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Внешние ключи
            $table->foreignId('cutting_operation_schema_id')
                ->nullable(false)
                ->comment('Связь со cхемой швейных операций')
                ->constrained('cutting_operation_schemas', 'id')
                ->cascadeOnDelete();

            $table->foreignId('cutting_operation_id')
                ->nullable(false)
                ->comment('Связь со швейной операцией')
                ->constrained('cutting_operations', 'id')
                ->cascadeOnDelete();

            $table->float('ratio')->nullable()->comment('Коэффициент');
            $table->float('amount')->nullable()->comment('Количество');
            $table->string('condition')->nullable()->comment('Дополнительные условия');
            $table->integer('position')->nullable()->comment('Позиция');

            // __ Уникальный индекс: одна схема—операция
            $table->unique(['cutting_operation_schema_id', 'cutting_operation_id']);

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
