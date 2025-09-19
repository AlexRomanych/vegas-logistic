<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    const TABLE_NAME = 'fabric_tuning_times';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Рисунок с которого происходит переналадка
            $table->foreignId('picture_from')
                ->nullable(false)
                // ->default(0)
                ->comment('Рисунок с которого происходит переналадка')
                ->references('id')
                ->on('fabric_pictures')
                ->cascadeOnDelete();

            // __ Рисунок на который происходит переналадка
            $table->foreignId('picture_to')
                ->nullable(false)
                // ->default(0)
                ->comment('Рисунок на который происходит переналадка')
                ->references('id')
                ->on('fabric_pictures')
                ->cascadeOnDelete();

            // __ Время переналадки в минутах
            $table->float('tuning_time')
                ->nullable(false)
                ->default(0)
                ->comment('Время переналадки в минутах');


            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');

            $table->timestamps();

            // __ Уникальное сочетание для 'Защиты от дурака'
            $table->unique(['picture_from', 'picture_to']);
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
