<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Enums\ElementTypes;
use App\Models\Client;
use App\Models\Models\Model;
use App\Models\Models\ModelCollection;
use App\Models\Models\ModelManufactureGroup;
use App\Models\Models\ModelManufactureStatus;
use App\Models\Models\ModelManufactureType;
use App\Models\Models\ModelType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ModelsService implements VegasDataUpdateContract
{

    // --- Типы моделей
    public const TYPE_UNIVERSAL = 'universal';
    public const TYPE_AUTO = 'auto';
    public const TYPE_SOLID_HARD = 'solid_hard';
    public const TYPE_SOLID_LITE = 'solid_lite';
    public const TYPE_UNDEFINED = 'undefined';
    public const TYPE_AVERAGE = 'average';


    private static array $modelsCollectionsCacheByCode1C = [];
    private static array $modelsCollectionsCacheByName = [];
    private static array $modelsCacheByCode1C = [];
    private static array $modelsCacheByName = [];
    private static array $modelsCoverCacheByCode1C = [];    // Кэш для чехлов моделей, используем в обновлении спецификаций
    private static array $modelManufactureTypeCacheByCode1C = [];
    private static array $modelManufactureTypeCacheByName = [];
    private static array $modelTypeCacheByCode1C = [];
    private static array $modelTypeCacheByName = [];
    private static array $modelManufactureStatusCacheById = [];
    private static array $modelManufactureStatusCacheByName = [];
    private static array $modelManufactureGroupCacheById = [];
    private static array $modelManufactureGroupCacheByName = [];

    // line -------------------------------------------------------------------
    // line ------------------------- Модели ----------------------------------
    // line -------------------------------------------------------------------

    /**
     * ___ Проверка на заполненность кэша моделей
     * @return bool
     */
    private static function isModelsCashed(): bool
    {
        return count(self::$modelsCacheByCode1C) > 0 && count(self::$modelsCacheByName) > 0;
    }

    /**
     * ___ Получение моделей из БД и заполнение кэша
     * @return array
     */
    public static function getModels(): array
    {
        if (!self::isModelsCashed()) {
            $models = Model::query()
                ->with(['modelType', 'cover', 'base', 'sewingSchema.operations', 'sewingOperations'])
                ->get();

            foreach ($models as $model) {
                self::$modelsCacheByCode1C[$model->code_1c]           = $model;
                self::$modelsCacheByName[mb_strtolower($model->name)] = $model;

                // Добавление чехла в кэш
                if ($model->cover_code_1c) {
                    self::$modelsCoverCacheByCode1C[$model->cover_code_1c] = $model;
                }
            }
        }

        return self::$modelsCacheByCode1C;
    }

    /**
     * ___ Получение модели по коду 1С
     * @param string $code1C
     * @return Model|null
     */
    public static function getModelByCode1C(string $code1C): ?Model
    {
        self::getModels();
        return self::$modelsCacheByCode1C[$code1C] ?? null;
    }

    /**
     * ___ Получение модели по имени
     * @param string $name
     * @return Model|null
     */
    public static function getModelByName(string $name): ?Model
    {
        self::getModels();
        return self::$modelsCacheByName[$name] ?? null;
    }

    /**
     * ___ Получение чехла модели по коду 1С
     * @param string $code1C
     * @return Model|null
     */
    public static function getModelCoverByCode1C(string $code1C): ?Model
    {
        self::getModels();
        return self::$modelsCoverCacheByCode1C[$code1C] ?? null;
    }
    // line -------------------------------------------------------------------


    // line -------------------------------------------------------------------
    // line ------------------------- Коллекции -------------------------------
    // line -------------------------------------------------------------------

    /**
     * ___ Проверка на заполненность кэша коллекций моделей
     * @return bool
     */
    private static function isModelsCollectionsCashed(): bool
    {
        return count(self::$modelsCollectionsCacheByCode1C) > 0 && count(self::$modelsCollectionsCacheByName) > 0;
    }

    /**
     * ___ Получение коллекций моделей из БД и заполнение кэша
     * @return void
     */
    private static function getModelsCollections(): void
    {
        if (!self::isModelsCollectionsCashed()) {
            $modelsCollections = ModelCollection::all();

            foreach ($modelsCollections as $modelsCollection) {
                self::$modelsCollectionsCacheByCode1C[$modelsCollection->code_1c] = $modelsCollection;
                self::$modelsCollectionsCacheByName[$modelsCollection->name]      = $modelsCollection;
            }
        }
    }

    /**
     * ___ Получение коллекции модели по коду 1С
     * @param string $code1C
     * @return ModelCollection|null
     */
    public static function getModelsCollectionByCode1C(string $code1C): ?ModelCollection
    {
        self::getModelsCollections();
        return self::$modelsCollectionsCacheByCode1C[$code1C] ?? null;
    }

    /**
     * ___ Получение коллекции модели по имени
     * @param string $name
     * @return ModelCollection|null
     */
    public static function getModelsCollectionByName(string $name): ?ModelCollection
    {
        self::getModelsCollections();
        return self::$modelsCollectionsCacheByName[$name] ?? null;
    }
    // line -------------------------------------------------------------------

    // line -------------------------------------------------------------------
    // line ---------------------- Тип производства ---------------------------
    // line -------------------------------------------------------------------

    /**
     * ___ Проверка на заполненность кэша Типа производства моделей
     * @return bool
     */
    private static function isModelManufactureTypesCashed(): bool
    {
        return count(self::$modelManufactureTypeCacheByCode1C) > 0 && count(self::$modelManufactureTypeCacheByName) > 0;
    }

    /**
     * ___ Получение Типа производства моделей из БД и заполнение кэша
     * @return void
     */
    private static function getModelManufactureTypes(): void
    {
        if (!self::isModelManufactureTypesCashed()) {
            $modelManufactureTypes = ModelManufactureType::all();

            foreach ($modelManufactureTypes as $modelManufactureType) {
                self::$modelManufactureTypeCacheByCode1C[$modelManufactureType->code_1c] = $modelManufactureType;
                self::$modelManufactureTypeCacheByName[$modelManufactureType->name]      = $modelManufactureType;
            }
        }
    }

    /**
     * ___ Получение Типа производства модели по коду 1С
     * @param string $code1C
     * @return ModelManufactureType|null
     */
    public static function getModelManufactureTypeByCode1C(string $code1C): ?ModelManufactureType
    {
        self::getModelManufactureTypes();
        return self::$modelManufactureTypeCacheByCode1C[$code1C] ?? null;
    }

    /**
     * ___ Получение Типа производства модели по имени
     * @param string $name
     * @return ModelManufactureType|null
     */
    public static function getModelManufactureTypeByName(string $name): ?ModelManufactureType
    {
        self::getModelManufactureTypes();
        return self::$modelManufactureTypeCacheByName[$name] ?? null;
    }

    // line -------------------------------------------------------------------

    // line -------------------------------------------------------------------
    // line ------------------------- Тип модели ------------------------------
    // line -------------------------------------------------------------------

    /**
     * ___ Проверка на заполненность кэша Типа моделей
     * @return bool
     */
    private static function isModelTypesCashed(): bool
    {
        return count(self::$modelTypeCacheByCode1C) > 0 && count(self::$modelTypeCacheByName) > 0;
    }

    /**
     * ___ Получение Типа моделей из БД и заполнение кэша
     * @return void
     */
    private static function getModelTypes(): void
    {
        if (!self::isModelTypesCashed()) {
            $modelTypes = ModelType::all();

            foreach ($modelTypes as $modelType) {
                self::$modelTypeCacheByCode1C[$modelType->code_1c] = $modelType;
                self::$modelTypeCacheByName[$modelType->name]      = $modelType;
            }
        }
    }

    /**
     * ___ Получение Типа модели по коду 1С
     * @param string $code1C
     * @return ModelType|null
     */
    public static function getModelTypeByCode1C(string $code1C): ?ModelType
    {
        self::getModelTypes();
        return self::$modelTypeCacheByCode1C[$code1C] ?? null;
    }

    /**
     * ___ Получение Типа модели по имени
     * @param string $name
     * @return ModelType|null
     */
    public static function getModelTypeByName(string $name): ?ModelType
    {
        self::getModelTypes();
        return self::$modelTypeCacheByName[$name] ?? null;
    }

    // line -------------------------------------------------------------------

    // line -------------------------------------------------------------------
    // line ------------------ Тип статуса производства -----------------------
    // line -------------------------------------------------------------------

    /**
     * ___ Проверка на заполненность кэша Статуса производства моделей
     * @return bool
     */
    private static function isModelManufactureStatusesCashed(): bool
    {
        return count(self::$modelManufactureStatusCacheById) > 0 && count(self::$modelManufactureStatusCacheByName) > 0;
    }

    /**
     * ___ Получение Статусов производства моделей из БД и заполнение кэша
     * @return void
     */
    private static function getModelManufactureStatuses(): void
    {
        if (!self::isModelManufactureStatusesCashed()) {
            $modelManufactureStatuses = ModelManufactureStatus::all();

            foreach ($modelManufactureStatuses as $modelManufactureStatus) {
                self::$modelManufactureStatusCacheById[$modelManufactureStatus->id]     = $modelManufactureStatus;
                self::$modelManufactureStatusCacheByName[$modelManufactureStatus->name] = $modelManufactureStatus;
            }
        }
    }

    /**
     * ___ Получение Статуса производства модели по id
     * @param int $id
     * @return ModelManufactureStatus|null
     */
    public static function getModelManufactureStatusById(int $id): ?ModelManufactureStatus
    {
        self::getModelManufactureStatuses();
        return self::$modelManufactureStatusCacheById[$id] ?? null;
    }

    /**
     * ___ Получение Статуса производства модели по имени
     * @param string $name
     * @return ModelManufactureStatus|null
     */
    public static function getModelManufactureStatusByName(string $name): ?ModelManufactureStatus
    {
        self::getModelManufactureStatuses();
        return self::$modelManufactureStatusCacheByName[$name] ?? null;
    }

    // line -------------------------------------------------------------------

    // line -------------------------------------------------------------------
    // line --------------- Группы сортировки производства --------------------
    // line -------------------------------------------------------------------

    /**
     * ___ Проверка на заполненность кэша Группы сортировки производства моделей
     * @return bool
     */
    private static function isModelManufactureGroupsCashed(): bool
    {
        return count(self::$modelManufactureGroupCacheById) > 0 && count(self::$modelManufactureGroupCacheByName) > 0;
    }

    /**
     * ___ Получение Групп сортировки производства моделей из БД и заполнение кэша
     * @return void
     */
    private static function getModelManufactureGroups(): void
    {
        if (!self::isModelManufactureGroupsCashed()) {
            $modelManufactureGroups = ModelManufactureGroup::all();

            foreach ($modelManufactureGroups as $modelManufactureGroup) {
                self::$modelManufactureGroupCacheById[$modelManufactureGroup->id]     = $modelManufactureGroup;
                self::$modelManufactureGroupCacheByName[$modelManufactureGroup->name] = $modelManufactureGroup;
            }
        }
    }

    /**
     * ___ Получение Группы сортировки производства модели по id
     * @param int $id
     * @return ModelManufactureGroup|null
     */
    public static function getModelManufactureGroupById(int $id): ?ModelManufactureGroup
    {
        self::getModelManufactureGroups();
        return self::$modelManufactureGroupCacheById[$id] ?? null;
    }

    /**
     * ___ Получение Группы сортировки производства модели по имени
     * @param string $name
     * @return ModelManufactureGroup|null
     */
    public static function getModelManufactureGroupByName(string $name): ?ModelManufactureGroup
    {
        self::getModelManufactureGroups();
        return self::$modelManufactureGroupCacheByName[$name] ?? null;
    }

    // line -------------------------------------------------------------------


    /**
     * ___ Создание или обновление сущности модели
     * ___ Сущности модели - это связанные с ней отношения, не влияющие на сущность самой модели
     * __ Коллекции
     * __ Типы модели (матрас, наматрасник, и т.д.)
     * __ Типы производства (пр-во матрасов, пр-во полотен стеганных и т.д.)
     * __ Статусы производства (Выпускается, архив и т.д.)
     * __ Группы сортировки
     * @param string $modelEntity Класс сущности
     * @param array $attributes Атрибуты сущности
     * @return ModelCollection|ModelManufactureType|ModelType|ModelManufactureStatus|ModelManufactureGroup|null
     */
    public static function createOrUpdateModelEntity(
        string $modelEntity = '',
        array  $attributes = []
    ): ModelCollection|ModelManufactureType|ModelType|ModelManufactureStatus|ModelManufactureGroup|null
    {
        if ($modelEntity === '') {
            return null;
        }


        if ($modelEntity === ModelCollection::class) {

            // __ Коллекция моделей
            if (!isset($attributes['model_collection_code_1c']) || !isset($attributes['model_collection_name'])) {
                return null;
            }

            $modelsCollection = self::getModelsCollectionByCode1C($attributes['model_collection_code_1c']);
            if (!$modelsCollection || $modelsCollection->name !== $attributes['model_collection_name'] || !$modelsCollection->active) {

                if ($modelsCollection) $modelsCollection->active = true;

                $insertedModelsCollection = ModelCollection::query()->updateOrCreate(
                    [
                        CODE_1C => $attributes['model_collection_code_1c'],
                    ],
                    [
                        'name'   => $attributes['model_collection_name'],
                        'active' => true,
                    ]
                );

                if (!$insertedModelsCollection) {
                    return null;
                }

                return $insertedModelsCollection;
            }
            return $modelsCollection;

        } else if ($modelEntity === ModelManufactureType::class) {

            // __ Тип производства
            if (!isset($attributes['model_manufacture_type_code_1c']) || !isset($attributes['model_manufacture_type_name'])) {
                return null;
            }

            $modelManufactureType = self::getModelManufactureTypeByCode1C($attributes['model_manufacture_type_code_1c']);
            if (!$modelManufactureType || $modelManufactureType->name !== $attributes['model_manufacture_type_name'] || !$modelManufactureType->active) {

                if ($modelManufactureType) $modelManufactureType->active = true;    // Устанавливаем в кэше, чтобы не перегружать БД

                $insertedManufactureType = ModelManufactureType::query()->updateOrCreate(
                    [
                        CODE_1C => $attributes['model_manufacture_type_code_1c'],
                    ],
                    [
                        'name'   => $attributes['model_manufacture_type_name'],
                        'active' => true,
                    ]
                );

                if (!$insertedManufactureType) {
                    return null;
                }

                return $insertedManufactureType;
            }

            return $modelManufactureType;

        } else if ($modelEntity === ModelType::class) {

            // __ Тип модели
            if (!isset($attributes['model_type_code_1c']) || !isset($attributes['model_type_name'])) {
                return null;
            }

            $modelType = self::getModelTypeByCode1C($attributes['model_type_code_1c']);
            if (!$modelType || $modelType->name !== $attributes['model_type_name'] || !$modelType->active) {

                if ($modelType) $modelType->active = true;

                $insertedModelType = ModelType::query()->updateOrCreate(
                    [
                        CODE_1C => $attributes['model_type_code_1c'],
                    ],
                    [
                        'name'   => $attributes['model_type_name'],
                        'active' => true,
                    ]
                );

                if (!$insertedModelType) {
                    return null;
                }

                return $insertedModelType;
            }
            return $modelType;

        } else if ($modelEntity === ModelManufactureStatus::class) {

            // __ Статус производства
            if (!isset($attributes['model_manufacture_status_id']) || !isset($attributes['model_manufacture_status_name'])) {
                return null;
            }

            $modelManufactureStatus = self::getModelManufactureStatusById($attributes['model_manufacture_status_id']);
            if (!$modelManufactureStatus || $modelManufactureStatus->name !== $attributes['model_manufacture_status_name'] || !$modelManufactureStatus->active) {

                if ($modelManufactureStatus) $modelManufactureStatus->active = true;

                $insertedModelManufactureStatus = ModelManufactureStatus::query()->updateOrCreate(
                    [
                        'id' => $attributes['model_manufacture_status_id'],
                    ],
                    [
                        'name'   => $attributes['model_manufacture_status_name'],
                        'active' => true,
                    ]
                );

                if (!$insertedModelManufactureStatus) {
                    return null;
                }

                return $insertedModelManufactureStatus;
            }
            return $modelManufactureStatus;

        } else if ($modelEntity === ModelManufactureGroup::class) {

            // __ Группа сортировки производства
            if (!isset($attributes['model_manufacture_group_id']) || !isset($attributes['model_manufacture_group_name'])) {
                return null;
            }

            $modelManufactureGroup = self::getModelManufactureGroupById($attributes['model_manufacture_group_id']);
            if (!$modelManufactureGroup || $modelManufactureGroup->name !== $attributes['model_manufacture_group_name'] || !$modelManufactureGroup->active) {

                if ($modelManufactureGroup) $modelManufactureGroup->active = true;

                $insertedModelManufactureGroup = ModelManufactureGroup::query()->updateOrCreate(
                    [
                        'id' => $attributes['model_manufacture_group_id'],
                    ],
                    [
                        'name'   => $attributes['model_manufacture_group_name'],
                        'active' => true,
                    ]
                );

                if (!$insertedModelManufactureGroup) {
                    return null;
                }

                return $insertedModelManufactureGroup;
            }
            return $modelManufactureGroup;
        }

        return null;
    }

    /**
     * ___ Установка всех сущностей моделей в неактивное состояние
     * @return void
     * @throws Throwable
     */
    public static function setActiveToFalseForModelsEntities(): void
    {
        DB::transaction(function () {
            ModelCollection::query()->update(['active' => false]);
            ModelManufactureType::query()->update(['active' => false]);
            ModelType::query()->update(['active' => false]);
            ModelManufactureStatus::query()->update(['active' => false]);
        });
    }


    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
        $fileName   = config('vegas.models_1C_json_name');
        $modelsList = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);

        Model::query()->update(['active' => 0]);

        foreach ($modelsList as $modelItem) {
            $base  = json_encode($modelItem['bs'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_SLASHES);
            $cover = json_encode($modelItem['cv'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
            //            $base = $modelItem['bs'];
            //            $cover = $modelItem['cv'];
            //            view('dd', ['data' => $modelItem]);
            //            return $base;

            Log::info($base);

            Model::query()->updateOrCreate(
                [
                    'code1C' => $modelItem['cd']
                ],
                [
                    'type'                => $modelItem['tp'],
                    'serial'              => $modelItem['sl'],
                    'name'                => $modelItem['nr'],
                    'name_1C'             => $modelItem['nm'],
                    'base_height'         => $modelItem['bh'],
                    'cover_height'        => $modelItem['ch'],
                    'textile'             => $modelItem['tl'],
                    'textile_combination' => $modelItem['tc'],
                    'cover_type'          => $modelItem['ct'],
                    'zipper'              => $modelItem['zp'],
                    'spacer'              => $modelItem['sr'],
                    'stitch_pattern'      => $modelItem['sp'],
                    'pack_type'           => $modelItem['pk'],
                    'base_composition'    => $modelItem['bn'],
                    'side_foam'           => $modelItem['sf'],
                    'base_block'          => $modelItem['bb'],
                    'load'                => $modelItem['ld'],
                    'guarantee'           => $modelItem['gt'],
                    'life'                => $modelItem['lf'],
                    'cover_mark'          => $modelItem['cm'],
                    'model_mark'          => $modelItem['bm'],
                    'group'               => $modelItem['gn'],
                    'owner'               => $modelItem['or'],
                    'line'                => $modelItem['ln'],
                    'sewing_machine'      => $modelItem['sm'],
                    'pack_density'        => $modelItem['dp'],
                    'side_height'         => $modelItem['sh'],
                    'pack_weight_rb'      => $modelItem['wr'],
                    'pack_weight_ex'      => $modelItem['we'],
                    'manufacture_type'    => $modelItem['mt'],
                    'weight'              => $modelItem['wg'],
                    'barcode'             => $modelItem['bc'],
                    'collection_id'       => $modelItem['ci'],
                    //                    'base' => json_encode($modelItem['bs'], JSON_UNESCAPED_UNICODE),
                    //                    'cover' => json_encode($modelItem['cv'], JSON_UNESCAPED_UNICODE),
                    'base'                => $base,
                    'cover'               => $cover,
                    'active'              => 1,
                ]
            );
        }

        $modelItem = $this->createFakeModel();
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

        $modelItem = $this->createFakeModel([
            'code1C' => '000000001',
            'name'   => 'Наматрасник',
            'owner'  => 'Наматрасники /с наполнителем/',
            'type'   => 'Наматрасник модифицирующий',
            'serial' => 'НП',
        ]);
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

        $modelItem = $this->createFakeModel([
            'code1C' => '000000002',
            'name'   => 'Чехол',
            'type'   => 'Чехол',
            'owner'  => 'Чехол',
            'serial' => '',
        ]);
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

        $modelItem = $this->createFakeModel([
            'code1C' => '000000003',
            'name'   => Model::AVERAGE_MODEL_NAME,
            'type'   => 'Статистическая модель',
            'owner'  => 'Статистическая модель',
            'serial' => '',
        ]);
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

    }

    // ___ Возвращаем фейковую модель
    public function createFakeModel(array $attributes = []): Model
    {
        $model           = new Model();
        $modelAttributes = $model->getAttributes();
        $modelAttributes = array_merge($modelAttributes, $attributes);

        if ($modelAttributes['name'] !== 'Чехол') {
            $modelAttributes['name_1C'] = $modelAttributes['serial'] . '.' . $modelAttributes['name'] . '.';     // составной атрибут
        } else {
            $modelAttributes['name_1C'] = 'Чехол';
        }
        return new Model($modelAttributes);
    }


    // line ---------------------------------------------------------------------------
    // line -- Проверка на принадлежность изделия к условной производственной группе --
    // line ---------------------------------------------------------------------------

    /**
     * ___ Возвращаем группу производства для модели
     * @param string|Model $data
     * @param string $name
     * @return string
     */
    public static function getElementTypeGroup(string|Model $data, string $name = ''): string
    {
        // !!! Порядок важен
        return match (true) {
            self::isElementCoversTypeGroup($data, $name) => ElementTypes::COVERS->value,
            self::isElementMattressTypeGroup($data)      => ElementTypes::MATTRESSES->value,
            self::isElementAccessoriesTypeGroup($data)   => ElementTypes::ACCESSORIES->value,
            default                                      => ElementTypes::UNDEFINED->value,
        };
    }

    /**
     * ___ Получение модели по коду 1С или имени или самой модели
     * @param string|Model $data
     * @return Model|null
     */
    public static function getElementByNameOrCode1C(string|Model $data): Model|null
    {
        if ($data instanceof Model) {
            return $data;
        }

        $model = self::getModelByCode1C($data);
        if (!$model) {
            $model = self::getModelByName(mb_strtolower($data));
        }

        return $model;
    }

    /**
     * ___ Проверка на принадлежность элемента к матрасной группе
     * @param string $data
     * @return bool
     * @noinspection PhpUnnecessaryLocalVariableInspection
     */
    public static function isElementMattressTypeGroup(string $data): bool
    {
        $model = self::getElementByNameOrCode1C($data);

        if (!$model) {
            return false;
        }

        return
            self::isElementMattress($model)         // Матрас
            || self::isElementUpMattress($model)    // Наматрасник
            || self::isElementCover($model);        // Чехол
    }

    /**
     * ___ Проверка на принадлежность элемента к аксессуарной группе
     * @param string $data
     * @return bool
     * @noinspection PhpUnnecessaryLocalVariableInspection
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function isElementAccessoriesTypeGroup(string $data): bool
    {
        $model = self::getElementByNameOrCode1C($data);
        if (!$model) {
            return false;
        }

        if (is_null($model->modelType)) {
            return false;
        };

        $isAccessoriesGroup
            = $model->modelType->code_1c === '000000003'          // Наматрасник защитный
            || $model->modelType->code_1c === '000000004'       // Подушка
            || $model->modelType->code_1c === '000000009'       // Одеяло
            || $model->modelType->code_1c === '000000041'       // Подматрасник
            || $model->modelType->code_1c === '000000006'       // Постельное белье
            || $model->modelType->code_1c === '000000024'       // Чехол для подушки
        ;

        return $isAccessoriesGroup;
    }

    /**
     * ___ Проверка на принадлежность элемента к группе производства чехлов
     * @param string|Model $data
     * @param string $name
     * @return bool
     */
    public static function isElementCoversTypeGroup(string|Model $data, string $name): bool
    {
        return self::isElementCover($data, $name);
    }


    // line ---------------------------------------------------------------------------
    // line ----------- Проверка на принадлежность изделия к типу изделия -------------
    // line ---------------------------------------------------------------------------

    /**
     * ___ Проверка, является ли элемент матрасом
     * @param string|Model $data
     * @return bool
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function isElementMattress(string|Model $data): bool
    {
        $model = self::getElementByNameOrCode1C($data);

        if (!$model) {
            return false;
        }

        if (is_null($model->modelType)) {
            return false;
        };

        return $model->modelType->code_1c === '000000001';   // Матрас
    }

    /**
     * ___ Проверка, является ли элемент наматрасником
     * @param string|Model $data
     * @return bool
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function isElementUpMattress(string|Model $data): bool
    {
        $model = self::getElementByNameOrCode1C($data);

        if (!$model) {
            return false;
        }

        if (is_null($model->modelType)) {
            return false;
        };

        return $model->modelType->code_1c === '000000002';  // Наматрасник модифицирующий
    }


    /**
     * ___ Проверка, является ли элемент чехлом ($name - возможность определить чехол по имени)
     * @param string|Model $data
     * @param string $name
     * @return bool
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function isElementCover(string|Model $data, string $name = ''): bool
    {
        $model = self::getElementByNameOrCode1C($data);

        // __ Если находим модель, пробуем определить его тип
        if ($model) {
            if (!is_null($model->modelType)) {
                return $model->modelType->code_1c === '000000012';
            };
        }

        // __ Если не находим модель, пробуем определить ее по имени
        if (str_contains(mb_strtolower($data), 'чехол')) {
            return true;
        };

        return str_contains(mb_strtolower($name), 'чехол');
    }


    /**
     * ___ Проверка, является ли элемент МЭ (матрас или наматрасник, но не чехол)
     * @param string|Model $data
     * @return bool
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function isElementBase(string|Model $data): bool
    {
        $model = self::getElementByNameOrCode1C($data);
        return self::isElementMattress($model) || self::isElementUpMattress($model);
    }


    /**
     * ___ Проверка, является ли элемент Прогнозной моделью ($name - возможность определить чехол по имени)
     * @param string|Model $data
     * @param string $name
     * @return bool
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function isElementAverage(string|Model $data, string $name = ''): bool
    {
        $model = self::getElementByNameOrCode1C($data);

        // __ Если находим модель, пробуем определить его тип
        if ($model) {
            // Если модель прогнозная, то проверяем по префиксу в коде 1С, который создаем сами
            return mb_stripos($model->code_1c, CLIENT_AVERAGE_MATTRESS_PREFIX) !== false
                || mb_stripos($model->name_1c, CLIENT_AVERAGE_ACCESSORY_PREFIX) !== false;

        }

        // __ Если не находим модель, пробуем определить ее по имени
        if (str_contains(mb_strtolower($data), 'average')) {
            return true;
        };

        return str_contains(mb_strtolower($name), 'average');
    }

    // line ---------------------------------------------------------------------------
    // line ---------------------------------------------------------------------------
    // line ---------------------------------------------------------------------------

    /**
     * ___ Создание средней модели
     * @param string $clientId id клиента
     * @param string $elementType тип элемента
     * @return Model|null
     */
    public static function createAverageModel(string $clientId, string $elementType = ElementTypes::MATTRESSES->value): ?Model
    {
        // __ Пока или матрасы или аксессуары
        if ($elementType !== ElementTypes::MATTRESSES->value && $elementType !== ElementTypes::ACCESSORIES->value) {
            return null;
        }

        $client = ClientsService::getClientById($clientId);
        if (!$client) {
            return null;
        }

        $isMattressType = ($elementType === ElementTypes::MATTRESSES->value);

        // __ Проверка на присутствие средней модели в базе
        $PREFIX  = $isMattressType ? CLIENT_AVERAGE_MATTRESS_PREFIX : CLIENT_AVERAGE_ACCESSORY_PREFIX;
        $code_1c = $PREFIX . str_pad($client->id, CODE_1C_LENGTH - mb_strlen($PREFIX), '0', STR_PAD_LEFT);

        // __ Получаем среднюю модель напрямую, без кэша, т.к. она создается динамически
        $averageModel = Model::query()->find($code_1c);
        if ($averageModel) {
            return $averageModel;
        }

        $averageModel = Model::query()->create(
            [
                'code_1c'                        => $code_1c,
                'model_manufacture_status_id'    => null,
                'model_collection_code_1c'       => $isMattressType ? AVERAGE_M_PREFIX . '0000' : AVERAGE_A_PREFIX . '0000',
                'model_type_code_1c'             => $isMattressType ? AVERAGE_M_PREFIX . '0000' : AVERAGE_A_PREFIX . '0000',
                'serial'                         => null,
                'name'                           => $isMattressType ? AVERAGE_M_PREFIX . $client->short_name : AVERAGE_A_PREFIX . $client->short_name,
                'name_short'                     => null,
                'name_common'                    => null,
                'name_report'                    => $isMattressType ? 'Плановый Матрас' : 'Плановый Аксессуар',
                'cover_code_1c_copy'             => null,
                'cover_name_1c'                  => null,
                // 'base_height'                    => 0,
                // 'cover_height'                   => 0,
                'textile'                        => null,
                'textile_composition'            => null,
                'cover_type'                     => null,
                'zipper'                         => null,
                'spacer'                         => null,
                'stitch_pattern'                 => null,
                'pack_type'                      => null,
                'base_composition'               => null,
                'side_foam'                      => null,
                'base_block'                     => null,
                'load'                           => null,
                'guarantee'                      => null,
                'life'                           => null,
                'cover_mark'                     => null,
                'model_mark'                     => null,
                // 'model_manufacture_group_id'     => 0,
                'owner'                          => null,
                'lamit'                          => null,
                'sewing_machine'                 => null,
                'kant'                           => null,
                'tkch'                           => null,
                'pack_density'                   => null,
                'side_height'                    => null,
                'pack_weight_rb'                 => null,
                'pack_weight_ex'                 => null,
                'model_manufacture_type_code_1c' => $isMattressType ? AVERAGE_M_PREFIX . '0000' : AVERAGE_A_PREFIX . '0000',
                'weight'                         => 0,
                'barcode'                        => null,
                'base'                           => null,
                'cover'                          => null,
                'active'                         => true,
                'status'                         => null,
                'description'                    => null,
                'comment'                        => null,
                'note'                           => null,
                'meta'                           => null,
                // 'created_at'                     => $this->created_at,
                // 'updated_at'                     => $this->updated_at,
                'cover_code_1c'                  => null,
            ]
        );

        return $averageModel;
    }


    /**
     * ___ Получение кода средней модели по id клиента и типу элемента
     * @param Client|int|null $client
     * @param string $modelType
     * @return string|null
     */
    public static function getAverageModelCodeByClientAndElementsType(
        Client|int $client = null,
        string $modelType = ElementTypes::MATTRESSES->value): string|null
    {
        if (!$client) {
            return null;
        }

        if ($modelType === ElementTypes::MATTRESSES->value) {
            $PREFIX  =  CLIENT_AVERAGE_MATTRESS_PREFIX;
            // $PREFIX  =  ElementTypes::MATTRESSES->value;
        } elseif ($modelType === ElementTypes::ACCESSORIES->value) {
            $PREFIX  =  CLIENT_AVERAGE_ACCESSORY_PREFIX;
            // $PREFIX  =  ElementTypes::ACCESSORIES->value;
        } else {
            return null;
        }

        if ($client instanceof Client) {
            /** @noinspection PhpUndefinedFieldInspection */
            $clientId = $client->id;
        } else {
            $clientId = $client;
        }

        return $PREFIX . str_pad($clientId, CODE_1C_LENGTH - mb_strlen($PREFIX), '0', STR_PAD_LEFT);
    }

}
