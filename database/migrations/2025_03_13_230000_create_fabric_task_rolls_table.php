<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'fabric_task_rolls';
    private const ROLL_NUMBER_START = 1;    // начальный номер рулона

    private const DEFAULT_TRANSLATE_RATE = 1.044;       // Коэффициент перевода в ткани
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


            // attract: Дата и время начала стегания рулона на стежке
            $table->timestamp('start_at')
                ->nullable()->comment('Дата и время начала стегания рулона на стежке');

            // attract: Дата и время приостановки стегания рулона на стежке
            $table->timestamp('paused_at')
                ->nullable()->comment('Дата и время приостановки стегания рулона на стежке');

            // attract: Дата и время возобновления стегания рулона на стежке
            $table->timestamp('resume_at')
                ->nullable()->comment('Дата и время возобновления стегания рулона на стежке');

            // attract: Дата и время окончания стегания рулона на стежке
            $table->timestamp('finish_at')
                ->nullable()->comment('Дата и время окончания стегания рулона на стежке');

            // attract: Время стегания рулона на стежке в миллисекундах
            $table->unsignedBigInteger('duration')
                ->nullable()->comment('Время стегания рулона на стежке в миллисекундах');


            // attract: Ответственный за выпуск рулона на стежке
            $table->ForeignId('finish_by')
                ->nullable()->default(0)->comment('Ответственный за производство рулона')
                ->constrained('workers', 'id')
                ->nullOnDelete();


            // attract: Id учетной записи пользователя, под записью которого были сделаны последние изменения
            $table->ForeignId('user_id')
                ->nullable()->default(0)->comment('Id учетной записи пользователя, под записью которого были сделаны последние изменения')
                ->constrained('users', 'id')
                ->nullOnDelete();


            // attract: Статус стегания рулона
            $table->unsignedTinyInteger('roll_status')->nullable(false)->default(0)->comment('Статус задания');

            // attract: Предыдущий Статус стегания рулона (на будущее, для отслеживания динамики выполнения)
            $table->unsignedTinyInteger('roll_status_previous')->nullable(false)->default(0)->comment('Статус задания');

            // attract: Постановка рулона на учет в 1С и ответственный за постановку
            $table->timestamp('registration_1C_at')
                ->nullable()->comment('Дата постановки на учет в 1С');
            $table->ForeignId('registration_1C_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за постановку рулона на учет в 1С')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Время перемещения рулона на закрой и ответственный за перемещение на закрой
            $table->timestamp('move_to_cut_at')
                ->nullable(true)->comment('Дата и время перемещения на закрой');     // маяк того, что рулон перемещен на стежку, а это минус в буфере
            $table->ForeignId('move_to_cut_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за перемещение рулона на закрой')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Время приема рулона на закрой и ответственный за прием на закрой
            $table->timestamp('receipt_to_cut_at')
                ->nullable(true)->comment('Дата и время перемещения на закрой');     // маяк того, что рулон перемещен на стежку, а это минус в буфере
            $table->ForeignId('receipt_to_cut_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за прием рулона на закрое')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Переходящий на другую смену рулон или нет
            $table->boolean('rolling')
                ->nullable(false)
                ->default(false)
                ->comment('Переходящий на другую смену рулон или нет');

            // attract: Причина невыполнения рулона
            $table->string('false_reason')->nullable()->comment('Причина невыполнения рулона');

            // attract: История рулона
            $table->json('history')->nullable()->comment('История рулона');


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

            // attract: Привязка к старому плановому сменному заданию (будем использовать при закрытии СЗ). Будет хранить
            // attract: старый id, чтобы можно было найти старое задание по этому id
            $table->unsignedBigInteger('task_context_id_old')->nullable()->comment('Id предыдущего задания, выставленного ООП');

            // attract: Атрибут для перемещения рулона из СЗ в другое СЗ. Если false - то рулон больше не перемещаемый
            $table->boolean('movable')->nullable(false)->default(true)->comment('Перемещаемый или нет');

//            $table->ForeignId('responsible_id')->nullable(false)->default(1)->comment('Ответственный')
//                ->references('id')
//                ->on('workers')
//                ->nullOnDelete();
            // attract: Маяк того, что длина рулона была изменена рабочим на стежке
            $table->boolean('is_length_change')->nullable(false)->default(false)->comment('Изменение длины рулона');

            $table->float('textile_rolling_length')->nullable(false)->default(0)->comment('Длина, которую отстегали для переходящего на другую смену рулона ткани, м.п.');
            $table->float('textile_used_length')->nullable(false)->default(0)->comment('Длина, которая была использована на закрое, м.п.');

            $table->float('textile_roll_length')->nullable(false)->default(0)->comment('Длина рулона ткани, м.п.');
            $table->float('fabric_roll_length')->nullable(false)->default(0)->comment('Длина рулона ПС, м.п.');     // то, что получилось в результате стегания
            $table->float('productivity')->nullable(false)->default(0)->comment('Средняя производительность на момент формирования, м/ч.');     // то, что получилось в результате стегания
            $table->float('translate_rate')->nullable(false)->default(self::DEFAULT_TRANSLATE_RATE)->comment('Коэффициент перевода');   // Непонятно пока зачем, но он равен 1,044 примерно
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
