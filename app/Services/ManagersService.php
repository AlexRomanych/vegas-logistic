<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Manager;

final class ManagersService implements VegasDataUpdateContract
{
    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
//        $fileName = config('vegas.sellers_1C_json_name');
//        $sellersList = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);


        Manager::query()->updateOrCreate(
            [
                'id' => 100
            ],
            [
                'surname' => Manager::DEFAULT_MANAGER_NAME,
                'first_name' => '',
                'description' => 'менеджер по умолчанию',
            ]
        );

        Manager::query()->updateOrCreate(
            [
                'id' => 101
            ],
            [
                'surname' => 'Псыщаница',
                'first_name' => 'Людмила',
                'second_name' => 'Леонидовна',
                'description' => 'менеджер по западному направлению',
            ]
        );

        Manager::query()->updateOrCreate(
             [
                 'id' => 102
             ],
             [
                 'surname' => 'Калиман',
                 'first_name' => 'Александр',
                 'second_name' => 'Анатольевич',
                 'description' => 'менеджер по восточному направлению',
             ]
        );

        Manager::query()->updateOrCreate(
            [
                'id' => 103
            ],
            [
                'surname' => 'Алексеева',
                'first_name' => 'Наталья',
                'second_name' => 'Александровна',
                'description' => 'менеджер по восточному направлению (РБ)',
            ]

        );

    }
}
