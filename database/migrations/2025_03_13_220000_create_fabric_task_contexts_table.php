<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    const TABLE_NAME = 'fabric_task_contexts';

    private const DEFAULT_TRANSLATE_RATE = 1.044;       // Коэффициент перевода в ткани

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // attract: Порядковый номер позиции рулона в сменном задании
            $table->unsignedTinyInteger('roll_position')
                ->nullable(false)
                ->default(0)
                ->comment('Порядковый номер позиции рулона в сменном задании');

            $table->boolean('fabric_mode')
                ->nullable(false)
                ->default(true)
                ->comment('Режим, в котором было создано ПС (true -основное для данной СМ, false - альтернативное)');

            $table->integer('rolls_amount')
                ->nullable(false)
                ->default(0)
                ->comment('Количество рулонов, выставленное специалистом ОПП');

            // attract: Средняя длина ткани на момент выставления СЗ,
            // attract: чтобы можно было потом отследить либо всю длину рулона ткани, либо еще чего-нибудь
            $table->float('average_textile_length')
                ->nullable(false)
                ->default(0)
                ->comment('Средняя длина рулона ткани для стегания при выставлении СЗ, м.п.');

            // attract: Средняя производительность ткани на момент выставления СЗ,
            // attract: чтобы можно было потом отследить либо всю производительность, либо еще чего-нибудь
            $table->float('productivity')
                ->nullable(false)
                ->default(0)
                ->comment('Средняя производительность стегания ПС при выставлении СЗ, м/ч');

            // attract: Средний коэффициент перевода ткани на момент выставления СЗ,
            // attract: чтобы можно было потом отследить чего-нибудь
            $table->float('translate_rate')->nullable(false)->default(self::DEFAULT_TRANSLATE_RATE)->comment('Коэффициент перевода');   // Непонятно пока зачем, но он равен 1,044 примерно

            // attract: Привязка к сменному заданию
            $table->ForeignIdFor(FabricTask::class)
                ->nullable(false)
                ->comment('Привязка к сменному заданию')
                ->constrained()
                ->cascadeOnDelete();

//            // attract: Привязка к стегальной машине
//              warning: не используется, привязка будет в самом сменном задании (см. FabricTask)
//            $table->ForeignIdFor(FabricMachine::class)->nullable(false)->comment('Привязка к стегальной машине')
//                ->constrained()
//                ->cascadeOnDelete();

            // attract: Привязка к ПС
            $table->ForeignIdFor(Fabric::class)
                ->nullable(false)
                ->comment('Привязка к ПС')
                ->constrained()
                ->cascadeOnDelete();

            // attract: После закрытия СЗ, те, которые были перенесены - не редактируем
            $table->boolean('editable')->nullable(false)->default(true)->comment('Возможность редактировать');

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
