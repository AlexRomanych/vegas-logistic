<?php

namespace App\Models\Shared\Facades;

use App\Models\Models\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ModelFacade
{
    private array $models = [];


    /**
     * Проверяет, является ли модель матрасом
     * @param string|null $modelName
     * @return bool
     */
    public function isMattress(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return trim(strtolower($model->type)) === strtolower('Матрас');
    }


    /**
     * Проверяет, является ли модель наматрасником
     * @param string|null $modelName
     * @return bool
     */
    public function isUpCover(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return trim(strtolower($model->type)) === strtolower('Наматрасник модифицирующий');
    }


    /**
     * Проверяет, является ли модель детским матрасом
     * @param string|null $modelName
     * @return bool
     */
    public function isChildren(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        $model->owner = $model->owner ?? '';
        return Str::contains(strtolower($model->owner), 'todo');
//        return Str::contains(strtolower($model->owner), COMPARE_STRING_CHILDREN);
    }


    /**
     * Проверяет, является ли модель подушкой
     * @param string|null $modelName
     * @return bool
     */
    public function isPillow(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return trim(strtolower($model->type)) === strtolower('Подушка');
    }


    /**
     * Проверяет, является ли модель одеялом
     * @param string|null $modelName
     * @return bool
     */
    public function isDuvet(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return trim(strtolower($model->type)) === strtolower('Одеяло');
    }


    /**
     * Проверяет, является ли модель наматрасником (MTicking)
     * @param string|null $modelName
     * @return bool
     */
    public function isAccessory(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return trim(strtolower($model->type)) === strtolower('Наматрасник защитный');
    }


    /**
     * Проверяет, является ли модель наматрасником (MTicking)
     * @param string|null $modelName
     * @return bool
     */
    public function isAverage(string|null $modelName = null): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }

        return Str::contains(strtolower($model->name), strtolower(Model::AVERAGE_MODEL_NAME));
//        return !!mb_strpos(strtolower($model->name), strtolower(Model::AVERAGE_MODEL_NAME));
//        return trim(strtolower($model->name)) === strtolower(Model::AVERAGE_MODEL_NAME);
    }


    /**
     * Возвращает модель по имени
     * @param string|null $name
     * @return Model|null
     */
    public function getModelByName(string|null $name = null): Model|null
    {
        $this->cacheModels();
        return $this->models[strtolower($name)] ?? null;
    }


    /**
     * Кэшируем модели
     * @return void
     */
    private function cacheModels(): void
    {
        if (count($this->models) === 0) {
            $models = Model::all();

            $modelsByNameIndex = [];

            foreach ($models as $model) {
                $modelsByNameIndex[strtolower($model->name)] = $model;
            }

            $this->models = $modelsByNameIndex;
        }
    }


    /**
     * Возвращает модель по имени
     * @param string|null $name
     * @return Model|null
     */
    public function getModelByName_(string|null $name = null): Model|null
    {
        $this->cacheModels();
        return Cache::get('models')[strtolower($name)];
    }


    /**
     * Кэшируем модели
     * @return void
     */
    private function cacheModels_(): void
    {
        if (is_null(Cache::get('models'))) {
            $models = Model::all();

            $modelsByNameIndex = [];

            foreach ($models as $model) {
                $modelsByNameIndex[strtolower($model->name)] = $model;
            }

            Cache::put('models', $modelsByNameIndex);
        }
    }


    //attract: Тут будем реализовывать функционал производства (ПЯ)

    /**
     * Проверка на AШМ
     * @param string $modelName
     * @return bool
     */
    public function isSewingAuto(string $modelName = ''): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return $model->is_auto;
    }

    /**
     * Проверка на УШМ
     * @param string $modelName
     * @return bool
     */
    public function isSewingUniversal(string $modelName = ''): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return $model->is_universal;
    }

    /**
     * Проверка на Глухие
     * @param string $modelName
     * @return bool
     */
    public function isSewingSolid(string $modelName = ''): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return $model->is_solid;
    }

    /**
     * Проверка на Глухие простые
     * @param string $modelName
     * @return bool
     */
    public function isSewingSolidLight(string $modelName = ''): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return $model->is_solid_light;
    }

    /**
     * Проверка на Глухие сложные
     * @param string $modelName
     * @return bool
     */
    public function isSewingSolidHard(string $modelName = ''): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return $model->is_solid_hard;
    }

    /**
     * Проверка на неопределенное свойство
     * @param string $modelName
     * @return bool
     */
    public function isSewingUndefined(string $modelName = ''): bool
    {
        $model = $this->getModelByName($modelName);
        if (is_null($model)) {
            return false;
        }
        return $model->is_undefined;
    }
}
