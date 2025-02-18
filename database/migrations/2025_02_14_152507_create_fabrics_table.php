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
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('code_1C')->unique()->nullable(false)->comment('Код 1С');
            $table->string('name')->nullable(false)->unique()->comment('Название');
            $table->double('buffer_amount')->nullable()->comment('Количество в буфере');
            $table->double('optimal_party')->nullable()->comment('Оптимальная партия для запуска');
            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность или архив');
            $table->string('description')->nullable()->comment('Описание');
            $table->timestamps();

            $table->foreignIdFor(FabricMachine::class)->nullable(false)->default(1)->comment('Привязка ПС к стегальной машине');
            $table->json('alternative_machines')->nullable()->comment('Альтернативные машины');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
