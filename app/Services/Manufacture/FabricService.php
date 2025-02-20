<?php

namespace App\Services\Manufacture;

// Сервис для Полотен стеганных
use App\Models\Manufacture\Cells\Fabric\Fabric;
//use Illuminate\Support\Collection;

final class FabricService
{
    private static array $fabricsNameCache = [];                       // Кэш ПС по имени


    /**
     * Получаем ПС по имени
     * @param string $name
     * @return Fabric|null
     */
    public static function getFabricByName(string $name): ?Fabric
    {

        if (count(self::$fabricsNameCache)  === 0) {
            self::cacheFabricsByName();
        }

        if (isset(self::$fabricsNameCache[$name])) {
            return self::$fabricsNameCache[$name];
        }

        return null;
    }

    // Кэшируем ПС
    private static function cacheFabricsByName(): void
    {
        $fabrics = Fabric::all();

        foreach ($fabrics as $fabric) {
            self::$fabricsNameCache[$fabric->name] = $fabric;
        }

    }
}
