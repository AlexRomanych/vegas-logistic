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
        Schema::table('block_task_lines', function (Blueprint $table) {
            $table
                ->boolean('false_rolling')
                ->nullable(false)
                ->default(false)
                ->comment('Маяк того, что строка СЗ переходит на следующий день со статусом false');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_task_lines', function (Blueprint $table) {
            $table->dropColumn('false_rolling');
        });
    }
};
