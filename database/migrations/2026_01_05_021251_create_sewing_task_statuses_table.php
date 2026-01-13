<?php

use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'sewing_task_statuses';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Название статуса
            $table->string('name')
                ->nullable(false)
                ->unique()
                ->comment('Название статуса');

            // __ Позиция (порядок) статуса
            $table->integer('position')
                ->nullable(false)
                ->default(0)
                ->comment('Позиция (порядок) статуса');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            [
                'id'         => SewingTaskStatus::SEWING_STATUS_CREATED_ID,
                'name'       => 'Создано',
                'position'   => SewingTaskStatus::SEWING_STATUS_CREATED_ID,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'         => SewingTaskStatus::SEWING_STATUS_ROLLING_ID,
                'name'       => 'Создано при закрытии СЗ',
                'position'   => SewingTaskStatus::SEWING_STATUS_ROLLING_ID,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'         => SewingTaskStatus::SEWING_STATUS_PENDING_ID,
                'name'       => 'Готово к выполнению',
                'position'   => SewingTaskStatus::SEWING_STATUS_PENDING_ID,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'         => SewingTaskStatus::SEWING_STATUS_RUNNING_ID,
                'name'       => 'Выполняется',
                'position'   => SewingTaskStatus::SEWING_STATUS_RUNNING_ID,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'         => SewingTaskStatus::SEWING_STATUS_DONE_ID,
                'name'       => 'Выполнено',
                'position'   => SewingTaskStatus::SEWING_STATUS_DONE_ID,
                'created_at' => now(),
                'updated_at' => now()
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
