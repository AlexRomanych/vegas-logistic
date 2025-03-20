<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
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
//            $table->bigInteger('order')->nullable(false)->default(0)->comment('Порядковый номер позиции');

            $table->unsignedBigInteger('roll_number')
                ->from(1)
                ->nullable(false)
                ->unique()
                ->comment('Номер рулона');
            // attract: Привязка к ПС
            $table->ForeignIdFor(Fabric::class)->nullable(false)->comment('Привязка к ПС')
                ->constrained()
                ->cascadeOnDelete();

            // attract: Время создания рулона на стежке и ответственный за выпуск
            $table->timestamp('finish_at')
                ->nullable(true)->comment('Дата и время завершения стегания рулона');
            $table->ForeignId('finish_by')
                ->nullable(false)->default(0)->comment('Ответственный за производство рулона')
                ->constrained('workers', 'id')
                ->nullOnDelete();

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

            // attract: Привязка к сменному заданию
            $table->ForeignIdFor(FabricTask::class)->nullable(false)->comment('Привязка к сменному заданию')
                ->constrained()
                ->cascadeOnDelete();

//            $table->ForeignId('responsible_id')->nullable(false)->default(1)->comment('Ответственный')
//                ->references('id')
//                ->on('workers')
//                ->nullOnDelete();

            $table->double('textile_roll_length')->nullable(false)->default(0)->comment('Длина рулона ткани, м.п.');
            $table->double('fabric_roll_length')->nullable(false)->default(0)->comment('Длина рулона ПС, м.п.');     // то, что получилось в результате стегания
            $table->double('defect_length')->nullable(false)->default(0)->comment('Брак, м.п.');

            // статус позиции сменного задания
            $table->unsignedTinyInteger('item_status')->nullable(false)->default(0)->comment('Статус задания');

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
