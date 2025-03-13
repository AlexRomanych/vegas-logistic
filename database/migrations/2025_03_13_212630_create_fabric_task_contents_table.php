<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'fabric_task_contents';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            $table->ForeignIdFor(FabricTask::class)->nullable(false)->comment('Привязка к сменному заданию')
                ->constrained()
                ->cascadeOnDelete();

            $table->ForeignIdFor(Fabric::class)->nullable(false)->comment('Привязка к ПС')
                ->constrained()
                ->cascadeOnDelete();

            $table->ForeignId('responsible_id')->nullable(false)->default(0)->comment('Ответственный')
                ->constrained('workers', 'id')
                ->nullOnDelete();

//            $table->ForeignId('responsible_id')->nullable(false)->default(1)->comment('Ответственный')
//                ->references('id')
//                ->on('workers')
//                ->nullOnDelete();

            $table->double('textile_length')->nullable(false)->default(0)->comment('Длина рулона ткани, м.п.');
            $table->double('fabric_length')->nullable(false)->default(0)->comment('Длина рулона ПС, м.п.');     // то, что получилось в результате стегания

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
