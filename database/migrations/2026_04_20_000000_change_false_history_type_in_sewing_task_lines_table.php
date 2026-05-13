<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewing_task_lines', function (Blueprint $table) {
            // Используем jsonb для PostgreSQL (или просто text, если нужна совместимость)
            $table->text('false_history')->nullable()->change();

            // Если колонке false_reason тоже может не хватить 255 символов:
            $table->text('false_reason')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sewing_task_lines', function (Blueprint $table) {
            $table->string('false_history', 255)->nullable()->change();
            $table->string('false_reason', 255)->nullable()->change();
        });
    }
};
