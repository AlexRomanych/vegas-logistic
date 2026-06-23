<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cutting_task_lines', function (Blueprint $table) {
            $table->string('detail')->nullable()->comment('Тип детали (Крышка /верх или низ/, Боковина, ...)');
            $table->integer('cut_detail_amount')->nullable(false)->default(0)->comment('Количество деталей, шт.');
            $table->float('expense')->nullable(false)->default(0.0)->comment('Расход, мп');

            // __ Код из 1С Модели той Спецификации, которая привязывается в Строке Заявки (OrderLine)
            // __ Если это регулярная модель, здесь должен лежать ее код
            // __ Это для того, чтобы во внешнем сервисе не бегать по таблицам
            $table->string('base_code_1c')->nullable()->comment('Код из 1С Модели той Спецификации, которая привязывается в Строке Заявки');

            // __ Код из 1С Чехла Модели той Спецификации, которая привязывается в Строке Заявки (OrderLine)
            // __ Если это регулярная модель, здесь должен лежать код Чехла
            // __ Это для того, чтобы во внешнем сервисе не бегать по таблицам + может быть ситуация
            // __ когда Процедуры указаны для Модели, но не указаны для Чехла
            // __ Тогда будем смотреть в Чехол, а если там ничего нет, тогда идем в Модель
            $table->string('cover_code_1c')->nullable()->comment('Код из 1С Чехла Модели той Спецификации, которая привязывается в Строке Заявки');

            // __ Переделываем в integer
            // 1. Сначала принудительно сбрасываем старый дефолт, чтобы он не мешал конвертации
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" DROP DEFAULT');

            // 2. Меняем тип данных с явным приведением через USING cast
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" TYPE INTEGER USING "cut_length"::INTEGER');

            // 3. Ставим новый дефолт (уже как число) и делаем колонку NOT NULL
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" SET DEFAULT 0');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" SET NOT NULL');


            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" DROP DEFAULT');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" TYPE INTEGER USING "cut_width"::INTEGER');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" SET DEFAULT 0');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" SET NOT NULL');

            //$table->integer('cut_length')->default(0)->change();
            //$table->integer('cut_width')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutting_task_lines', function (Blueprint $table) {
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" DROP DEFAULT');

            // Переводим обратно в VARCHAR (text)
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" TYPE VARCHAR(255) USING "cut_length"::VARCHAR');

            // Возвращаем строковый дефолт
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" SET DEFAULT \'0\'');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_length" SET NOT NULL');

            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" DROP DEFAULT');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" TYPE VARCHAR(255) USING "cut_width"::VARCHAR');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" SET DEFAULT \'0\'');
            DB::statement('ALTER TABLE "cutting_task_lines" ALTER COLUMN "cut_width" SET NOT NULL');

            //$table->string('your_column_name')->default('0')->change();
            //$table->string('cut_width')->default('0')->change();

            // __ Удаляем созданные столбцы
            $table->dropColumn([
                'detail',
                'expense',
                'cut_detail_amount',
                'base_code_1c',
                'cover_code_1c',
            ]);
        });
    }
};
