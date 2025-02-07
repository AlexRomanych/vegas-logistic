<?php

use App\Models\Manufacture\CellItem;
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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('surname')->nullable(false)->comment('фамилия');
            $table->string('name')->nullable(false)->comment('имя');
            $table->string('patronymic')->nullable(false)->comment('отчество');
            $table->string('phone')->nullable()->comment('телефон');
            $table->string('address')->nullable()->comment('адрес');
            $table->string('role')->nullable()->comment('роль');
            $table->string('status')->nullable()->comment('статус');
            $table->json('cell_items')->nullable()->comment('привязка к ПЯ');
            $table->timestamps();

            // attract: У каждого рабочего может быть привязка к ПЯ
//            $table->foreignIdFor(CellItem::class)->nullable()->constrained()->nullOnDelete();    //Внешний ключ, указывающий на таблицу с ПЯ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
