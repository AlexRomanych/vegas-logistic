<?php

use App\Models\Client;
use App\Models\Order\OrderType;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'plan_loads';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Количество изделий для отгрузки
            $table->jsonb('amounts')->nullable()->comment('Количество изделий');

            // __ Дата загрузки на складе Вегас
            $table->dateTime('load_at')
                ->nullable(false)
                ->comment('Дата загрузки на складе Вегас');

            // __ Дата загрузки на складе Вегас предыдущая, в случае изменения
            $table->dateTime('load_at_previous')
                ->nullable()
                ->comment('Предыдущая дата загрузки на складе Вегас');

            // __ Флаг, который говорит о том, что были изменены даты загрузки на складе Вегас и ситуация требует разрешения
            $table->boolean('load_at_dates_conflict')
                ->nullable(false)
                ->default(false)
                ->comment('Конфликт при изменении даты загрузки на складе Вегас');

            $table->string('extended_meta')->nullable()->comment('Расширенная информация');


            // Warning: Этот код переехал в миграцию Orders
            // TODO: Закомментировать после переноса

            // Relations: Связь с клиентом
            $table->foreignIdFor(Client::class)
                ->comment('Клиент к которому относится заявка')
                ->constrained();

            $table->string('order_no')->nullable(false)->comment('Номер заявки');

            // Relations: Связь с типом заявки (серийная, гарантийная и т.д.)
            $table->foreignIdFor(OrderType::class)
                ->comment('Тип заявки')
                ->constrained();

            // __ Обходим ситуацию, когда заявки начинаются каждый год для каждого клиента с 1
            $table->date('period')->nullable(false)->comment('Период, к которому относится заявка');

            // __ Порядок заявки в плане
            $table->unsignedSmallInteger('load_position')
                ->nullable()
                ->comment('Порядок заявки в плане');

            // __ Дата разгрузки на складе Клиента
            $table->dateTime('unload_at')->nullable()->comment('Дата разгрузки на складе Клиента');

            // __ Флаг, указывающий, что заявка является прогнозной
            $table->boolean('is_forecast')
                ->nullable(false)
                ->default(false)
                ->comment('Прогнозная заявка');

            // __ Флаг, указывающий на пропуск при расчете сырья, например, если заявка тендерная
            $table->boolean('miss_calculate')
                ->nullable(false)
                ->default(false)
                ->comment('Пропуск при расчете сырья');

            // __ Флаг, указывающий на не отображение заявки в плане
            $table->boolean('shown')
                ->nullable(false)
                ->default(true)
                ->comment('Отображать в плане');

            $table->jsonb('history')->nullable()->comment('История изменений');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        // __ Индекс для JSONB для поля total (важно для производительности)
        DB::statement("CREATE INDEX amount_total_idx ON " . self::TABLE_NAME . " USING btree (CAST(amounts->>'total' as numeric))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
