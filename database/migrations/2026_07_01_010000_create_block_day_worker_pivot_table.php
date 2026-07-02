<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    const TABLE_NAME = 'block_day_worker_pivot';

    /** @noinspection DuplicatedCode */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Внешние ключи
            $table->foreignId('block_day_id')
                ->nullable(false)
                ->comment('Связь с производственным днем пошива')
                ->constrained('block_days', 'id')
                ->cascadeOnDelete();

            $table->foreignId('worker_id')
                ->nullable(false)
                ->comment('Связь с работником')
                ->constrained('workers', 'id')
                ->cascadeOnDelete();

            // __ Номер производственной смены. Пока 1 смена, но задел на будущее
            $table->unsignedSmallInteger('change')
                ->nullable(false)
                ->default(1)
                ->comment('Номер производственной смены');

            // __ Отработанное время
            $table->integer('working_time')
                ->nullable()
                ->comment('Отработанное время в минутах');

            // __ Уникальный индекс производственного дня и работника
            $table->unique(['block_day_id', 'worker_id', 'change']);
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
