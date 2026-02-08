<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    const TABLE_NAME = 'sewing_days';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Дата рабочего дня
            $table->timestamp('action_at')
                ->nullable(false)
                ->comment('Дата рабочего дня');

            // __ Дата рабочего дня в строковом формате 'YYYY-MM-DD'
            $table->string('action_at_str')
                ->nullable(false)
                ->comment('Дата рабочего дня в строковом формате YYYY-MM-DD');

            // __ Номер производственной смены. Пока 1 смена, но задел на будущее
            $table->unsignedSmallInteger('change')
                ->nullable(false)
                ->default(1)
                ->comment('Номер производственной смены');



            // __ Связь с работником, ответственным за производство
            $table->foreignId('responsible_id')
                ->nullable()
                ->comment('Работник, ответственный за производство')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // __ Дата и время начала производственной смены
            $table->timestamp('start_at')
                ->nullable()
                ->comment('Дата и время начала производственной смены');

            // __ Дата и время приостановки производственной смены
            $table->timestamp('paused_at')
                ->nullable()
                ->comment('Дата и время приостановки производственной смены');

            // __ Дата и время возобновления производственной смены
            $table->timestamp('resume_at')
                ->nullable()
                ->comment('Дата и время возобновления производственной смены');

            // __ Дата и время окончания производственной смены
            $table->timestamp('finish_at')
                ->nullable()
                ->comment('Дата и время окончания производственной смены');

            // __ Время производственной смены в миллисекундах
            $table->unsignedBigInteger('duration')
                ->nullable()
                ->comment('Время производственной смены в миллисекундах');

            $table->jsonb('meta_ext')->nullable()->comment('Дополнительные данные');

            // __ Ограничение на уникальность дата + смена
            $table->unique(['action_at_str', 'change']);
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
