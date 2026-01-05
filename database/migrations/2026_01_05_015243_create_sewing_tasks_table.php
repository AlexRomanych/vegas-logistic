<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'sewing_tasks';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Связь с Основной Заявкой
            $table->foreignId('order_id')
                ->comment('Ссылка на Заявку')
                ->nullable(false)
                ->constrained('orders')
                ->cascadeOnDelete();

            // __ Порядок выполнения в производственном дне
            // __ Если разные дни, то порядок можно определить по дате
            $table->integer('position')
                ->nullable(false)
                ->default(0)
                ->comment('Порядок выполнения в производственном дне');

            // __ Дата выполнения СЗ
            $table->timestamp('planned_at')
                ->nullable(false)
                ->comment('Дата выполнения СЗ');


            $table->jsonb('meta_ext')->nullable()->comment('Метаданные в формате JSON');
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
