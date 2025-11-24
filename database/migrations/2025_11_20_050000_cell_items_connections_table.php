<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;
    private const TABLE_NAME = 'cell_items_connections';
    private const TABLE_REFERENCE_NAME = 'cell_items';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Ссылка на предыдущую ПЯ (ПЯ-источник)
            $table->foreignId('previous_cell_id')
                ->nullable()
                ->comment('Ссылка на предыдущую ПЯ (ПЯ-источник)')
                ->constrained(self::TABLE_REFERENCE_NAME)
                ->cascadeOnDelete();

            // __ Ссылка на следующую ПЯ (ПЯ-назначение)
            $table->foreignId('next_cell_id')
                ->nullable()
                ->comment('Ссылка на следующую ПЯ (ПЯ-назначение)')
                ->constrained(self::TABLE_REFERENCE_NAME)
                ->cascadeOnDelete();

            // __ Тип связи: 'sequential' (последовательная), 'parallel' (параллельная)
            // $table->enum('type', ['sequential', 'parallel'])->default('sequential');

            // __ Порядок для сортировки параллельных/последовательных участков
            $table->unsignedSmallInteger('order')->nullable();

            // __ Обеспечение уникальности пары (чтобы не было дубликатов связей)
            $table->unique(['previous_cell_id', 'next_cell_id']);

            // $table->timestamps();

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
