<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Collection;

final class CollectionsService implements VegasDataUpdateContract
{
    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
        $fileName = config('vegas.collections_1C_json_name');
        $collectionsList = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);

        Collection::query()->update(['active' => 0]);

        foreach ($collectionsList as $collectionItem) {
            Collection::query()->updateOrCreate(
                [
                    'code1C' => $collectionItem['cd']
                ],
                [
                    'name' => $collectionItem['nm'],
                    'active' => 1,
                ]

            );
        }

        // Создаем пустую модель
        Collection::query()->updateOrCreate(
            [
                'code1C' => '000000000',
            ],
            [
                'name'   => '000 Без коллекции',
                'active' => 0,
            ]
        );



    }
}
