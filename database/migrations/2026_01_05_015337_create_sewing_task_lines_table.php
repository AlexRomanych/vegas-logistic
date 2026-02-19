<?php

use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'sewing_task_lines';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Связь с Основным СЗ
            $table->foreignId('sewing_task_id')
                ->comment('Ссылка на Основное СЗ')
                ->nullable(false)
                ->constrained('sewing_tasks')
                ->cascadeOnDelete();                    // __ Удалили СЗ - удалили все строки

            // __ Связь с Содержимым Основной Заявки (OrderLine)
            $table->foreignId('order_line_id')
                ->comment('Ссылка на записи Заявки')
                ->nullable(false)
                ->constrained('order_lines')
                ->cascadeOnDelete();                    // __ Удалили запись в Заявке - удалили все строки

            // __ Количество. При разбиении СЗ на строки оно будет складываться из таких же частей
            $table->unsignedInteger('amount')
                ->nullable(false)
                ->default(0)
                ->comment('Количество одной части при разбиении записи Заявки на части или все количество в Заявке');

            // __ Фантом (призрак, фейк, фиктивный)
            // __ Поле, которое показывает, на что подменять свойства той или иной модели в записи
            $table->string('phantom')
                ->nullable()
                ->comment('Подмена свойств модели');

            // __ Фантом (призрак, фейк, фиктивный)
            // __ Поле, которое показывает, на что подменять свойства той или иной модели в записи
            $table->jsonb('phantom_json')
                ->nullable(false)
                ->default('[]')
                ->comment('Подмена свойств модели в JSON');

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

            // __ Ставим ограничение уникальности по позиции (порядковый номер Элемента в сочетании с id - Части СЗ - SewingTask)
            // __ должно быть уникальным
            $table->unique(['sewing_task_id', 'position']);

            // __ Ставим ограничение уникальности по позиции (порядковый номер Элемента в сочетании с id - Части СЗ - SewingTask)
            // __ должно быть уникальным в рамках одного дня. Пока оставляем так
            // $table->unique(['sewing_task_id', 'position_day']);

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
            $table->string('false_reason')->nullable()->comment('Причина невыполнения');

            // __ Причина невыполнения
            $table->string('false_history')->nullable()->comment('История невыполнения');

            // --- Трудозатраты на Швейную машину в момент создания СЗ в секундах

            // __ Трудозатраты на ШМ отдельное поле
            $table->unsignedInteger('time')
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты на УШМ, секунды');

            // !!! Пока не используем
            /*
            // __ Суммарное время
            $defaultLaborTime = json_encode([
                SewingTask::FIELD_UNIVERSAL  => 0,
                SewingTask::FIELD_AUTO       => 0,
                SewingTask::FIELD_SOLID_HARD => 0,
                SewingTask::FIELD_SOLID_LITE => 0,
                SewingTask::FIELD_UNDEFINED  => 0,
            ]);

            $table->jsonb('time_labor')
                ->nullable(false)
                ->default($defaultLaborTime)
                ->comment('Трудозатраты в момент создания СЗ, секунды');



            // __ УШМ
            $table->unsignedInteger(SewingTask::FIELD_UNIVERSAL)
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты на УШМ, секунды');

            // __ АШМ
            $table->unsignedInteger(SewingTask::FIELD_AUTO)
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты на AШМ, секунды');

            // __ Глухие Сложные
            $table->unsignedInteger(SewingTask::FIELD_SOLID_HARD)
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты на Глухие Сложные, секунды');

            // __ Глухие Простые
            $table->unsignedInteger(SewingTask::FIELD_SOLID_LITE)
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты на Глухие Простые, секунды');

            // __ Неопознанные
            $table->unsignedInteger(SewingTask::FIELD_UNDEFINED)
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты на Неопознанные, секунды');
*/
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
