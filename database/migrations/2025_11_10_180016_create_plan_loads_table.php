<?php

use App\Models\Client;
use App\Models\Order\OrderType;
use App\Traits\AddCommonColumnsInTableTrait;
use Carbon\Carbon;
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

            // line --------------------------------------------------------------------------
            // line ----- Поля не обязательные, дублируют поля в заявках,  -------------------
            // line------ по ним можно привязывать заявки к плану загрузки -------------------
            // line --------------------------------------------------------------------------
            // Relations: Связь с клиентом (необязательный, по нему и номеру можно привязывать заявки)
            $table->foreignIdFor(Client::class)
                ->nullable()
                ->comment('Клиент к которому относится заявка')
                ->constrained()
                ->nullOnDelete();

            // __ Номер заявки (необязательный, по нему и клиенту можно привязывать заявки)
            $table->decimal('order_no_num')->nullable()->comment('Номер заявки в числовом формате');
            $table->string('order_no_str')->nullable()->comment('Номер заявки в строковом формате');
            $table->string('order_no_origin')->nullable()->comment('Номер заявки оригинальный в строковом формате (из 1С)');

            // __ Обходим ситуацию, когда заявки начинаются каждый год для каждого клиента с 1
            $table->date('period')->nullable()->comment('Период, к которому относится заявка');

            // __ Порядковый номер отгрузки, чтобы различать отгрузки в рамках одного периода (пока дня)
            $table->unsignedSmallInteger('unload_no')->nullable()->comment('Порядковый номер отгрузки в дне');
            // line --------------------------------------------------------------------------

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

            // __ Дата разгрузки на складе Клиента
            $table->dateTime('unload_at')->nullable()->comment('Дата разгрузки на складе Клиента');

            // __ Флаг, который говорит о том, что были изменены даты загрузки на складе Вегас и ситуация требует разрешения
            $table->boolean('load_at_dates_conflict')
                ->nullable(false)
                ->default(false)
                ->comment('Конфликт при изменении даты загрузки на складе Вегас');

            // __ Порядок Загрузки в плане
            $table->unsignedSmallInteger('load_position')
                ->nullable()
                ->comment('Порядок заявки в плане');

            $table->jsonb('history')->nullable()->comment('История изменений');
            $table->string('extended_meta')->nullable()->comment('Расширенная информация');

            // Relations: __ ID склада (На будущее, пока не используется)
            $table->unsignedInteger('storage_id')->nullable()->comment('ID склада');


            // Warning: Этот код переехал в миграцию Orders
            // TODO: Закомментировать после переноса

            /*
            // Relations: Связь с типом заявки (серийная, гарантийная и т.д.)
            $table->foreignIdFor(OrderType::class)
                ->comment('Тип заявки')
                ->constrained();

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

            */

        });

        $this->addCommonColumns(self::TABLE_NAME);

        // __ Индекс для JSONB для поля total (важно для производительности)
        DB::statement("CREATE INDEX amount_total_idx ON " . self::TABLE_NAME . " USING btree (CAST(amounts->>'total' as numeric))");

        DB::table(self::TABLE_NAME)->insert([
            'id'              => 0,
            'client_id'       => 0,
            'order_no_num'    => 0,
            'order_no_str'    => '0',
            'order_no_origin' => '0',
            'period'          => Carbon::parse('2000-01-01'),
            'amounts'         => '{"amounts":{"mattresses":0,"up_covers":0,"averages":0,"covers":0,"children":0,"formats":0,"unknowns":0,"totals":0}}',
            'load_at'         => Carbon::parse('2000-01-01'),
            'created_at'      => now(),
            'updated_at'      => now(),
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
