<?php

use App\Models\Client;
use App\Models\Order\OrderType;
use App\Models\Plan\PlanLoad;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'orders';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->comment('Таблица всех заказов');

            $table->id()->from(1);

            // __ Номер заявки в десятичном представлении
            $table->decimal('order_no_num', 8, 3)
                ->nullable(false)
                ->default(0)
                ->comment('Номер заявки в числовом формате');

            // __ Номер заявки в текстовом представлении
            $table->string('order_no_str')
                ->nullable()
                ->comment('Номер заявки в текстовом формате');

            // __ Оригинальный номер заявки - номер, который может придти из 1С
            $table->string('order_no_origin')
                ->nullable()
                ->comment('Оригинальный номер заявки');

            // __ Период (начало месяца), к которому принадлежит заявка, оставляем, пока не знаем, пригодится ли
            // __ Обходим ситуацию, когда заявки начинаются каждый год для каждого клиента с 1
            $table->date('order_period')
                ->nullable()
                ->comment('Период (начало месяца), к которому принадлежит заявка');

            // __ Тип производства (матрасы, аксессуары и т.д.). Это ключевой элемент, по которому мы будем идентифицировать заявки
            // __ Не может быть смешанный. Либо матрасная группа, либо аксессуары, либо что-то еще
            $table->string('elements_type_ref')
                ->nullable()
                ->comment('Тип производства (матрасы, аксессуары и т.д.) - Опорный ключ');

            // __ Тип производства (матрасы, аксессуары и т.д.)
            $table->string('elements_type')
                ->nullable()
                ->comment('Тип производства (матрасы, аксессуары и т.д.)');

            // __ Статус заявки (типа загружена/не загружена/отгружена/закрыта)
            $table->unsignedInteger('status')
                ->nullable(false)
                ->default(0)
                ->comment('Статус заявки (загружена/не загружена/отгружена/закрыта и т.д.)');

            // __ Мета-информация о статусе (кто и когда менял)
            $table->jsonb('status_history')->nullable()->default(json_encode([]))->comment('Мета-информация о статусе');

            // __ Количество изделий (расширенное, для быстроты обмена данными)
            $table->jsonb('amounts')
                ->nullable()
                ->comment('Количество изделий');

            // __ Флаг, указывающий, что заявка является прогнозной
            $table->boolean('is_forecast')
                ->nullable(false)
                ->default(false)
                ->comment('Прогнозная заявка');

            // __ Флаг, указывающий на не отображение заявки в плане
            $table->boolean('shown')
                ->nullable(false)
                ->default(true)
                ->comment('Отображать в плане');

            // __ Включать или нет в статистику при планировании
            // __ Флаг, указывающий на пропуск при расчете сырья, например, если заявка тендерная
            $table->boolean('stat_include')
                ->nullable(false)
                ->default(true)
                ->comment('Включать или нет в статистику при планировании (пропуск при расчете сырья)');

            // __ Позиция загрузки заявки в Плане отгрузок. Выносим сюда, чтобы обеспечить более гибкий порядок управления загрузкой
            // __ Например, загрузить сначала ЛММ 1, потом ЛММ 2, а потом ЛММ 1 аксессуары
            $table->unsignedInteger('load_position')->nullable()->comment('Позиция загрузки заявки в Плане отгрузок');

            // __ Ответственный, кто вносил заявку в 1С
            $table->string('responsible')
                ->nullable()
                ->comment('Ответственный (кто вносил заявку в 1С');

            // __ Даты загрузки заявки на складе из 1С
            $table->timestamp('manager_load_date')
                ->nullable()
                ->comment('Дата загрузки на складе из плана в 1С');

            // __ Обработка заявки в 1С службой КС
            $table->timestamp('manager_start')->nullable()->comment('Создание документа Заказ для обмена (момент начала загрузки менеджером заявки в 1С)');
            $table->timestamp('manager_end')->nullable()->comment('Установка статуса Проверено КС (момент окончания загрузки менеджером заявки в 1С)');

            // __ Обработка заявки в 1С службой КБ
            $table->timestamp('design_start')->nullable()->comment('Перевод заявки в базу Заказов (момент начала обработки заявки КБ)');
            $table->timestamp('design_end')->nullable()->comment('Установка статуса Проверено КБ (момент окончания обработки заявки КБ)');

            // __ Номер заявки из 1С
            $table->string('no_1c')->nullable()->comment('Номер заявки из 1С');

            // __ Код заявки из 1С (длинный строковый код заявки)
            // __ Заносим, чтобы при обновлении заявки через загрузку с диска определять, что заявка уже была загружена
            // __ и не загружать ее повторно, если не удастся определить ее по Клиенту, Номеру, Типу изделий и Периоду
            $table->string('code_1c')->nullable()->comment('Код заявки из 1С');

            // __ Код заявки-основания из базы Заказ для обмена из 1С
            $table->string('base_order_code_1c')->nullable()->comment('Код заявки-основания из базы Заказ для обмена из 1С');

            // __ Комментарий из 1С
            $table->string('comment_1c')->nullable()->comment('Комментарий из 1С');

            // __ Клиент из 1С - Код клиента из 1С
            $table->string('client_code_1c')->nullable()->comment('Код клиента из 1С');

            // __ Клиент из 1С - Название клиента из 1С
            $table->string('client_name_1c')->nullable()->comment('Название клиента из 1С');

            // __ Дополнительные поля
            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность');
            $table->boolean('active_logistic')->nullable(false)->default(true)->comment('Актуальность для ОЗ');
            $table->string('service')->nullable()->comment('Сервисная для хранения различной информации в json формате');
            $table->string('service_ext')->nullable()->comment('Расширенная сервисная для хранения различной информации в json формате');
            $table->string('extended_meta')->nullable()->comment('Расширенная информация');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->json('meta')->nullable()->comment('Метаданные');
            $table->jsonb('history')->nullable()->comment('История изменений');
            $table->jsonb('meta_ext')->nullable()->comment('Расширенные метаданные');

            $table->timestamps();

            // Relations: Связь с клиентом
            $table->foreignIdFor(Client::class)
                ->nullable(false)
                ->default(0)
                ->comment('Клиент к которому относится заявка')
                ->constrained()
                ->nullOnDelete();

            // Relations: Связь с типом заявки (серийная, гарантийная и т.д.)
            $table->foreignIdFor(OrderType::class)
                ->nullable(false)
                ->default(0)
                ->comment('Тип заявки (серийная, гарантийная и т.д.)')
                ->constrained();

            // Relations: Связь с Загрузкой в плане
            $table->foreignIdFor(PlanLoad::class)
                ->nullable()
                ->comment('Загрузка на складе Вегас (Загрузка в плане)')
                ->constrained();

            // Warn: Ставим уникальность на номер заявки + тип элементов (матрасы/аксессуары и т.д.) + период + клиент,
            // Warn: т.к. заявки могут быть с одинаковым номером, но в разных периодах
            $table->unique(['client_id', 'order_no_num', 'order_period', 'elements_type_ref']);



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


            // Relations: __ ID склада (На будущее, пока не используется)
            $table->unsignedInteger('storage_id')->nullable()->comment('ID склада');




            // $table->timestamp('load_date')->nullable(false)->default(now())->comment('дата загрузки на складе Вегаса');
            // $table->timestamp('unload_date')->nullable()->comment('дата разгрузки на складе клиента');
            // $table->string('unload_place')->nullable()->comment('место разгрузки');
            // $table->timestamp('manuf_date')->nullable()->comment('дата производства из ЕПС');

            // __ Порядок заявки в плане - Перенесем в каждый план отдельно
            // $table->unsignedSmallInteger('position')
            //     ->nullable()
            //     ->comment('Порядок заявки в плане');

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
