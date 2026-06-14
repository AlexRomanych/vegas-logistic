<?php

//use App\Models\Manufacture\Cells\Cutting\CuttingOperationSchema;
use App\Models\Manufacture\Cells\Cutting\CuttingProcedure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE = 'models';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {

            // __ Верхняя Крышка
            $table->foreignId('cover_up_proc_id')
                ->nullable(false)
                ->default(0)
                ->comment('Процедура для верхней Крышки')
                ->constrained('cutting_procedures', 'id')
                ->onDelete('set default');

            // __ Нижняя Крышка
            $table->foreignId('cover_down_proc_id')
                ->nullable(false)
                ->default(0)
                ->comment('Процедура для нижней Крышки')
                ->constrained('cutting_procedures', 'id')
                ->onDelete('set default');

            // __ Боковина
            $table->foreignId('side_proc_id')
                ->nullable(false)
                ->default(0)
                ->comment('Процедура для Боковины')
                ->constrained('cutting_procedures', 'id')
                ->onDelete('set default');
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {

            // 1. Сначала дропаем внешние ключи (foreign constraints)
            // Laravel по умолчанию называет их по схеме: имяТаблицы_имяКолонки_foreign
            // Передача массива ['column_name'] заставляет Laravel самому вычислить имя индекса.
            $table->dropForeign(['cover_up_proc_id']);
            $table->dropForeign(['cover_down_proc_id']);
            $table->dropForeign(['side_proc_id']);

            //$table->dropForeign('models_cutting_procedure_id_foreign');

            // 2. Теперь безопасно удаляем сами колонки
            $table->dropColumn([
                'cover_up_proc_id',
                'cover_down_proc_id',
                'side_proc_id',
                //'cutting_procedure_id'
            ]);
        });
    }
};
