<?php

use App\Models\BusinessProcesses\BusinessProcess;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    /**
     * ___ Промежуточная таблица для связи "многие ко многим"
     * ___ сама на себя для операционных узлов
     * ___ Цель: смоделировать граф Бизнес-процесса
     */

    private const TABLE_NAME = 'business_process_nodes_connections';
    private const TABLE_REFERENCE_NAME = 'business_process_nodes';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Ссылка на Бизнес-процесс
            $table->foreignIdFor(BusinessProcess::class)
                ->nullable(false)
                ->default(0)
                ->comment('Ссылка на Бизнес-процесс')
                ->constrained();


            // __ Ссылка на предыдущий Бизнес узел (Узел-источник)
            $table->foreignId('previous_node_id')
                ->nullable()
                ->comment('Ссылка на предыдущий Бизнес узел (Узел-источник)')
                ->constrained(self::TABLE_REFERENCE_NAME)
                ->cascadeOnDelete();

            // __ Ссылка на следующий Бизнес узел (Узел-назначение)
            $table->foreignId('next_node_id')
                ->nullable()
                ->comment('Ссылка на следующий Бизнес узел (Узел-назначение)')
                ->constrained(self::TABLE_REFERENCE_NAME)
                ->cascadeOnDelete();

            // __ Тип связи: 'sequential' (последовательная), 'parallel' (параллельная)
            // $table->enum('type', ['sequential', 'parallel'])->default('sequential');

            // __ Порядок для сортировки параллельных/последовательных участков
            $table->unsignedSmallInteger('order')->nullable();

            // __ Обеспечение уникальности пары (чтобы не было дубликатов связей в одинаковых бизнес-процессах)
            $table->unique(['previous_node_id', 'next_node_id', 'business_process_id']);

            // $table->timestamps();

        });

        $this->addCommonColumns(self::TABLE_NAME);

        // __ Добавляем начальные основные данные для формирования графа Бизнес-процесса: Схема движения заявки
        DB::table(self::TABLE_NAME)->insert([
            ['previous_node_id' => null, 'next_node_id' => 1, 'business_process_id' => 1, 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 1, 'next_node_id' => 2, 'business_process_id' => 1, 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 2, 'next_node_id' => 6, 'business_process_id' => 1, 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 6, 'next_node_id' => 7, 'business_process_id' => 1, 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 7, 'next_node_id' => 8, 'business_process_id' => 1, 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 8, 'next_node_id' => 9, 'business_process_id' => 1, 'order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 9, 'next_node_id' => 10, 'business_process_id' => 1, 'order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 10, 'next_node_id' => 11, 'business_process_id' => 1, 'order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 11, 'next_node_id' => 12, 'business_process_id' => 1, 'order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['previous_node_id' => 12, 'next_node_id' => null, 'business_process_id' => 1, 'order' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }

};
