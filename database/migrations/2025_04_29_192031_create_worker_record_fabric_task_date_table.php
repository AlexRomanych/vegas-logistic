<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'worker_record_fabric_task_date';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fabric_tasks_date_id')->constrained()->cascadeOnDelete();

            $table->unique(['worker_record_id', 'fabric_tasks_date_id']); // Предотвращение дублирования связей, создает индекс

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
