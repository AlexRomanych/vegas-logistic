<?php

use App\Models\Manufacture\Cells\Cutting\CuttingOperationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE = 'models';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->foreignIdFor(CuttingOperationSchema::class)
                ->nullable(false)
                ->default(0)
                ->constrained()
                ->onDelete('set default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            // 1. Удаляем внешний ключ.
            // Передача массива ['column_name'] заставляет Laravel самому вычислить имя индекса.
            $table->dropForeign(['cutting_operation_schema_id']);

            // 2. Удаляем саму колонку
            $table->dropColumn('cutting_operation_schema_id');
        });
    }
};
