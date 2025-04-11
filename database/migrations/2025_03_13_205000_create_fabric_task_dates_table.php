<?php

use App\Models\Manufacture\Cells\Fabric\FabricTeam;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    const TABLE_NAME = 'fabric_tasks_dates';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // warning: 'tasks_date' - множественное число
            $table->timestamp('tasks_date')
                ->nullable(false)
                ->unique()
                ->comment('Дата сменного задания, которая объединяет все: СЗ на СМ + плановые СЗ + рулоны');



            // Зададим статус для всех СЗ в этом дне (американец, немец, китаец, кореец) -
            // своеобразный флаг действий, которые можно выполнять с заданием:
            //      0 - Сменное задание еще не создано (или сохранено)
            //      1 - Сменное задание создано (или сохранено)
            //      2 - Сменное задание отправлено на выполнение
            //      3 - Сменное задание взято на выполнение (находится в процессе выполнения)
            //      4 - Сменное задание выполнено (закрыто)
            // warning: 'tasks_status' - множественное число
            $table->unsignedTinyInteger('tasks_status')
                ->nullable(false)
                ->default(0)
                ->comment('Статус задания');

            // warning: 'tasks_finish_date' - множественное число
            $table->timestamp('tasks_finish_date')
                ->nullable()
                ->comment('Дата завершения всех сменных заданий в этом дне');

            $table->ForeignIdFor(FabricTeam::class)
                ->nullable(false)
                ->default(1)
                ->comment('Привязка к бригаде')
                ->constrained()
                ->nullOnDelete();

            $table->ForeignIdFor(User::class)
                ->nullable(false)
                ->default(1)
                ->comment('Тот, кто создал этот день для СЗ')
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
