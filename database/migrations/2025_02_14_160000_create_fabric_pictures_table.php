<?php

use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fabric_pictures', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->nullable(false)->unique()->comment('Название рисунка');
            $table->binary('image')->nullable()->comment('Рисунок стежки');
            $table->string('rows')->nullable()->comment('Количество задействованных рядов');  // '1' - 1 ряд, '1-2' - 1, 2 ряд, '1-3' - 1, 3 ряд
            $table->float('stitch length')->nullable(false)->default(0)->comment('Длина стежка в мм'); // Длина стежка по КД, мм
            $table->integer('stitch_speed')->nullable(false)->default(0)->comment('Скорость стежков, шт./мин.'); // Скорость стежков шт./мин.
            $table->integer('moment_speed')->nullable(false)->default(0)->comment('Мгновенная скорость, м/ч'); // Мгновенная скорость, м/час
            $table->string('description')->nullable()->comment('Описание рисунка');
            $table->string('comment')->nullable()->comment('Комментарий к рисунку');


            // Основная стегальная машина
            $table->foreignIdFor(FabricMachine::class)->nullable(false)->default(1)->comment('Привязка к стегальной машине')
                ->constrained()
                ->nullOnDelete();

            // Альтернативные машины в порядке приоритета
            $table->foreignId('alt_machine_1')->nullable(false)->default(1)
                ->references('id')
                ->on('fabric_machines')
                ->nullOnDelete();

            $table->foreignId('alt_machine_2')->nullable(false)->default(1)
                ->references('id')
                ->on('fabric_machines')
                ->nullOnDelete();

            $table->foreignId('alt_machine_3')->nullable(false)->default(1)
                ->references('id')
                ->on('fabric_machines')
                ->nullOnDelete();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_pictures');
    }
};
