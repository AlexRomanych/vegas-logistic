<?php

namespace App\Models\Models;

// use App\Models\Order\Line;
use App\Models\Manufacture\Cells\Sewing\SewingOperation;
use App\Models\Manufacture\Cells\Sewing\SewingOperationModelPivot;
use App\Models\Manufacture\Cells\Sewing\SewingOperationSchema;
use App\Services\ModelsService;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

final class Model extends LaravelModel
{
    protected $primaryKey = CODE_1C;
    protected $keyType = 'string';
    public $incrementing = false;

    public const AVERAGE_MODEL_NAME = 'AVERAGE';                                                                           // Название средней (расчетной) модели

    //attract Определяем признаки принадлежности модели к оборудованию
    public const MACHINE_AUTO = 'Автоматы';
    public const MACHINE_UNIVERSAL = 'УШМ';
    public const MACHINE_SOLID = 'Глух';
    public const MACHINE_SOLID_LIGHT = 'Глух';
    public const MACHINE_SOLID_HARD = 'сложные';


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


    protected $casts = [
        'active'       => 'boolean',
        'line'         => 'boolean',
        'base_height'  => 'float',
        'cover_height' => 'float',
    ];

    protected $guarded = [];

    // __ Задаем вычисляемые свойства
    protected $appends = [
        'is_auto',
        'is_universal',
        'is_solid',
        'is_solid_lite',
        'is_solid_hard',
        'is_undefined',
        'is_average',
        'machine_type',
    ];                                                                                                                 // Задаем типы швейных машин, исходя из свойства sewing_machine

    // info Получаем тип швейной машины для матраса
    public function getMachineTypeAttribute(): string
    {
        return match (true) {
            $this->getIsAutoAttribute()      => ModelsService::TYPE_AUTO,
            $this->getIsUniversalAttribute() => ModelsService::TYPE_UNIVERSAL,
            $this->getIsSolidHardAttribute() => ModelsService::TYPE_SOLID_HARD,
            $this->getIsSolidLiteAttribute() => ModelsService::TYPE_SOLID_LITE,
            $this->getIsAverageAttribute()   => ModelsService::TYPE_AVERAGE,
            // $this->getIsUndefinedAttribute() => ModelsService::TYPE_UNDEFINED,
            default                          => ModelsService::TYPE_UNDEFINED
        };
    }

    //info Проверяем на АШМ
    public function getIsAutoAttribute(): bool
    {
        return mb_stripos($this->sewing_machine, self::MACHINE_AUTO) !== false;            // без учета регистра
    }

    //info Проверяем на УШМ
    public function getIsUniversalAttribute(): bool
    {
        return mb_stripos($this->sewing_machine, self::MACHINE_UNIVERSAL) !== false;        // без учета регистра
    }

    //info Проверяем на Глухой
    public function getIsSolidAttribute(): bool
    {
        return mb_stripos($this->sewing_machine, self::MACHINE_SOLID) !== false;            // без учета регистра
    }

    //info Проверяем на Глухой сложный
    public function getIsSolidHardAttribute(): bool
    {
        return mb_stripos($this->sewing_machine, self::MACHINE_SOLID_HARD) !== false;       // без учета регистра
    }

    //info Проверяем на Глухой простой
    public function getIsSolidLiteAttribute(): bool
    {
        return ($this->getIsSolidAttribute() && !$this->getIsSolidHardAttribute());
    }

    //info Проверяем на Неопределенность
    public function getIsUndefinedAttribute(): bool
    {
        return is_null($this->sewing_machine) || $this->sewing_machine === '';
    }

    // info Проверяем на то, что модель является Average
    public function getIsAverageAttribute(): bool
    {
        return Str::contains(
            mb_strtolower($this->code_1c),
            [
                mb_strtolower(CLIENT_AVERAGE_MATTRESS_PREFIX),
                mb_strtolower(CLIENT_AVERAGE_ACCESSORY_PREFIX)
            ]
        );
    }


    //--- -------------------------------------------------------------------------------
    //--- ------------------------------ Scopes -----------------------------------------
    //--- -------------------------------------------------------------------------------
    public function scopeBasics(Builder $query, array $modelTypes = ['000000001', '000000002', '000000012']): Builder
    {
        // __ Фильтруем...
        return $query
            ->whereHas('modelType', function ($q) use ($modelTypes) {
                $q->whereIn('code_1c', $modelTypes);
            })

            // __ 0 => Вариант исполнения
            // __ 3 => Архив
            // __ 4 => Выпускается
            // __ 5 => Разовый выпуск
            // __ 6 => Без признака
            // __ 7 => На паузе
            ->whereHas('modelManufactureStatus', function ($q) {
                $q->whereIn('id', ['0', '3', '4', /*'5', '6', '7'*/]);
            });
            // __ ...и сразу подгружаем связь, чтобы не забыть в контроллере
            // ->with([
            //     'type' => function ($q) {
            //         $q->select('id', 'name', 'code_1c'); // Оптимизация: берем только нужные колонки
            //     }
            // ]);
    }


    //--- -------------------------------------------------------------------------------
    //--- -------------------------- Relations ------------------------------------------
    //--- -------------------------------------------------------------------------------

    // Relations: Связь с коллекцией
    public function collection(): BelongsTo
    {
        return $this->belongsTo(ModelCollection::class, 'model_collection_code_1c', CODE_1C);
    }


    // Relations: Связь со спецификацией
    public function constructs(): HasMany
    {
        return $this->hasMany(ModelConstruct::class, 'model_code_1c', CODE_1C);
    }


    // Relations: Связь Модели с Чехлом
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Model::class, 'cover_code_1c', CODE_1C);
    }


    // Relations: Связь Чехла с Моделью
    public function base(): HasOne
    {
        return $this->hasOne(Model::class, 'cover_code_1c', CODE_1C);
    }


    // Relations: Связь с Типом модели
    public function modelType(): BelongsTo
    {
        return $this->belongsTo(ModelType::class, 'model_type_code_1c', CODE_1C);
    }


    // Relations: Связь с Типом производства
    public function modelManufactureStatus(): BelongsTo
    {
        return $this->belongsTo(ModelManufactureStatus::class, 'model_manufacture_status_id', 'id');
    }


    // Relations: Связь Модели со Схемой Типовых операций Пошива
    public function sewingSchema(): BelongsTo {
        return $this->belongsTo(SewingOperationSchema::class, 'sewing_operation_schema_id', 'id');
    }


    // Relations: Связь с Операциями
    public function sewingOperations(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                SewingOperation::class,           // Класс, с которым связываемся
                SewingOperationModelPivot::TABLE,   // Промежуточная Таблица, связывающая классы
                'model_code_1c',           // Ключ в промежуточной таблице, связывающий с текущим классом
                'sewing_operation_id'      // Ключ в промежуточной таблице, связывающий с классом, с которым связываемся
            )
            ->using(SewingOperationModelPivot::class)
            ->withPivot(['ratio', 'amount', 'position', 'condition']);
    }

}
