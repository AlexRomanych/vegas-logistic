<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'materials';
    private const COLUMN_NAME = 'ext_meta';

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->jsonb(self::COLUMN_NAME)->nullable()->comment('Дополнительная для расчета материалов информация');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            if (Schema::hasColumn(self::TABLE_NAME, self::COLUMN_NAME)) {
                $table->dropColumn(self::COLUMN_NAME);
            }
        });
    }
};
