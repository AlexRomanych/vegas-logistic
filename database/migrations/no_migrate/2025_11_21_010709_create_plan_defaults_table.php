<?php

use App\Models\Manufacture\CellsGroup;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    /**
     * ___ Таблица, которая описывает настройку для формирования плана (смещение по дате)
     * ___ для использования при генерации планов
     */

    const TABLE_NAME = 'plan_defaults';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Связь с группой ПЯ (группа ПЯ, относительно которой задается смещение)
            $table->foreignIdFor(CellsGroup::class)
                ->nullable(false)
                ->comment('Референс на группу ПЯ')
                ->constrained()
                ->cascadeOnDelete();

            // __ Операция (true - сложение, false - вычитание)
            $table->boolean('operation')
                ->nullable()
                ->comment('Операция (true - сложение, false - вычитание)');

            // __ Смещение по дате в днях
            $table->integer('offset')
                ->nullable(false)
                ->default(0)
                ->comment('Смещение по дате в днях');

            // __ Расширенная логика (не только +/-, а еще и условие) (true - использовать, false - не использовать)
            $table->boolean('is_extended_logic')
                ->nullable(false)
                ->default(false)
                ->comment('Расширенная логика (не только +/-, а еще и условие)');

            // __ Метаданные (jsonb). Делаем на случай, если логика будет не только +/-, а еще и условие
            $table->jsonb('meta_extended')->nullable()->comment('Метаданные (jsonb)');
            // $table->timestamps();
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
