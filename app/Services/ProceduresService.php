<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Procedure;

final class ProceduresService implements VegasDataUpdateContract
{
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
