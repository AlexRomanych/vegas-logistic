<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cells_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->unique(true)->comment('Название группы');
            $table->string('description')->nullable()->comment('Описание группы');
            $table->timestamps();
        });

        // Добавляем начальные данные
        DB::table('cells_groups')->insert([
            ['name' => 'Швейный цех', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Сборка', 'created_at' => now(), 'updated_at' => now(),]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cells_groups');
    }
};
