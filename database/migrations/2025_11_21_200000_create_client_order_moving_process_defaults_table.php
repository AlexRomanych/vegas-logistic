<?php

use App\Models\BusinessProcesses\BusinessProcess;
use App\Models\BusinessProcesses\BusinessProcessNode;
use App\Models\Client;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    /**
     * ___ Промежуточная таблица, которая описывает настройку (смещение по дате) для формирования плана
     * ___ для бизнес процесса "Схема движения заявки"
     * ___ для использования при генерации планов
     * ___ и связывает клиента с группой ПЯ
     */

    const TABLE_NAME = 'client_order_moving_process_defaults';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Связь с Бизнес-процессом
            $table->foreignIdFor(BusinessProcess::class)
                ->nullable(false)
                ->constrained()
                ->cascadeOnDelete();

            // // __ Связь с Операционным узлом
            // $table->foreignId('operation_node_id')
            //     ->constrained('operation_nodes', 'id')
            //     ->cascadeOnDelete();

            // __ Связь с Клиентом
            $table->foreignIdFor(Client::class)
                ->nullable(false)
                ->constrained()
                ->cascadeOnDelete();
            // $table->foreignId('client_id')
            //     ->constrained('clients', 'id')
            //     ->cascadeOnDelete();

            // __ Связь с Операционным узлом (узел, относительно которого задается смещение)
            $table->foreignIdFor(BusinessProcessNode::class)
                ->nullable(false)
                ->comment('Референс на Бизнес узел')
                ->constrained()
                ->cascadeOnDelete();
            // $table->foreignId('operation_node_ref_id')
            //     ->nullable(false)
            //     ->comment('Референс на Операционный узел')
            //     ->constrained('operation_nodes', 'id')
            //     ->cascadeOnDelete();

            // __ Предотвращение дублирования связей, создает индекс
            $table->unique(['business_process_id', 'client_id', 'business_process_node_id']);

            // __ Операция для смещения по дате (true - сложение, false - вычитание)
            $table->boolean('day_offset_operation')
                ->nullable()
                ->comment('Операция (true - сложение, false - вычитание)');

            // __ Смещение по дате в днях
            $table->integer('offset')
                ->nullable(false)
                ->default(0)
                ->comment('Смещение по дате в днях');

            // __ Расширенная логика (не только +/-, а еще и условие) (true - использовать, false - не использовать)
            $table->boolean('is_extended_logic')
                ->nullable(false)
                ->default(false)
                ->comment('Расширенная логика (не только +/-, а еще и условие)');

            // __ Время для тех случаев, когда нужно задать не только смещение по дате, но и время относительно которого будет происходить событие
            $table->time('time_at')
                ->nullable()
                ->comment('Время');

            // __ Операция для смещения по времени (true - до, false - после)
            $table->boolean('time_offset_operation')
                ->nullable()
                ->comment('Операция (true - до, false - после)');

            // __ Метаданные (jsonb). Делаем на случай, если логика будет не только +/-, а еще и условие
            $table->jsonb('meta_extended')->nullable()->comment('Метаданные (jsonb)');
            // $table->timestamps();
        });

        $this->addCommonColumns(self::TABLE_NAME);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
