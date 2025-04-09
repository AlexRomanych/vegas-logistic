<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'fabric_task_contexts';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // attract: Порядковый номер позиции рулона в сменном задании
            $table->unsignedTinyInteger('roll_position')->nullable(false)->default(0)->comment('Порядковый номер позиции рулона в сменном задании');

            $table->boolean('fabric_mode')->nullable(false)->default(true)->comment('Режим, в котором было создано ПС (true -основное для данной СМ, false - альтернативнное)');
            $table->integer('rolls_amount')->nullable(false)->default(0)->comment('Количество рулонов, выставленное специалистом ОПП');

            // attract: Привязка к сменному заданию
            $table->ForeignIdFor(FabricTask::class)->nullable(false)->comment('Привязка к сменному заданию')
                ->constrained()
                ->cascadeOnDelete();

            // attract: Привязка к стегальной машине
            $table->ForeignIdFor(FabricMachine::class)->nullable(false)->comment('Привязка к стегальной машине')
                ->constrained()
                ->cascadeOnDelete();

            // attract: Привязка к ПС
            $table->ForeignIdFor(Fabric::class)->nullable(false)->comment('Привязка к ПС')
                ->constrained()
                ->cascadeOnDelete();

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
