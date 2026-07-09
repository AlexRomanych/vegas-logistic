<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    const TABLE_NAME = 'block_tuning_times';
    const TABLE_NAME_REF = 'block_collections';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Коллекция Блоков с которой происходит переналадка
            $table->foreignId('collection_from')
                ->nullable(false)
                ->comment('Коллекция Блоков с которой происходит переналадка')
                ->references('id')
                ->on(self::TABLE_NAME_REF)
                ->cascadeOnDelete();

            // __ Коллекция Блоков на которую происходит переналадка
            $table->foreignId('collection_to')
                ->nullable(false)
                ->comment('Коллекция Блоков на которую происходит переналадка')
                ->references('id')
                ->on(self::TABLE_NAME_REF)
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
            $table->unique(['collection_from', 'collection_to']);
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
