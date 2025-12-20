<?php

use App\Models\Order\Order;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'orders_lines';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Размер элемента
            $table->string('size')
                ->nullable()
                // ->nullable(false)
                // ->default('0x0x0')
                ->comment('Размер матраса');

            // __ Имя модели, оно не нужное, оставляем для удобства
            $table->string('model_name')
                ->nullable(false)
                ->comment('Оригинальное имя модели в заказе');

            // Relations: Связь с Моделью
            $table->string('model_code_1c')
                ->nullable(false)
                ->comment('Код модели из 1С');;
            $table->foreign('model_code_1c')
                ->references(CODE_1C)
                ->on('models');

            // __ Количество элементов
            $table->integer('amount')
                ->nullable(false)
                ->default(0)
                ->comment('Количество элементов');

            // __ Ткань
            $table->string('textile')
                ->nullable()
                ->comment('Вид ткани элемента в заказе');

            // __ Комментарий
            $table->string('composition')->nullable()->comment('Состав/Комментарий');
            $table->string('describe_1')->nullable()->comment('Описание 1');
            $table->string('describe_2')->nullable()->comment('Описание 2');
            $table->string('describe_3')->nullable()->comment('Описание 3');

            // Relations: Связь с Заказом
            $table->foreignIdFor(Order::class)
                ->nullable(false)
                ->comment('Заказ, к которому относится элемент')
                ->constrained()
                ->cascadeOnDelete();
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
