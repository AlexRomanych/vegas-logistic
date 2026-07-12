<?php

use App\Models\Manufacture\Events\CellEvent;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE = 'cell_events';

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();

            $table->string('cell')
                ->nullable(false)
                ->default(CellEvent::CELL_UNKNOWN)
                ->index()
                ->comment('Идентификатор Производственной ячейки (константа)');

            // __ День (и соответственно смена), к которому привязано событие
            $table->bigInteger('day_id')
                ->unsigned()
                ->nullable()
                ->index()
                ->comment('День (и соответственно смена), к которому привязано событие');

            // __ СЗ, для которого создается Запись. Пока не пользуем, но на перспективу можно
            $table->bigInteger('task_id')
                ->unsigned()
                ->nullable()
                ->comment('СЗ, для которого создается Запись');

            // __ Дата и время начала События
            $table->timestamp('start_at')
                ->nullable(false)
                ->comment('Дата и время начала События');

            // __ Дата и время окончания События
            $table->timestamp('finish_at')
                ->nullable(false)
                ->comment('Дата и время окончания События');

            // __ Категория События
            $table->string('category')
                ->nullable()
                ->comment('Категория События (опционально)');

            // __ Само событие
            $table->text('event')
                ->nullable(false)
                ->comment('Событие');

            // __ Обратная связь
            $table->text('answer')
                ->nullable()
                ->comment('Обратная связь');
        });

        $this->addCommonColumns(self::TABLE);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
