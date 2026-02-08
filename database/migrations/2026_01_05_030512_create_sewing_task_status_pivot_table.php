<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;
    private const TABLE_NAME = 'sewing_task_status_pivot';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Внешние ключи (constrained автоматически ищет таблицы orders и statuses)
            $table->foreignId('sewing_task_id')
                ->nullable(false)
                ->comment('Связь с СЗ')
                ->constrained('sewing_tasks', 'id')
                ->cascadeOnDelete();

            $table->foreignId('sewing_task_status_id')
                ->nullable(false)
                ->comment('Связь со статусом')
                ->constrained('sewing_task_statuses', 'id')
                ->cascadeOnDelete();

            // __ Кто назначил и кто делает
            $table->foreignId('created_by')
                ->nullable()
                ->comment('Кто назначил')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('executor_id')
                ->nullable()
                ->comment('Кто делает')
                ->constrained('workers')
                ->nullOnDelete();

            // __ Дата установки статуса
            $table->timestamp('set_at')->nullable()->comment('Дата установки статуса');

            // __ Дополнительные поля для бизнес-логики
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('planned_finished_at')->nullable()->comment('Планируемая дата завершения');
            $table->integer('duration')->nullable()->comment('Длительность в минутах');

            // __ Уникальный индекс: одна заявка — один конкретный статус
            // __ Пока не ставим, потому что будем писать всю историю
            // $table->unique(['sewing_task_id', 'sewing_task_status_id']);

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
