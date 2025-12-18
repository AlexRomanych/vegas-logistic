<?php

namespace App\Services;


use App\Enums\MaterialUnits;
use App\Models\Materials\Material;

final class MaterialsService
{
    private static array $materialsCacheByCode1C = [];
    // private static array $materialsCacheByName = [];

    /**
     * ___ Проверка на наличие Материалов в Кэше
     * @return bool
     */
    private static function isMaterialsCashed(): bool
    {
        return count(self::$materialsCacheByCode1C) > 0;
        // return count(self::$materialsCacheByCode1C) > 0 && count(self::$materialsCacheByName) > 0;
    }

    /**
     * ___ Кэширование Материалов
     * @return void
     */
    private static function getMaterials(): void
    {
        if (!self::isMaterialsCashed()) {
            $materials = Material::all();

            foreach ($materials as $material) {
                self::$materialsCacheByCode1C[$material->code_1c] = $material;
                // self::$materialsCacheByName[$material->name] = $material;
            }
        }
    }

    /**
     * ___ Получение Материала по коду 1С
     * @param string $code1C
     * @return Material|null
     */
    public static function getMaterialByCode1C(string $code1C): ?Material
    {
        self::getMaterials();
        return self::$materialsCacheByCode1C[$code1C] ?? null;
    }

    // /**
    //  * ___ Получение Материала по имени
    //  * @param string $name
    //  * @return Material|null
    //  */
    // public static function getMaterialByName(string $name): ?Material
    // {
    //     self::getMaterials();
    //     return null;
    //     // return self::$materialsCacheByName[$name] ?? null;
    // }

    // _____ =================================================================================================================

    /**
     * ___ Получение корректного имени единицы измерения
     * @param string $unitName
     * @return string
     */
    public static function getCorrectUnitName(string $unitName): string
    {
        return match ($unitName) {
            'шт' => MaterialUnits::PIC->value,
            'м' => MaterialUnits::METERS->value,
            'м2' => MaterialUnits::SQUARE_METERS->value,
            'м3' => MaterialUnits::CUBIC_METERS->value,
            'пог. м', 'м п' => MaterialUnits::RUNNING_METERS->value,
            'кг' => MaterialUnits::KG->value,
            'рул' => MaterialUnits::ROLL->value,
            // 'упак' => '',
            // 'боб' => '',
            // 'компл' => '',
            // 'л' => '',
            // 'тыс. шт' => '',
            // 'мл' => '',
            default => $unitName,
        };
    }

}
