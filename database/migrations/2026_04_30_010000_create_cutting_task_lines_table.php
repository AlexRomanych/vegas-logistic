<?php

use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'cutting_task_lines';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Связь с Основным СЗ
            $table->foreignId('cutting_task_id')
                ->comment('Ссылка на Основное СЗ')
                ->nullable(false)
                ->constrained('cutting_tasks')
                ->cascadeOnDelete();                    // __ Удалили СЗ - удалили все строки

            // __ Связь с Содержимым Основной Заявки (OrderLine)
            $table->foreignId('order_line_id')
                ->comment('Ссылка на записи Заявки')
                ->nullable(false)
                ->constrained('order_lines')
                ->cascadeOnDelete();                    // __ Удалили запись в Заявке - удалили все строки

            // __ Делаем связь на само себя, потому что сама запись может быть разбита на несколько частей (Крышка + Боковина)
            $table->foreignId('parent_id')
                ->comment('Ссылка на основную запись СЗ')
                ->nullable()
                ->constrained(self::TABLE_NAME, 'id')
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

            // __ Ставим ограничение уникальности по позиции (порядковый номер Элемента в сочетании с id - Части СЗ - CuttingTask)
            // __ должно быть уникальным
            //$table->unique(['cutting_task_id', 'position']); // __ Пока отключаем

            // __ Ставим ограничение уникальности по позиции (порядковый номер Элемента в сочетании с id - Части СЗ - CuttingTask)
            // __ должно быть уникальным в рамках одного дня. Пока оставляем так
            // $table->unique(['cutting_task_id', 'position_day']);

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

            // __ Причина невыполнения
            $table->text('false_history')->nullable()->comment('История невыполнения');

            // __ Стол для раскроя (Определяем в момент создания СЗ)
            $table->string('table')->nullable()->comment('Стол для раскроя');

            // __ Признак того, что сама запись - есть действие к закрою Крышка
            $table->boolean('is_panel')->nullable()->comment('Признак того, что это Крышка');

            // __ Признак того, что у записи есть Крышка
            $table->boolean('has_panel')->nullable()->comment('Признак того, что у записи есть Крышка');

            // __ Признак того, что сама запись - есть действие к закрою Боковина
            $table->boolean('is_side')->nullable()->comment('Признак того, что это Боковина>');

            // __ Признак того, что у записи есть Боковина
            $table->boolean('has_side')->nullable()->comment('Признак того, что у записи есть Боковина>');

            // __ Ткань из спецификации
            $table->jsonb('fabric_construct')->nullable(false)->default('[]')->comment('Крой');

            // __ Крой (деталька типа 159x199)
            $table->string('cut')->nullable()->comment('Крой');

            // __ Угол
            $table->string('angle')->nullable()->comment('Угол');

            // --- Трудозатраты на стол в момент создания СЗ в секундах

            // __ Трудозатраты на стол
            $table->unsignedInteger('time')
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты');
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
