<?php

use App\Enums\ReasonCategoryGroupFabric;
use App\Models\Manufacture\Reasons\ReasonCategory;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'reasons';
    private const REASON_CATEGORY_ID_FOREIGN_KEY = 'reason_category_id';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            $table->string('name')
                ->nullable(false)
                ->unique()
                ->comment('Название причины');
            $table->string('display_name')
                ->nullable(false)
                ->unique()
                ->comment('Отображаемое название причины');

            // __ Номер группы в Категории причин, то есть не сквозная нумерация, а нумерация внутри Категории причин
            $table->integer('reason_number_in_reason_category')
                ->nullable(false)
                ->comment('Номер причины в категории причин');

            // __ Связь с категорией
            $table->foreignIdFor(ReasonCategory::class)
                ->nullable(false)
                ->comment('Категория причины')
                ->constrained()
                ->cascadeOnDelete();
        });


        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            [
                'name' => 'Пример причины изменения статуса на "Не выполнено"',
                'display_name' => 'Пример причины изменения статуса на "Не выполнено"',
                self::REASON_CATEGORY_ID_FOREIGN_KEY => ReasonCategoryGroupFabric::REASON_ROLL_FALSE,
                'reason_number_in_reason_category' => 0,
            ],
            [
                'name' => 'Пример причины изменения статуса на "Переходящий"',
                'display_name' => 'Пример причины изменения статуса на "Переходящий"',
                self::REASON_CATEGORY_ID_FOREIGN_KEY => ReasonCategoryGroupFabric::REASON_ROLL_ROLLING,
                'reason_number_in_reason_category' => 0,
            ],
            [
                'name' => 'Пример причины изменения статуса на "Отменено"',
                'display_name' => 'Пример причины изменения статуса на "Отменено"',
                self::REASON_CATEGORY_ID_FOREIGN_KEY => ReasonCategoryGroupFabric::REASON_ROLL_CANCELLED,
                'reason_number_in_reason_category' => 0,
            ],
            [
                'name' => 'Пример причины добавления рулона во время выполнения СЗ',
                'display_name' => 'Пример причины добавления рулона во время выполнения СЗ',
                self::REASON_CATEGORY_ID_FOREIGN_KEY => ReasonCategoryGroupFabric::REASON_ROLL_ADDING,
                'reason_number_in_reason_category' => 0,
            ],
            [
                'name' => 'Пример причины изменения порядка рулонов во время выполнения СЗ',
                'display_name' => 'Пример причины статуса "Не выполнено"',
                self::REASON_CATEGORY_ID_FOREIGN_KEY => ReasonCategoryGroupFabric::REASON_ROLL_REORDERED,
                'reason_number_in_reason_category' => 0,
            ],
            [
                'name' => 'Пример причины приостановки стегания рулона',
                'display_name' => 'Пример причины приостановки стегания рулона',
                self::REASON_CATEGORY_ID_FOREIGN_KEY => ReasonCategoryGroupFabric::REASON_ROLL_PAUSED,
                'reason_number_in_reason_category' => 0,
            ],
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
