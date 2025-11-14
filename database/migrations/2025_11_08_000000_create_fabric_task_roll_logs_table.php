<?php

use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\User;
use App\Models\Worker\Worker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private const TABLE_NAME = 'fabric_task_roll_logs';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Связь с физическим рулоном
            $table->foreignIdFor(FabricTaskRoll::class)
                ->nullable(false)
                ->comment('Связь с физическим рулоном')
                ->constrained()
                ->cascadeOnDelete();

            // __ Связь с юзером, который изменил статус физического
            $table->foreignIdFor(User::class)
                ->nullable(false)
                ->comment('Связь с пользователем')
                ->constrained()
                ->nullOnDelete();

            // __ Дата записи лога
            $table->timestamp('log_at')
                ->nullable(false)
                // ->default(now())
                ->useCurrent()
                ->comment('Дата записи лога');

            // __ Статус до
            $table->integer('status_before')
                ->nullable(false)
                ->default(FABRIC_ROLL_NULL_CODE)
                ->comment('Статус до');

            // __ Статус после
            $table->integer('status_after')
                ->nullable(false)
                ->comment('Статус после');

            // __ Постановка на учет в 1С
            $table->boolean('check_1C')
                ->nullable()
                ->comment('Постановка на учет в 1С');

            // __ Снятие с учета в 1С
            $table->boolean('uncheck_1C')
                ->nullable()
                ->comment('Снятие с учета в 1С');

            //__ Порядок/Позиция (roll_position) до
            $table->integer('roll_position_before')
                ->nullable()
                ->comment('Порядок до');

            //__ Порядок/Позиция (roll_position) после
            $table->integer('roll_position_after')
                ->nullable()
                ->comment('Порядок после');

            // __ Событие
            $table->string('event')
                ->nullable()
                ->comment('Событие');

            // __ Причина (невыполнения, отмены, изменения статуса, изменения позиции и т.д.)
            $table->string('reason')
                ->nullable()
                ->comment('Причина');

            // __ Ответственный
            $table->foreignIdFor(Worker::class)
                ->nullable()
                ->comment('Ответственный')
                ->constrained();

            $table->ipAddress('ip')->nullable()->comment('IP адрес');

            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->jsonb('meta')->nullable()->comment('Метаданные');



            // __ Индексы для быстрого поиска
            $table->index(['log_at', 'status_before', 'status_after']);

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
