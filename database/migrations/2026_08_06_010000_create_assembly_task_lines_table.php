<?php

use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'assembly_task_lines';

    /** @noinspection DuplicatedCode */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Связь с Основным СЗ
            $table->foreignId('assembly_task_id')
                ->index()
                ->comment('Ссылка на Основное СЗ')
                ->nullable(false)
                ->constrained('assembly_tasks')
                ->cascadeOnDelete();                    // __ Удалили СЗ - удалили все строки

            // __ Связь с Содержимым Основной Заявки (OrderLine)
            $table->foreignId('order_line_id')
                ->index()
                ->comment('Ссылка на записи Заявки')
                ->nullable(false)
                ->constrained('order_lines')
                ->cascadeOnDelete();                    // __ Удалили запись в Заявке - удалили все строки

            // __ Количество. При разбиении СЗ на строки оно будет складываться из таких же частей
            $table->unsignedInteger('amount')
                ->nullable(false)
                ->default(0)
                ->comment('Количество Деталей для данной строки в Заявке');

            // __ Порядок в списке по порядку (позиция) в конкретном СЗ
            $table->bigInteger('position')
                ->nullable(false)
                ->default(1)
                ->comment('Порядок в списке по порядку (позиция) в конкретном СЗ');

            // __ Порядок в списке по порядку (позиция) в дне
            // __ Ситуация когда в одном дне несколько СЗ и они объединяются в одно
            $table->bigInteger('position_day')
                ->nullable(false)
                ->default(0)
                ->comment('Порядок в списке по порядку (позиция) в дне');

            // __ Время завершения работы по данному элементу в СЗ
            $table->timestamp('finished_at')
                ->nullable()
                ->comment('Время завершения работы по данному элементу в СЗ');

            // __ Время завершения работы по данному элементу в СЗ
            $table->timestamp('false_at')
                ->nullable()
                ->comment('Время метки невыполнения');

            // __ Связь с Ответственным за выполнение
            $table->foreignId('finished_by')
                ->comment('Ответственный за выполнение')
                ->nullable()
                ->constrained('workers')
                ->nullOnDelete();

            // __ Причина невыполнения
            $table->text('false_reason')->nullable()->comment('Причина невыполнения');

            // __ История невыполнения
            $table->jsonb('false_history')->nullable()->comment('История невыполнения');

            // __ Трудозатраты
            $table->unsignedInteger('time')
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты');

            // __ Трудозатраты на момент создания записи. Для Моделей, например 314.314
            $table->json('time_json')->nullable()->comment('Трудозатраты на момент создания записи');

            // __ Линия производства 'Lamit' или 'Стол'
            $table->string('manuf_line')->nullable()->default(AssemblyTask::ASSEMBLY_TASK_SECTOR_TABLE)->comment('Линия производства Lamit или Стол');

            // __ Фантом (призрак, фейк, фиктивный)
            // __ Поле, которое показывает, на что подменять свойства той или иной модели в записи
            $table->string('phantom')
                ->nullable()
                ->comment('Подмена свойств модели');

            // __ Фантом (призрак, фейк, фиктивный)
            // __ Поле, которое показывает, на что подменять свойства той или иной модели в записи
            $table->jsonb('phantom_json')
                ->nullable()
                ->comment('Подмена свойств модели в JSON');
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
