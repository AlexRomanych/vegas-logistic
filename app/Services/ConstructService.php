<?php

namespace App\Services;


use App\Models\Models\ModelConstruct;

class ConstructService
{
    private static array $constructsCacheByCode1C = [];
    // private static array $constructsCacheByName = [];

    /**
     * ___ Проверка на наличие Материалов в Кэше
     * @return bool
     */
    private static function isConstructsCashed(): bool
    {
        return count(self::$constructsCacheByCode1C) > 0;
        // return count(self::$constructsCacheByCode1C) > 0 && count(self::$constructsCacheByName) > 0;
    }

    /**
     * ___ Кэширование Материалов
     * @return void
     */
    private static function getConstructs(): void
    {
        if (!self::isConstructsCashed()) {
            $constructs = ModelConstruct::query()
                ->with('')
                ->get();

            foreach ($constructs as $construct) {
                self::$constructsCacheByCode1C[$construct->code_1c] = $construct;
                // self::$constructsCacheByName[$material->name] = $material;
            }
        }
    }

    /**
     * ___ Получение Материала по коду 1С
     * @param string $code1C
     * @return ModelConstruct|null
     */
    public static function getModelConstructByCode1C(string $code1C): ?ModelConstruct
    {
        self::getConstructs();
        return self::$constructsCacheByCode1C[$code1C] ?? null;
    }

    // /**
    //  * ___ Получение Материала по имени
    //  * @param string $name
    //  * @return ModelConstruct|null
    //  */
    // public static function getModelConstructByName(string $name): ?ModelConstruct
    // {
    //     self::getConstructs();
    //     return null;
    //     // return self::$constructsCacheByName[$name] ?? null;
    // }

    // _____ =================================================================================================================

}
