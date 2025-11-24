<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'business_processes';
    private const TABLE_NODES_NAME = 'business_process_nodes';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Название бизнес-процесса
            $table->string('name')
                ->nullable(false)
                ->unique(true)
                ->comment('Название бизнес-процесса');

            // __ Тип бизнес-процесса
            $table->string('type')
                ->nullable()
                ->comment('Тип бизнес-процесса');

            // __ Принадлежность бизнес-процесса к какому-либо участку (Производство, Отдел продаж и т.д.)
            $table->string('belongs_to')
                ->nullable()
                ->comment('Принадлежность бизнес-процесса к какой-либо производственно-административной единице');

            // __ Стартовый узел бизнес-процесса
            $table->foreignId('start_node_id')
                ->nullable()
                ->comment('Стартовый узел бизнес-процесса')
                ->constrained(self::TABLE_NODES_NAME, 'id');

            // __ Финишный узел бизнес-процесса
            $table->foreignId('finish_node_id')
                ->nullable()
                ->comment('Финишный узел бизнес-процесса')
                ->constrained(self::TABLE_NODES_NAME, 'id');

            // __ Опорный узел бизнес-процесса (Например, для процесса "Схема движения заявки" - это узел, от
            // __ которого начинается отсчет всех временных интервалов - это узел "Загрузка на складе")
            // __ Финишный узел бизнес-процесса
            $table->foreignId('reference_node_id')
                ->nullable()
                ->comment('Опорный узел бизнес-процесса')
                ->constrained(self::TABLE_NODES_NAME, 'id');

            $table->jsonb('meta_extended')->nullable()->comment('Метаданные jsonb');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        // __ Добавляем начальные основные данные
        DB::table(self::TABLE_NAME)->insert([
            'id'         => 0,
            'name'       => 'Дефолтный процесс',
            'active'     => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'              => 'Схема движения заявки',
            'reference_node_id' => 12,      // Загрузка на складе
            'created_at'        => now(),
            'updated_at'        => now()
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
