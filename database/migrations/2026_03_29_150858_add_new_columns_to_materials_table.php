<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private const TABLE_NAME = 'materials';

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('object_name')->nullable()->comment('Тип объекта, который соответствует типу в таблице процедур');
            $table->string('object_code_1c')->nullable()->comment('Код из 1С Типа объекта, который соответствует типу в таблице процедур');
            $table->jsonb('properties')->nullable()->comment('Свойства, прикрепленные к материалу');
            $table->boolean('is_modify')->nullable(false)->default(false)->comment('Флаг модификации материала (был ли изменен при обновлении)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            if (Schema::hasColumn(self::TABLE_NAME, 'object_name')) {
                $table->dropColumn('object_name');
            }

            if (Schema::hasColumn(self::TABLE_NAME, 'object_code_1c')) {
                $table->dropColumn('object_code_1c');
            }

            if (Schema::hasColumn(self::TABLE_NAME, 'properties')) {
                $table->dropColumn('properties');
            }

            if (Schema::hasColumn(self::TABLE_NAME, 'is_modify')) {
                $table->dropColumn('is_modify');
            }
        });
    }
};
