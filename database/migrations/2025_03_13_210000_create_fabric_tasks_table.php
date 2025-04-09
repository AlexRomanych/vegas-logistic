<?php

use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricTeam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'fabric_tasks';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->timestamp('task_date')
                ->nullable(false)
//                ->useCurrent()
                ->comment('Плановая дата сменного задания');

            // Оригинальное сменное задание warning Будем реализовывать через таблицу fabric_tasks_context
            // Перечень ПС и количества к стеганию
//            $table->json('initial_tasks')->nullable()->comment('Список заданий при выставлении');

            // Зададим статус СЗ:
            //      0 - Сменное задание еще не создано (или сохранено)
            //      1 - Сменное задание создано (или сохранено)
            //      2 - Сменное задание отправлено на выполнение
            //      3 - Сменное задание взято на выполнение (находится в процессе выполнения)
            //      4 - Сменное задание выполнено (закрыто)
            $table->unsignedTinyInteger('task_status')->nullable(false)->default(0)->comment('Статус задания');


            $table->timestamp('finish_task_date')
                ->nullable()
//                ->useCurrent()
                ->comment('Дата завершения сменного задания');

            $table->timestamp('registration_1C')->nullable()->comment('Дата постановки на учет в 1С');

            $table->ForeignIdFor(FabricMachine::class)->nullable(false)->comment('Привязка к стегальной машине')
                ->constrained()
                ->cascadeOnDelete();

            $table->ForeignIdFor(FabricTeam::class)->nullable(false)->default(1)->comment('Привязка к бригаде')
                ->constrained()
                ->nullOnDelete();

            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность');

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
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
