<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Worker\Worker;

return new class extends Migration
{
    /**
     * descr: Это так называемая запись о работнике, которая содержит всю информацию, необходимую для работы с работниками.
     * descr: Будет связывать производственную ячейку и работника, который там трудился
     * descr: Сделано, чтобы сделать связь "многие-ко-многим" между ПЯ и работниками, чтобы работать с ними как с одним объектом
     * descr: Тут можно добавлять разную инфу, например, сколько часов отработал работник на данном участке
     */

    private const TABLE = 'worker_records';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id()->from(1);


            // hr------------------------------------------------------
            // attract: Привязка к сотруднику
            $table->ForeignIdFor(Worker::class)
                ->nullable()
                ->comment('Привязка к списку сотрудников')
                ->constrained()
                ->nullOnDelete();

            // attract: Привязка к Условной ячейке производства
            // attract: Например, для ПС - это FABRIC_ID=1, причем это не запись в таблице cells_items,
            // attract: а то, что определяем сами.
            // attract: Для каждой условной ячейки производства (модуль в ИС) - своя константа.
            $table->unsignedInteger('cell_entity_id')->nullable()->comment('Условная ячейка производства');

            // attract: Привязка к сущности, которая отражает движение по определенному участку производства
            // attract: Например, для ПС - это отношение 'fabric_tasks_dates' - сущность, которая объединяет
            // attract: все СЗ по каждой СМ
            $table->unsignedInteger('entity_id')->nullable()->comment('Сущность, к которой будем привязываться');

            // attract: Эти три сущности помогут нам определить однозначно запись в этой таблице,
            // attract: что в свою очередь избавит от дублирования данных.
            // hr------------------------------------------------------


            $table->json('history')->nullable()->comment('История работы сотрудника на данном участке');

            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
