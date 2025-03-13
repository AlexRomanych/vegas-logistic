<?php

use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricTeam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'fabric_tasks';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->timestamp('task_date')
                ->nullable(false)
                ->useCurrent()
                ->comment('Дата создания сменного задания');

            $table->timestamp('finish_task_date')
                ->nullable(false)
                ->useCurrent()
                ->comment('Дата завершения сменного задания');

            $table->timestamp('registration_1C')->nullable()->comment('Дата постановки на учет в 1С');

            $table->ForeignIdFor(FabricMachine::class)->nullable(false)->comment('Привязка к стегальной машине')
                ->constrained()
                ->cascadeOnDelete();

            $table->ForeignIdFor(FabricTeam::class)->nullable(false)->default(1)->comment('Привязка к бригаде')
                ->constrained()
                ->nullOnDelete();

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
