<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'assembly_task_line_sectors';

    /** @noinspection DuplicatedCode */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Связь с Контекстом СЗ Сборки (AssemblyTaskLine)
            $table->foreignId('assembly_task_line_id')
                ->index()
                ->comment('Ссылка на Контекстную строку Основного СЗ')
                ->nullable(false)
                ->constrained('assembly_task_lines')
                ->cascadeOnDelete();                    // __ Удалили СЗ - удалили все строки

            // __ Связь с Содержимым Основной Заявки (OrderLine)
            $table->foreignId('order_line_id')
                ->index()
                ->comment('Ссылка на записи Заявки')
                ->nullable(false)
                ->constrained('order_lines')
                ->cascadeOnDelete();                    // __ Удалили запись в Заявке - удалили все строки

            // __ Участок производства
            $table->string('sector')->nullable()->comment('Участок производства');

            // __ Физические Размеры элемента, см
            $table->unsignedSmallInteger('width')->nullable()->comment('Ширина элемента, см');
            $table->unsignedSmallInteger('length')->nullable()->comment('Длина элемента, см');
            $table->unsignedSmallInteger('height')->nullable()->comment('Высота элемента, см');

            // __ Код материала из 1С
            $table->string('material_code_1c')->nullable(false)->comment('Код материала из 1С');

            // __ Название материала из 1С
            $table->string('material_name')->nullable(false)->comment('Название материала из 1С');

            // __ Физические Размеры детали, мм
            $table->unsignedSmallInteger('detail_width')->nullable()->comment('Ширина детали, мм');
            $table->unsignedSmallInteger('detail_length')->nullable()->comment('Длина детали, мм');
            $table->unsignedSmallInteger('detail_height')->nullable()->comment('Высота детали, мм');

            // __ Расход
            $table->float('expense')->nullable(false)->default(0.0)->comment('Расход');
            $table->float('expense_per_pic')->nullable(false)->default(0.0)->comment('Расход на единицу');

            // __ Остаток
            $table->float('rest')->nullable(false)->default(0.0)->comment('Остаток');
            $table->float('rest_per_pic')->nullable(false)->default(0.0)->comment('Остаток на единицу');

            // __ Общее количество
            $table->float('total')->nullable(false)->default(0.0)->comment('Общее количество');
            $table->float('total_per_pic')->nullable(false)->default(0.0)->comment('Общее количество на единицу');

            // __ Делаем связь на само себя, потому что сама запись может быть разбита на несколько частей (Крышка + Боковина)
            //$table->foreignId('parent_id')
            //    ->comment('Ссылка на основную запись СЗ')
            //    ->nullable()
            //    ->constrained(self::TABLE_NAME, 'id')
            //    ->cascadeOnDelete();                    // __ Удалили запись в Заявке - удалили все строки

            // __ Количество Деталей для данной строки в Заявке
            $table->unsignedInteger('amount')
                ->nullable(false)
                ->default(0)
                ->comment('Количество Деталей для данной строки в Заявке');

            // __ Количество Повторений Деталей для данной строки в Заявке
            $table->unsignedInteger('count')
                ->nullable(false)
                ->default(1)
                ->comment('Количество Повторений Деталей для данной строки в Заявке');

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

            // __ Порядок в списке по порядку (позиция) в конкретном СЗ
            $table->bigInteger('position')
                ->nullable(false)
                ->default(0)
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

            // __ История невыполнения
            $table->jsonb('false_history')->nullable()->comment('История невыполнения');

            // __ Трудозатраты
            $table->unsignedInteger('time')
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты');

            // __ Трудозатраты на момент создания записи. Для Моделей, например 314.314
            $table->json('time_json')->nullable()->comment('Трудозатраты на момент создания записи');

            // __ Output Scope
            $table->text('outputs')->nullable()->comment('Output Scope');
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
