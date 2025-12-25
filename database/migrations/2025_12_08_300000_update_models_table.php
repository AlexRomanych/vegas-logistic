<?php

use App\Models\Models\ModelManufactureGroup;
use App\Models\Models\ModelManufactureStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'models';

    public function up(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);

        Schema::create(self::TABLE_NAME, function (Blueprint $table) {

            $table->string(CODE_1C, CODE_1C_LENGTH)->primary()->comment('Код по 1С.1');

            // Relations: __ Связь со статусом (Выпускается, Архив ...)
            $table->foreignIdFor(ModelManufactureStatus::class)
                ->nullable()
                ->comment('Порядок, Статус.2 (Выпускается, Архив, ...)')
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Relations: __ Связь с коллекцией
            $table->string('model_collection_code_1c')->nullable()->comment('Коллекция.Код.3');;
            $table->foreign('model_collection_code_1c')
                ->references(CODE_1C)
                ->on('model_collections');

            // collection_name  Коллекция.Наименование.4      // warn Нет в таблице Models

            // Relations: __ Связь с типом модели (Матрас, Наматрасник ...)
            $table->string('model_type_code_1c')->nullable()->comment('Тип продукции.Код.5 (Матрас, Наматрасник, ...)');;
            $table->foreign('model_type_code_1c')
                ->references(CODE_1C)
                ->on('model_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // model_type_name  Тип продукции.Наименование.6  // warn Нет в таблице Models

            $table->string('serial')->nullable()->comment('Серия.7');

            $table->string('name')->nullable(false)->comment('Наименование.8');
            $table->string('name_short')->nullable()->comment('Наименование краткое.9');
            $table->string('name_common')->nullable()->comment('Наименование общее.10');
            $table->string('name_report')->nullable()->comment('Имя отчеты.11');

            $table->unique(['name', 'model_manufacture_status_id']);    // warn Ставим сомнительное ограничение уникальности

            // __ Выносим во внешний ключ, а здесь только копия, куда заносим код 1С для чехла, даже на него нельзя указать внешний ключ
            $table->string('cover_code_1c_copy')->nullable()->comment('Копия Модель чехла.Код.12');
            $table->string('cover_name_1c')->nullable()->comment('Модель чехла.Наименование.13');

            $table->decimal('base_height', 4, 3)->nullable(false)->default(0.000)->comment('Стандартная высота.14 (высота матраса, м)');
            $table->decimal('cover_height', 4, 3)->nullable(false)->default(0.000)->comment('Стандартная высота чехла.15 (высота чехла, м)');

            $table->string('textile')->nullable()->comment('Ткань.16');
            $table->string('textile_composition')->nullable()->comment('Состав ткани.17');
            $table->string('cover_type')->nullable()->comment('Тип чехла.Наименование.18');
            $table->string('zipper')->nullable()->comment('Молния.19');
            $table->string('spacer')->nullable()->comment('Прокладочный материал.Наименование.20');
            $table->string('stitch_pattern')->nullable()->comment('Рисунок стежки . Наименование.21');
            $table->string('pack_type')->nullable()->comment('Вид упаковки.Наименование.22');
            $table->string('base_composition')->nullable()->comment('Состав мягкого элемента.23');
            $table->string('side_foam')->nullable()->comment('ППУ бортов.24');
            $table->string('base_block')->nullable()->comment('Базовый блок.25');
            $table->unsignedInteger('load')->nullable()->comment('Нагрузка.26');
            $table->unsignedInteger('guarantee')->nullable()->comment('Гарантийный срок, мес.27');
            $table->unsignedInteger('life')->nullable()->comment('Срок службы, лет.28');
            $table->string('cover_mark')->nullable()->comment('Маркировка чехла.29');
            $table->string('model_mark')->nullable()->comment('Маркировка матраса.30');

            // Relations: __ Связь с Номер группы модели для сортировки (ЗаявкаФ)
            $table->foreignIdFor(ModelManufactureGroup::class)
                ->nullable(false)
                ->default(0)
                ->comment('Номер группы модели для сортировки.31')
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('owner')->nullable()->comment('Владелец.32');
            $table->boolean('lamit')->nullable()->comment('Возможность изготовления на линии (Lamit).33');
            $table->string('sewing_machine')->nullable()->comment('Группы ДСЗ.34 (Тип швейных машин: АШМ, УШМ, Обшивка и Прочее)');
            $table->string('kant')->nullable()->comment('Кант.35');
            $table->string('tkch')->nullable()->comment('ТКЧ.36 (Типовая конструкция чехла)');
            $table->decimal('pack_density', 7, 6)->nullable()->comment('Плотность упаковки.37');
            $table->string('side_height')->nullable()->comment('Высота бортов.38');
            $table->decimal('pack_weight_rb', 5, 4)->nullable()->comment('Вес упаковки РБ.39');
            $table->decimal('pack_weight_ex', 5, 4)->nullable()->comment('Вес упаковки экспорт.40');

            // Relations: __ Связь с Видом производства (Производство матрасов, Производство стеганных полотен ...)
            $table->string('model_manufacture_type_code_1c')->nullable()->comment('Вид производства.Код.41 (Производство матрасов, Производство стеганных полотен ...)');;
            $table->foreign('model_manufacture_type_code_1c')
                ->references(CODE_1C)
                ->on('model_manufacture_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // model_manufacture_type_name  Вид производства.Наименование	42 // warn Нет в таблице Models

            $table->decimal('weight', 7, 6)->nullable(false)->comment('Вес в г.43 (куб. см. ???)');
            $table->string('barcode')->nullable()->comment('Штрих код.44');

            $table->jsonb('base')->nullable()->comment('Спецификация МЭ');
            $table->jsonb('cover')->nullable()->comment('Спецификация чехла');

            $table->boolean('active')->nullable(false)->default(true)->comment('Активный или Архивный.45');
            $table->unsignedSmallInteger('status')->nullable()->comment('Статус');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->json('meta')->nullable()->comment('Метаданные');

            $table->timestamps();

            // base_construct_id  __ Конструкция МЭ    - Переделать на FK   - Оставляем эту связь в ModelConstruct
            // cover_construct_id __ Конструкция чехла - Переделать на FK   - Оставляем эту связь в ModelConstruct

        });

        // __ Делаем отдельным обращением к фасаду, потому что нужно, чтобы был сформирован первичный ключ,
        // __ а миграция выполняется в транзакции
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            // $table->string('cover_code_1c')->nullable()->comment('Модель чехла.Код.12');

            // Relations: __ Связь с Чехлом
            $table->string('cover_code_1c', CODE_1C_LENGTH)
                ->nullable()
                ->after('cover_code_1c_copy')
                ->comment('Модель чехла.Код.12');
            $table->foreign('cover_code_1c')
                ->references(CODE_1C)
                ->on(self::TABLE_NAME)
                ->nullOnDelete()
                ->cascadeOnUpdate();
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
