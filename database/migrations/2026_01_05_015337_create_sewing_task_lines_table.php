<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

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

            // __ Порядок в списке по порядку (позиция)
            $table->unsignedInteger('position')
                ->nullable(false)
                ->default(1)
                ->comment('Порядок в списке по порядку (позиция)');

            // __ Ставим ограничение уникальности по позиции (порядковый номер Элемента в сочетании с id - Части СЗ - SewingTask)
            // __ должно быть уникальным
            $table->unique(['sewing_task_id', 'position']);

            // __ Время завершения работы по данному элементу в СЗ
            $table->timestamp('finished_at')
                ->nullable()
                ->comment('Время завершения работы по данному элементу в СЗ');

            // __ Связь с Ответственным за выполнение
            $table->foreignId('finished_by')
                ->comment('Ответственный за выполнение')
                ->nullable()
                ->constrained('workers')
                ->nullOnDelete();

            // __ Причина невыполнения
            $table->string('false_reason')->nullable()->comment('Причина невыполнения');

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
