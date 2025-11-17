<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private const TABLE_NAME = 'fabric_task_rolls';

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('service')->nullable()->comment('Сервисная информация');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            // Удаляем столбец 'service', который был добавлен в методе up()
            $table->dropColumn('service');
        });
    }
};
