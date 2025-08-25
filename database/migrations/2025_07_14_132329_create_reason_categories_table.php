<?php

use App\Enums\CellsGroupConst;
use App\Enums\ReasonCategoryGroupFabric;
use App\Models\Manufacture\CellsGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\AddCommonColumnsInTableTrait;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'reason_categories';
    private const CELLS_GROUPS_ID_FOREIGN_KEY = 'cells_group_id';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            $table->string('name')
                ->nullable(false)
                ->unique()
                ->comment('Название категории');
            $table->string('display_name')
                ->nullable(false)
                ->unique()
                ->comment('Отображаемое название категории');

            // __ Номер группы в группе ПЯ, то есть не сквозная нумерация, а нумерация внутри группы ПЯ
            $table->integer('group_number_in_cell_group')
                ->nullable(false)
                ->comment('Номер группы в группе ПЯ');

            // __ Связь с группой ПЯ
            $table->foreignIdFor(CellsGroup::class)
                ->nullable(false)
                ->default(0)
                ->comment('Связь с группой ПЯ')
                ->constrained()
                ->cascadeOnDelete();

        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            [
                'name' => 'Статус "Не выполнено"',
                'display_name' => 'Статус "Не выполнено"',
                self::CELLS_GROUPS_ID_FOREIGN_KEY => CellsGroupConst::FABRIC_GROUP,
                'group_number_in_cell_group' => ReasonCategoryGroupFabric::REASON_ROLL_FALSE,
                'description' => 'Причина статуса "Не выполнено"',
            ],
            [
                'name' => 'Статус "Переходящий"',
                'display_name' => 'Статус "Переходящий"',
                self::CELLS_GROUPS_ID_FOREIGN_KEY => CellsGroupConst::FABRIC_GROUP,
                'group_number_in_cell_group' => ReasonCategoryGroupFabric::REASON_ROLL_ROLLING,
                'description' => 'Причина статуса "Переходящий"',
            ],
            [
                'name' => 'Статус "Отменено"',
                'display_name' => 'Статус "Отменено"',
                self::CELLS_GROUPS_ID_FOREIGN_KEY => CellsGroupConst::FABRIC_GROUP,
                'group_number_in_cell_group' => ReasonCategoryGroupFabric::REASON_ROLL_CANCELLED,
                'description' => 'Причина статуса "Отменено"',
            ],
            [
                'name' => 'Добавлен во время выполнения',
                'display_name' => 'Добавлен во время выполнения',
                self::CELLS_GROUPS_ID_FOREIGN_KEY => CellsGroupConst::FABRIC_GROUP,
                'group_number_in_cell_group' => ReasonCategoryGroupFabric::REASON_ROLL_ADDING,
                'description' => 'Причина добавления рулона во время выполнения',
            ],
            [
                'name' => 'Изменен порядок во время выполнения',
                'display_name' => 'Изменен порядок во время выполнения',
                self::CELLS_GROUPS_ID_FOREIGN_KEY => CellsGroupConst::FABRIC_GROUP,
                'group_number_in_cell_group' => ReasonCategoryGroupFabric::REASON_ROLL_REORDERED,
                'description' => 'Причина изменения порядка рулонов во время выполнения',
            ],
            [
                'name' => 'Статус "Приостановка выполнения"',
                'display_name' => 'Статус "Приостановка выполнения"',
                self::CELLS_GROUPS_ID_FOREIGN_KEY => CellsGroupConst::FABRIC_GROUP,
                'group_number_in_cell_group' => ReasonCategoryGroupFabric::REASON_ROLL_PAUSED,
                'description' => 'Причина статуса "Приостановка выполнения"',
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
