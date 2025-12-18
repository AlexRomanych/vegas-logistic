<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Models\ModelConstructProcedure;
use App\Models\Procedure;

final class ProceduresService implements VegasDataUpdateContract
{
    private static array $proceduresCacheByCode1C = [];
    // private static array $proceduresCacheByName = [];

    /**
     * ___ Проверка на наличие Процедур в Кэше
     * @return bool
     */
    private static function isProceduresCashed(): bool
    {
        return count(self::$proceduresCacheByCode1C) > 0;
        // return count(self::$proceduresCacheByCode1C) > 0 && count(self::$proceduresCacheByName) > 0;
    }

    /**
     * ___ Кэширование Процедур
     * @return void
     */
    private static function getProcedures(): void
    {
        if (!self::isProceduresCashed()) {
            $procedures = ModelConstructProcedure::query()->get();

            foreach ($procedures as $procedure) {
                self::$proceduresCacheByCode1C[$procedure->code_1c] = $procedure;
                // self::$proceduresCacheByName[$material->name] = $material;
            }
        }
    }

    /**
     * ___ Получение Процедуры по коду 1С
     * @param string $code1C
     * @return ModelConstructProcedure|null
     */
    public static function getProcedureByCode1C(string $code1C): ?ModelConstructProcedure
    {
        self::getProcedures();
        return self::$proceduresCacheByCode1C[$code1C] ?? null;
    }

    // /**
    //  * ___ Получение Процедуры по имени
    //  * @param string $name
    //  * @return Procedure|null
    //  */
    // public static function getProcedureByName(string $name): ?Procedure
    // {
    //     self::getProcedures();
    //     return null;
    //     // return self::$proceduresCacheByName[$name] ?? null;
    // }

    // _____ =================================================================================================================




    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
        $fileName = config('vegas.procedures_1C_json_name');
        $proceduresList = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);

        Procedure::query()->truncate();     // очищаем таблицу с процедурами

        // todo Доделать выборку только тех процедур, которые есть в спецификациях

        foreach ($proceduresList as $procedureItem) {
            Procedure::query()->create(
                [
                    'code1C' => $procedureItem['cd'],
                    'name' => $procedureItem['nm'],
                    'text' => $procedureItem['tx'],
                    'object_code1C' => $procedureItem['oc'],
                    'object_type' => $procedureItem['ot'],
                ]

            );
        }
    }
}
