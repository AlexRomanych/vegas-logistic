<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    const TABLE_NAME = 'fabric_task_rolls';

    /*
     * Warning! Меняем внешние ключи. Исправляем ошибку. Меняем таблицу 'workers' на 'users'
     */

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            // attract: Сбрасываем внешние ограничения
            $table->dropConstrainedForeignId('registration_1C_by');
//            $table->dropColumn('registration_1C_by');

            $table->dropConstrainedForeignId('move_to_cut_by');
//            $table->dropColumn('move_to_cut_by');

            $table->dropConstrainedForeignId('receipt_to_cut_by');
//            $table->dropColumn('receipt_to_cut_by');


            // attract: Создаем внешние ограничения

            // attract: Постановка рулона на учет в 1С и ответственный за постановку
            $table->ForeignId('registration_1C_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за постановку рулона на учет в 1С')
                ->constrained('users', 'id')
                ->nullOnDelete();

            // attract: Время перемещения рулона на закрой и ответственный за перемещение на закрой
            $table->ForeignId('move_to_cut_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за перемещение рулона на закрой')
                ->constrained('users', 'id')
                ->nullOnDelete();

            // attract: Время приема рулона на закрой и ответственный за прием на закрой
            $table->ForeignId('receipt_to_cut_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за прием рулона на закрое')
                ->constrained('users', 'id')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            // attract: Сбрасываем внешние ограничения
            $table->dropConstrainedForeignId('registration_1C_by');
            $table->dropColumn('registration_1C_by');

            $table->dropConstrainedForeignId('move_to_cut_by');
            $table->dropColumn('move_to_cut_by');

            $table->dropConstrainedForeignId('receipt_to_cut_by');
            $table->dropColumn('receipt_to_cut_by');


            // attract: Постановка рулона на учет в 1С и ответственный за постановку
            $table->ForeignId('registration_1C_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за постановку рулона на учет в 1С')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Время перемещения рулона на закрой и ответственный за перемещение на закрой
            $table->ForeignId('move_to_cut_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за перемещение рулона на закрой')
                ->constrained('workers', 'id')
                ->nullOnDelete();

            // attract: Время приема рулона на закрой и ответственный за прием на закрой
            $table->ForeignId('receipt_to_cut_by')
                ->nullable(false)
                ->default(0)
                ->comment('Ответственный за прием рулона на закрое')
                ->constrained('workers', 'id')
                ->nullOnDelete();

        });
    }
};
