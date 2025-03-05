<?php

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fabric_orders', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('code_1C')->nullable(false)->comment('Код заявки по 1С');
            $table->timestamp('time_1C')->nullable()->comment('Дата внесения заявки в 1С');
            $table->string('order_no')->nullable(false)->default('')->comment('Номер заявки');
            $table->date('expense_date')->nullable(false)->default(now())->comment('Дата расхода ПС');
            $table->string('type')->nullable()->comment('Тип заявки из которой был сделан отчет');   // Тип заявки - из оригинальной заявки, из заявки на чехлы или заявки на аксессуары
            $table->boolean('is_closed')->nullable(false)->default(false)->comment('Закрыта ли заявка (Передана в производство)');
            $table->timestamp('closed_at')->nullable()->comment('Дата закрытия заявки');
            $table->string('raw_text')->nullable()->comment('Оригинальный текст заявки из отчета СВПМ');

            $table->string('description')->nullable()->comment('Описание заявки или дополнительная информация');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->timestamps();

            $table->unsignedBigInteger('closed_by_user_id')->nullable()->comment('Ссылка на закрываемого');    // Объявляем столбец для внешнего ключа юзера, который закрывает заявку

            // Ссылка на закрываемого
            $table->foreign('closed_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(Client::class)->nullable()->comment('Ссылка на клиента')->constrained()->nullOnDelete();     // Ссылка на клиента
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_orders');
    }
};
