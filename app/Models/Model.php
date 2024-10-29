<?php

namespace App\Models;

use App\Models\Order\Line;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Model extends LaravelModel
{
    public const AVERAGE_MODEL_NAME = 'AVERAGE';                // Название средней (расчетной) модели
    //'code1C'                          ('Код по 1С')
    //'type'                            ('Тип продукции.Наименование (матрас, наматрасник и тд.)')
    //'serial'                          ('Серия')
    //'name'                            ('Имя отчеты')
    //'name_1C'                         ('Наименование')
    //'base_height'                     ('Стандартная высота матраса')
    //'cover_height'                    ('Стандартная высота чехла')
    //'textile'                         ('Ткань')
    //'textile_combination'             ('Состав ткани')
    //'cover_type'                      ('Тип чехла')
    //'zipper'                          ('Молния')
    //'spacer'                          ('Прокладочный материал')
    //'stitch_pattern'                  ('Рисунок стежки')
    //'pack_type'                       ('Вид упаковки')
    //'base_composition'                ('Состав мягкого элемента')
    //'side_foam'                       ('ППУ бортов')
    //'base_block'                      ('Базовый блок')
    //'load'                            ('Нагрузка')
    //'guarantee'                       ('Гарантийный срок, мес.')
    //'life'                            ('Срок службы, лет')
    //'cover_mark'                      ('Маркировка чехла')
    //'model_mark'                      ('Маркировка матраса')
    //'group'                           ('Номер группы модели для сортировки')
    //'owner'                           ('Владелец')
    //'line'                            ('Возможность изготовления на линии (Lamit)')
    //'sewing_machine'                  ('Группы ДСЗ (Тип швейных машин: АШМ, УШМ, Обшивка и Прочее)')
    //'pack_density'                    ('Плотность упаковки')
    //'side_height'                     ('Высота бортов. Может быть 14.5, а может быть 13/8')
    //'pack_weight_rb'                  ('Вес упаковки РБ')
    //'pack_weight_ex'                  ('Вес упаковки экспорт')
    //'manufacture_type'                ('Вид производства')
    //'weight'                          ('Вес в г. куб. см.')
    //'barcode'                         ('Штрихкод')


    protected $primaryKey = 'code1C';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code1C',
        'type', 'serial',
        'name', 'name_1C',
        'base_height', 'cover_height',
        'textile', 'textile_combination', 'cover_type', 'zipper', 'spacer', 'stitch_pattern',
        'pack_type', 'pack_density',
        'base_composition', 'base_block', 'side_foam',
        'load',
        'guarantee',
        'life',
        'cover_mark', 'model_mark',
        'group',
        'owner',
        'line',
        'sewing_machine',
        'side_height',
        'pack_weight_rb', 'pack_weight_ex',
        'manufacture_type',
        'weight',
        'barcode',
        'collection_id',
        'base', 'cover',
        'active'
    ];

    protected $hidden = [
        'code1C', 'collection_id',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'line' => 'boolean',
        'base' => 'array',
        'cover' => 'array',
    ];

    protected $guarded = [];

    protected $attributes = [
        'code1C'         => '000000000',
        'type'           => 'Матрас',
        'serial'         => 'МВ 314',
        'name'           => 'Матрас',
        'name_1C'        => 'МВ 314.Матрас.',
        'owner'          => 'Матрасы ортопедические обшитые тип МВ314',
        'base_height'    => 0.2,
        'cover_height'   => 0.2,
        'pack_density'   => 0,
        'pack_weight_rb' => 0,
        'pack_weight_ex' => 0,
        'weight'         => 0,
        'active'         => 1,
        'collection_id'  => '000000000',
    ];




    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id', 'code1C');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

}
