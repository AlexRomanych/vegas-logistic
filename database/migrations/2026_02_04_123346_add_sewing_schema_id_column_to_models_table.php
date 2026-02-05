<?php

use App\Models\Manufacture\Cells\Sewing\SewingOperationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE = 'models';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->foreignIdFor(SewingOperationSchema::class)
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
            $table->dropForeign(['sewing_operation_schema_id']);

            // 2. Удаляем саму колонку
            $table->dropColumn('sewing_operation_schema_id');
        });
    }
};
