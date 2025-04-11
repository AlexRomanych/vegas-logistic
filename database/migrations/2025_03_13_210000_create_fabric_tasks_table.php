<?php

use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Models\Manufacture\Cells\Fabric\FabricTeam;
use App\Models\User;
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
//            $table->timestamp('task_date')
//                ->nullable(false)
////                ->useCurrent()
//                ->comment('Плановая дата сменного задания');

            // Оригинальное сменное задание warning Будем реализовывать через таблицу fabric_tasks_context
            // Перечень ПС и количества к стеганию
//            $table->json('initial_tasks')->nullable()->comment('Список заданий при выставлении');

            // Зададим статус СЗ именно для данной даты и данной СМ:
            //      0 - Сменное задание еще не создано (или сохранено)
            //      1 - Сменное задание создано (или сохранено)
            //      2 - Сменное задание отправлено на выполнение
            //      3 - Сменное задание взято на выполнение (находится в процессе выполнения)
            //      4 - Сменное задание выполнено (закрыто)
            // warning: 'task_status' - единственное число
            $table->unsignedTinyInteger('task_status')
                ->nullable(false)
                ->default(0)
                ->comment('Статус задания по данной СМ');

            // warning: 'task_finish_date' - единственное число
            $table->timestamp('task_finish_at')
                ->nullable()
//                ->useCurrent()
                ->comment('Дата завершения сменного задания');



            $table->ForeignIdFor(FabricTasksDate::class)
                ->nullable(false)
                ->comment('Привязка к определенной дате, группирующей СЗ')
                ->constrained()
                ->cascadeOnDelete();

            $table->ForeignIdFor(FabricMachine::class)
                ->nullable(false)
                ->comment('Привязка к стегальной машине')
                ->constrained()
                ->nullOnDelete();

            // warning: Оставляем здесь, но пока не используем. Привязка будет в TasksDate
            $table->ForeignIdFor(FabricTeam::class)
                ->nullable(false)
                ->default(1)
                ->comment('Привязка к бригаде')
                ->constrained()
                ->nullOnDelete();

            $table->ForeignIdFor(User::class)
                ->nullable(false)
                ->default(1)
                ->comment('Тот, кто создал это СЗ для данной СМ')
                ->constrained()
                ->nullOnDelete();

            $table->boolean('active')
                ->nullable(false)
                ->default(true)
                ->comment('Актуальность');
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
