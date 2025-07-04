<?php

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    const TABLE_NAME = 'fabric_orders';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('code_1C')->nullable(false)->comment('Код заявки по 1С');
            $table->timestamp('time_1C')->nullable()->comment('Дата внесения заявки в 1С');
            $table->string('order_no')->nullable(false)->default('')->comment('Номер заявки');
            $table->date('expense_date')->nullable(false)->default(now())->comment('Дата расхода ПС');
            $table->string('type')->nullable()->comment('Тип заявки из которой был сделан отчет');   // Тип заявки - из оригинальной заявки, из заявки на чехлы или заявки на аксессуары
            $table->boolean('is_closed')->nullable(false)->default(false)->comment('Закрыта ли заявка (Передана в производство)');
            $table->timestamp('closed_at')->nullable()->comment('Дата закрытия заявки');
            $table->string('raw_text')->nullable()->comment('Оригинальный текст заявки из отчета СВПМ');

            // attract: Статус заявки, пока не используем
            $table->unsignedSmallInteger('status')->nullable()->comment('Статус заявки');

            // attract: Отодвигать в конец списка расходов при отображении или нет
            $table->boolean('display_last')->nullable(false)->default(false)->comment('Показывать в конце списка или нет');

            // attract: Список id ПС, которые надо исключить из расчетов расходов
            $table->json('excluded_fabrics')->nullable()->comment('Список id ПС, которые надо исключить из расчетов расходов');

            // attract: признак расхода (если заявка закрыта - признак расхода неактуален, если открыта и false - не учитываем в расчетах)
            $table->boolean('active')->nullable(false)->default(true)->comment('Учитывать расход или нет');

            $table->string('description')->nullable()->comment('Описание заявки или дополнительная информация');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->timestamps();

//            $table->unsignedBigInteger('closed_by_user_id')->nullable()->comment('Ссылка на закрываемого');    // Объявляем столбец для внешнего ключа юзера, который закрывает заявку

            // attract: Ссылка на закрываемого
            $table->foreignId('closed_by_user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // attract: Ссылка на клиента
            $table->foreignIdFor(Client::class)
                ->nullable()
                ->comment('Ссылка на клиента')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
