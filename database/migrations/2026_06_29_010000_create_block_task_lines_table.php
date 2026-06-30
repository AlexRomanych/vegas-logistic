<?php

/** @noinspection DuplicatedCode */
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'block_task_lines';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Связь с Основным СЗ Блоков
            $table->foreignId('block_task_id')
                ->index()
                ->comment('Ссылка на Основное СЗ')
                ->nullable(false)
                ->constrained('block_tasks')
                ->cascadeOnDelete();                    // __ Удалили СЗ - удалили все строки

            // __ Связь с Блоком из Справочника Блоков
            $table->string('block_code_1c', CODE_1C_LENGTH)
                ->index()
                ->comment('Ссылка на Блок');
            $table->foreign('block_code_1c')
                ->references(CODE_1C)
                ->on('blocks');
                /*->nullOnDelete()*/;

            // __ Копия Ссылки на Блок при удалении
            $table->string('block_code_1c_copy')->nullable(false)->comment('Копия Ссылки на Блок при удалении');

            // __ Название Блока (для визуализации при разработке)
            $table->string('block_name')->nullable(false)->comment('Копия Ссылки на Блок при удалении');

            // __ Связь с Содержимым Основной Заявки (OrderLine)
            // __ Связь будет только односторонней, в момент создания будем определять
            // __ набор ids у OrderLine и записывать в jsonb: ['id': amount],
            // __ а одинаковые блоки - схлопываем
            $table->jsonb('order_line_ids')
                ->nullable(false)
                ->comment('Ссылка на записи Заявки');

            // __ Количество. При разбиении СЗ на строки оно будет складываться из таких же частей
            $table->unsignedInteger('amount')
                ->nullable(false)
                ->default(0)
                ->comment('Количество Деталей для данной строки в Заявке');

            // __ Линия производства (Определяем в момент создания СЗ)
            $table->string('line')->nullable()->comment('Линия производства');

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

            // __ История Причин невыполнения
            $table->text('false_history')->nullable()->comment('История невыполнения');


            // --- Трудозатраты на стол в момент создания СЗ в секундах

            // __ Трудозатраты на единицу на стол в момент создания (скорость)
            $table->double('productivity')
                ->nullable(false)
                ->default(0.0)
                ->comment('Трудозатраты на единицу на стол в момент создания (скорость), кв.м./час');

            // __ Площадь блока на единицу в момент создания
            $table->double('square')
                ->nullable(false)
                ->default(0)
                ->comment('Площадь блока на единицу в момент создания, кв.м.');

            // __ Общие Трудозатраты на стол в момент создания
            $table->double('time')
                ->nullable(false)
                ->default(0)
                ->comment('Трудозатраты, час');

            // __ Трудозатраты на момент создания записи
            $table->json('productivity_json')->nullable()->comment('Трудозатраты на момент создания записи');

            // __ Ссылка на родитель при разбиении на части
            $table->unsignedBigInteger('ref_id')->nullable()->comment('Ссылка на родитель при разбиении на части');

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
