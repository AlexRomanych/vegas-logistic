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
            $table->id();

            // __ Связь с клиентом
            $table->foreignIdFor(Client::class)
                ->comment('Клиент к которому относится заявка')
                ->constrained();

            $table->string('order_no')->nullable(false)->comment('Номер заявки');

            // __ Связь с типом заявки (серийная, гарантийная и т.д.)
            $table->foreignIdFor(OrderType::class)
                ->comment('Тип заявки')
                ->constrained();

            $table->jsonb('amounts')->nullable()->comment('Количество изделий');

            // __ Обходим ситуацию, когда заявки начинаются каждый год для каждого клиента с 1
            $table->date('period')->nullable(false)->comment('Период, к которому относится заявка');

            $table->string('extended_meta')->nullable()->comment('Расширенная информация');

            $table->jsonb('history')->nullable()->comment('История изменений');

            $table->dateTime('load_at')->nullable(false)->comment('Дата загрузки на складе Вегас');
            $table->dateTime('unload_at')->nullable()->comment('Дата разгрузки на складе Клиента');

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
