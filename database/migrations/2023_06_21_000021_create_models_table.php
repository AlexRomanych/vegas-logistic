<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('models', function (Blueprint $table) {

            $table->string('code1C')->primary()->comment('Код по 1С');
            $table->string('type')->nullable(false)->comment('Тип продукции.Наименование (матрас, наматрасник и тд.)');
            $table->string('serial')->nullable(false)->comment('Серия');
            $table->string('name')->unique()->nullable(false)->comment('Имя отчеты');
            $table->string('name_1C')->unique()->nullable(false)->comment('Наименование');
            $table->decimal('base_height',4,3)->nullable(false)->comment('Стандартная высота матраса');
            $table->decimal('cover_height',4,3)->nullable(false)->comment('Стандартная высота чехла');
            $table->string('textile')->nullable()->comment('Ткань');
            $table->string('textile_combination')->nullable()->comment('Состав ткани');
            $table->string('cover_type')->nullable()->comment('Тип чехла');
            $table->string('zipper')->nullable()->comment('Молния');
            $table->string('spacer')->nullable()->comment('Прокладочный материал');
            $table->string('stitch_pattern')->nullable()->comment('Рисунок стежки');
            $table->string('pack_type')->nullable()->comment('Вид упаковки');
            $table->string('base_composition')->nullable()->comment('Состав мягкого элемента');
            $table->string('side_foam')->nullable()->comment('ППУ бортов');
            $table->string('base_block')->nullable()->comment('Базовый блок');
            $table->unsignedSmallInteger('load')->nullable()->comment('Нагрузка');
            $table->unsignedSmallInteger('guarantee')->nullable()->comment('Гарантийный срок, мес.');
            $table->unsignedSmallInteger('life')->nullable()->comment('Срок службы, лет');
            $table->string('cover_mark')->nullable()->comment('Маркировка чехла');
            $table->string('model_mark')->nullable()->comment('Маркировка матраса');
            $table->unsignedTinyInteger('group')->nullable()->comment('Номер группы модели для сортировки');
            $table->string('owner')->nullable()->comment('Владелец');
            $table->unsignedTinyInteger('line')->nullable()->comment('Возможность изготовления на линии (Lamit)');
            $table->string('sewing_machine')->nullable()->comment('Группы ДСЗ (Тип швейных машин: АШМ, УШМ, Обшивка и Прочее)');
            $table->decimal('pack_density',7,6)->nullable(false)->comment('Плотность упаковки');
            $table->string('side_height')->nullable()->comment('Высота бортов. Может быть 14.5, а может быть 13/8');
            $table->decimal('pack_weight_rb',5,4)->nullable(false)->comment('Вес упаковки РБ');
            $table->decimal('pack_weight_ex',5,4)->nullable(false)->comment('Вес упаковки экспорт');
            $table->string('manufacture_type')->nullable()->comment('Вид производства');
            $table->decimal('weight',7,6)->nullable(false)->comment('Вес в г. куб. см.');
            $table->string('barcode')->nullable()->comment('Штрихкод');

            $table->json('base')->nullable()->comment('Спецификация МЭ');
            $table->json('cover')->nullable()->comment('Спецификация чехла');

            $table->unsignedTinyInteger('active')->nullable(false)->default(0)->comment('Активный или нет');

            $table->timestamps();

            $table->string('collection_id');
            $table->foreign('collection_id')->references('code1C')->on('collections');


//            Const CODE1C_COL = 1                            ' ['cd'] Код
//            Const TYPE_COL = 2                              ' ['tp'] Тип продукции.Наименование
//            Const SERIAL_COL = 3                            ' ['sl'] Серия
//            Const NAME_REPORT_COL = 4                       ' ['nr'] Имя отчеты
//            Const NAME_COL = 5                              ' ['nm'] Наименование
//            Const BASE_HEIGHT_COL = 6                       ' ['bh'] Стандартная высота
//            Const COVER_HEIGHT_COL = 7                      ' ['ch'] Стандартная высота чехла
//            Const TEXTILE_COL = 8                           ' ['tl'] Ткань
//            Const TEXTILE_COMBINATION_COL = 9               ' ['tc'] Состав ткани
//            Const COVER_TYPE_COL = 10                       ' ['ct'] Тип чехла.Наименование
//            Const ZIP_COL = 11                              ' ['zp'] Молния
//            Const SPACER_COL = 12                           ' ['sr'] Прокладочный материал.Наименование
//            Const STITCH_PATTERN_COL = 13                   ' ['sp'] Рисунок стежки.Наименование
//            Const PACK_TYPE_COL = 14                        ' ['pk'] Вид упаковки.Наименование
//            Const BASE_COMPOSITION_COL = 15                 ' ['bn'] Состав мягкого элемента
//            Const SIDE_FOAM_COL = 16                        ' ['sf'] ППУ бортов
//            Const BASE_BLOCK_COL = 17                       ' ['bb'] Базовый блок
//            Const LOAD_COL = 18                             ' ['ld'] Нагрузка
//            Const GUARANTEE_COL = 19                        ' ['gt'] Гарантийный срок, мес.
//            Const LIFE_COL = 20                             ' ['lf'] Срок службы, лет
//            Const COVER_MARK_COL = 21                       ' ['cm'] Маркировка чехла
//            Const MODEL_MARK_COL = 22                       ' ['bm'] Маркировка матраса
//            Const GROUP_COL = 23                            ' ['gn'] Номер группы модели для сортировки
//            Const OWNER_COL = 24                            ' ['or'] Владелец
//            Const LINE_COL = 25                             ' ['ln'] Возможно изготовление на линии
//            Const SEWING_MACHINE_COL = 26                   ' ['sm'] Группы ДСЗ
//            Const PACK_DENSITY_COL = 27                     ' ['dp'] Плотность упаковки
//            Const SIDE_HEIGHT_COL = 28                      ' ['sh'] Высота бортов
//            Const PACK_WEIGHT_RB_COL = 29                   ' ['wr'] Вес упаковки РБ
//            Const PACK_WEIGHT_EX_COL = 30                   ' ['we'] Вес упаковки экспорт
//            Const MANUF_TYPE_COL = 31                       ' ['mt'] Вид производства
//            Const WEIGHT_COL = 32                           ' ['wg'] Вес в г.
//            Const BARCODE_COL = 33                          ' ['bc'] Штрих код
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
