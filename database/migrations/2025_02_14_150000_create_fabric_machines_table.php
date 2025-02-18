<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fabric_machines', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->nullable(false)->unique()->comment('Название машины');
            $table->string('short_name')->nullable()->unique()->comment('Сокращенное название машины');
            $table->string('description')->nullable();

            $table->timestamps();
        });

        // Добавляем в таблицу имена машин
        DB::table('fabric_machines')->insert([
            ['id' => 1, 'name' => 'Машина не определена', 'short_name' => 'Нет данных'],
            ['id' => 2, 'name' => 'LEGACY-4 (Американец)', 'short_name' => 'Американец'],
            ['id' => 3, 'name' => 'CHAINTRONIC (Немец)', 'short_name' => 'Немец'],
            ['id' => 4, 'name' => 'HY-W-DGW (Китаец)', 'short_name' => 'Китаец'],
            ['id' => 5, 'name' => 'МТ-94 (Кореец)', 'short_name' => 'Кореец'],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_machines');
    }
};
