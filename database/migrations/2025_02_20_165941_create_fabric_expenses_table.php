<?php

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    const TABLE_NAME = 'fabric_expenses';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->nullable(false)->comment('Название ПС');
            $table->float('expense')->nullable(false)->default(0)->comment('Количество расхода ПС');
            $table->date('expense_at')->nullable()->comment('Дата расхода');        // Это на всякий случай, если расход не будем привязывать к общему заказу в 1С
            $table->string('description')->nullable()->comment('Описание или дополнительная информация');
            $table->timestamps();

            $table->foreignIdFor(Fabric::class)->nullable(false)->comment('Привязка к ПС')->constrained()->nullOnDelete();
            $table->foreignIdFor(FabricOrder::class)->nullable(false)->comment('Привязка к заказу')->constrained()->cascadeOnDelete();
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
