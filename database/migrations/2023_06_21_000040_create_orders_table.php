<?php

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->comment('Таблица всех заказов');

            $table->id();

            $table->decimal('no_num')->nullable(false)->default(0)->comment('номер заявки в числовом формате');
            $table->string('no_origin')->nullable()->comment('оригинальный номер заявки');

            $table->timestamp('plan_period')->nullable()->comment('период (начало месяца), к которому принадлежит заявка');

            $table->integer('status')->nullable()->default(0)->comment('статус заявки (типа загружена-не загружена)');

            $table->timestamp('load_date')->nullable(false)->default(now())->comment('дата загрузки на складе Вегаса');
            $table->timestamp('unload_date')->nullable()->comment('дата разгрузки на складе клиента');

            $table->string('unload_place')->nullable()->comment('место разгрузки');

            $table->string('description')->nullable()->comment('примечания к заявке');

            //************************************************************************************************************************
            // Попробовать сделать из этих столбцов формат Json
            $table->string('responsible')->nullable()->comment('ответственный (кто вносил заявку в 1С');

            $table->timestamp('manager_load_date')->nullable()->comment('дата загрузки на складе из 1С');
            $table->timestamp('manager_start')->nullable()->comment('момент начала загрузки менеджером заявки в 1С');
            $table->timestamp('manager_end')->nullable()->comment('момент окончания загрузки менеджером заявки в 1С');

            $table->timestamp('design_start')->nullable()->comment('момент начала обработки заявки КБ (ранее ОДСП)');
            $table->timestamp('design_end')->nullable()->comment('момент окончания обработки заявки КБ (ранее ОДСП)');

            $table->timestamp('manuf_date')->nullable()->comment('дата производства из ЕПС');

            $table->string('comment')->nullable()->comment('комментарий к заявке из 1С');

            $table->json('service_json')->nullable()->comment('сервисная для хранения различной информации в json формате');
            //************************************************************************************************************************

            $table->string('meta')->nullable()->comment('сервисная для хранения различной информации в текстовом формате');

            $table->timestamps();

            // Привязываем к клиенту
            $table->foreignIdFor(Client::class)->nullable()->constrained()->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
