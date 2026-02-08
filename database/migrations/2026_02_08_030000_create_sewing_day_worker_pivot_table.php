<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    const TABLE_NAME = 'sewing_day_worker_pivot';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Внешние ключи
            $table->foreignId('sewing_day_id')
                ->nullable(false)
                ->comment('Связь с производственным днем пошива')
                ->constrained('sewing_days', 'id')
                ->cascadeOnDelete();

            $table->foreignId('worker_id')
                ->nullable(false)
                ->comment('Связь с работником')
                ->constrained('workers', 'id')
                ->cascadeOnDelete();

            // __ Уникальный индекс производственного дня и работника
            $table->unique(['sewing_day_id', 'worker_id']);

            // __ Отработанное время
            $table->integer('working_time')
                ->nullable()
                ->comment('Отработанное время в минутах');
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
