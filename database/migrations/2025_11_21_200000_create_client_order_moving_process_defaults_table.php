<?php

use App\Models\BusinessProcesses\BusinessProcess;
use App\Models\BusinessProcesses\BusinessProcessNode;
use App\Models\Client;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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

            // __ Связь с Операционным узлом (узел, для которого производится настройка)
            $table->foreignIdFor(BusinessProcessNode::class)
                ->nullable(false)
                ->comment('Бизнес узел, для которого производится настройка')
                ->constrained()
                ->cascadeOnDelete();

            // __ Связь с Операционным узлом (узел, относительно которого задается смещение)
            $table->foreignId('process_node_ref_id')
                ->nullable(false)
                ->comment('Референс на Операционный узел')
                ->constrained('business_process_nodes', 'id')
                ->cascadeOnDelete();

            // __ Предотвращение дублирования связей, создает индекс
            $table->unique(['business_process_id', 'client_id', 'business_process_node_id']);

            // __ Смещение по дате в днях
            $table->integer('offset')
                ->nullable(false)
                ->default(0)
                ->comment('Смещение по дате в днях');

            // __ Операция для смещения по дате (true - сложение, false - вычитание)
            $table->boolean('day_offset_operation')
                ->nullable()
                ->comment('Операция (true - сложение, false - вычитание)');

            // __ Интервал (true - использовать, false - не использовать)
            $table->boolean('is_interval')
                ->nullable(false)
                ->default(false)
                ->comment('Смещение интервальное или точное');

            // __ Смещение по дате в днях для интервала: начало
            $table->integer('offset_start')
                ->nullable(false)
                ->default(0)
                ->comment('Начало смещение по дате в днях для интервала');

            // __ Смещение по дате в днях для интервала: окончание
            $table->integer('offset_end')
                ->nullable(false)
                ->default(0)
                ->comment('Окончание смещение по дате в днях для интервала');


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

            // __ Разрешить обработку (для случаев, когда, например, нужно запретить или разрешить обработку
            // __ данного узла в процессе оптимизации)
            $table->boolean('allow_action')
                ->nullable()
                ->comment('Разрешить обработку');


            // __ Метаданные (jsonb). Делаем на случай, если логика будет не только +/-, а еще и условие
            $table->jsonb('meta_extended')->nullable()->comment('Метаданные (jsonb)');
            // $table->timestamps();
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            [ // __ Поступление заявки
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 1,
                'process_node_ref_id'      => 12,
                'offset'                   => -5,
                'day_offset_operation'     => false,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Внесение заявки КС в 1С
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 2,
                'process_node_ref_id'      => 1,
                'offset'                   => 0,
                'day_offset_operation'     => null,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Начало производства
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 6,
                'process_node_ref_id'      => 2,
                'offset'                   => 1,
                'day_offset_operation'     => true,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Раскрой
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 7,
                'process_node_ref_id'      => 12,
                'offset'                   => -3,
                'day_offset_operation'     => false,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Пошив
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 8,
                'process_node_ref_id'      => 7,
                'offset'                   => 1,
                'day_offset_operation'     => true,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Сборка
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 9,
                'process_node_ref_id'      => 8,
                'offset'                   => 1,
                'day_offset_operation'     => true,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Упаковка
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 10,
                'process_node_ref_id'      => 9,
                'offset'                   => 0,
                'day_offset_operation'     => null,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Передача на склад ГП
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 11,
                'process_node_ref_id'      => 9,
                'offset'                   => 0,
                'day_offset_operation'     => null,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
            [ // __ Загрузка на складе
                'business_process_id'      => 1,
                'client_id'                => 0,
                'business_process_node_id' => 12,
                'process_node_ref_id'      => 12,
                'offset'                   => 0,
                'day_offset_operation'     => null,
                'created_at'               => now(),
                'updated_at'               => now()
            ],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
