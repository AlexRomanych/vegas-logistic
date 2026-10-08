<?php

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
        Schema::table('block_tasks', function (Blueprint $table) {
            $table
                ->boolean('manual_adding')
                ->default(false)
                ->comment('Маяк того, в каком режиме было добавлено СЗ (true - при загрузке Заявок, false - в ручном режиме)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_tasks', function (Blueprint $table) {
            $table->dropColumn(['manual_adding']);
        });
    }
};
