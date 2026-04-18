<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private const TABLE_NAME = 'event_logs';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            $table->string('level')->nullable(false)->comment('INFO, ERROR, WARN');
            $table->string('target')->nullable(false)->comment('Имя модуля (например, "excel_parser" или "compiler")');
            $table->text('message')->nullable(false)->comment('Основное описание ошибки');
            $table->jsonb('context')->nullable(false)->default('{}')->comment('Сюда пишем { "file": "report.xlsx", "row": 154 }');

            $table->timestamps();

            $table->index(['level', 'created_at', 'context']);
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
