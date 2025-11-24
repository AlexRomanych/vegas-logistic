<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ___ Промежуточная таблица для связи Клиента и смещение по датам для генерации плана
     */

    const TABLE_NAME = 'client_plan_default';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Связь с Настройками смещения для группы ПЯ
            $table->foreignId('plan_default_id')
                ->constrained('plan_defaults')
                ->cascadeOnDelete();

            // __ Связь с Клиентом
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            // __ Предотвращение дублирования связей, создает индекс
            // $table->unique(['plan_default_id', 'client_id']);

            $table->json('information')->nullable();

            $table->integer('description')->nullable();
            $table->integer('comment')->nullable();
            $table->integer('note')->nullable();

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
