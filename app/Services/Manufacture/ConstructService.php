<?php

namespace App\Services\Manufacture;



use App\Models\Models\ModelConstruct;
use Exception;
use Illuminate\Database\Eloquent\Collection;

final class ConstructService {
    private static array $modelsConstructCacheByCode1C = [];


    /**
     * ___ Кэшируем Спецификации
     * @return void
     */
    private static function cacheModelConstructs(): void {
        $constructs = [];
        if (count(self::$modelsConstructCacheByCode1C) === 0) {
            $constructs = self::getConstructsFromBase();
        }

        foreach ($constructs as $construct) {
            self::$modelsConstructCacheByCode1C[$construct->code_1c] = $construct;
        }

    }


    /**
     * ___ Получаем из Базы Спецификации
     * @return Collection|void
     */
    private static function getConstructsFromBase()  {
        try {
            return ModelConstruct::all();
        } catch (Exception $e) {
            self::$modelsConstructCacheByCode1C = [];
        }
    }


    /**
     * ___ Получаем спецификацию по коду из 1С
     * @param string $code
     * @return ModelConstruct|null
     */
    public static function getModelsConstructsByCode(string $code): ?ModelConstruct {
        if (count(self::$modelsConstructCacheByCode1C) === 0) {
            self::cacheModelConstructs();
        }

        if (isset(self::$modelsConstructCacheByCode1C[$code])) {
            return self::$modelsConstructCacheByCode1C[$code];
        }
        return null;
    }

}

