<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'fabric_task_rolls';
    private const ROLL_NUMBER_START = 1;    // начальный номер рулона
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {

            $table->id()->from(self::ROLL_NUMBER_START);

//            $table->unsignedInteger('roll_number')
//                ->from(self::ROLL_NUMBER_START)
//                ->nullable(false)
//                ->unique()
//                ->comment('Номер рулона');

            // attract: Порядковый номер позиции рулона в сменном задании
            $table->unsignedTinyInteger('roll_position')->nullable(false)->default(0)->comment('Порядковый номер позиции рулона в сменном задании');

            // attract: Привязка к ПС
            $table->ForeignIdFor(Fabric::class)->nullable(false)->comment('Привязка к ПС')
                ->constrained()
                ->cascadeOnDelete();

            // attract: Постановка рулона на учет в 1С
            $table->timestamp('registration_1C_at')
                ->nullable()
                ->comment('Дата постановки на учет в 1С');

            // attract: Время создания рулона на стежке
            $table->timestamp('finish_at')
                ->nullable()->comment('Дата и время завершения стегания рулона');

            // attract: Ответственный за выпуск рулона на стежке
            $table->ForeignId('finish_by')
                ->nullable(false)->default(0)->comment('Ответственный за производство рулона')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Статус позиции сменного задания (статус стегания рулона)
            $table->unsignedTinyInteger('roll_status')->nullable(false)->default(0)->comment('Статус задания');

            // attract: Время перемещения рулона на закрой и ответственный за перемещение на закрой
            $table->timestamp('move_to_cut_at')
                ->nullable(true)->comment('Дата и время перемещения на закрой');     // маяк того, что рулон перемещен на стежку, а это минус в буфере
            $table->ForeignId('move_to_cut_by')
                ->nullable(false)->default(0)->comment('Ответственный за перемещение рулона на закрой')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Время приема рулона на закрой и ответственный за прием на закрой
            $table->timestamp('receipt_to_cut_at')
                ->nullable(true)->comment('Дата и время перемещения на закрой');     // маяк того, что рулон перемещен на стежку, а это минус в буфере
            $table->ForeignId('receipt_to_cut_by')
                ->nullable(false)->default(0)->comment('Ответственный за прием рулона на закрое')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Привязка к сменному заданию warning: Пока не будем реализовывать эту привязку
//            $table->ForeignIdFor(FabricTask::class)->nullable(false)->comment('Привязка к сменному заданию')
//                ->constrained()
//                ->cascadeOnDelete();

            // attract: Привязка к плановому сменному заданию
            // attract: Плановое сменное задание и сменное задание к выполнению - это две разные сущности
            // attract: В плановом СЗ учитывается ткань и количество рулонов - это одна позиция (один id)
            // attract: В выполняемом СЗ учитывается это плановое сменное задание разбивается на каждый отдельный рулон
            // attract: который и описывается в этом задании. Поэтому каждый рулон этой сущности привязываем
            // attract: к группе рулонов планового сменного задания
            $table->ForeignIdFor(FabricTaskContext::class)->nullable()->comment('Привязка к плановому сменному заданию')
                ->constrained()
                ->cascadeOnDelete();

//            $table->ForeignId('responsible_id')->nullable(false)->default(1)->comment('Ответственный')
//                ->references('id')
//                ->on('workers')
//                ->nullOnDelete();

            $table->float('textile_roll_length')->nullable(false)->default(0)->comment('Длина рулона ткани, м.п.');
            $table->float('fabric_roll_length')->nullable(false)->default(0)->comment('Длина рулона ПС, м.п.');     // то, что получилось в результате стегания
            $table->float('productivity')->nullable(false)->default(0)->comment('Средняя производительность на момент формирования, м/ч.');     // то, что получилось в результате стегания
            $table->float('defect_length')->nullable(false)->default(0)->comment('Брак, м.п.');


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
