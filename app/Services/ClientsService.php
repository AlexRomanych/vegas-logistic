<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Client;

final class ClientsService implements VegasDataUpdateContract
{
    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
        $fileName = config('vegas.clients_EPS_json_name');
        $clientList = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);

        Client::query()->update(['active' => 0]);

        foreach ($clientList as $client) {

            Client::query()->updateOrCreate(
                [
                    'id' => $client['id']
                ],
                [
                    'name' => $client['nm'],
                    'add_name' => $client['an'],
                    'short_name' => $client['sn'],
                    'region' => $client['rg'],
                    'manager_id' => $client['mi'],

                    'active' => 1,
                ]
            );
        }
    }
}
