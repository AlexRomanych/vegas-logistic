<?php

use App\Models\Order\Line;
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
        Schema::create('cell_sewing_universal', function (Blueprint $table) {
            $table->id();
            $table->integer('amount')->nullable(false);
            $table->timestamp('manufactured_at')->nullable(false);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            //attract создаем связь с Line - записью в заказе
            $table->foreignIdFor(Line::class)->constrained()->cascadeOnDelete();  // Связываем с датой в заказе
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_sewing_universal');
    }
};
