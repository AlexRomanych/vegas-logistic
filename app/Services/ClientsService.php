<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Client;


final class ClientsService implements VegasDataUpdateContract
{

    private static array $clientsCache = [];

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
                    'name'       => $client['nm'],
                    'add_name'   => $client['an'],
                    'short_name' => $client['sn'],
                    'region'     => $client['rg'],
                    'manager_id' => $client['mi'],

                    'active' => $client['ia'],
                ]
            );
        }
    }

    /**
     * ___ Возвращает массив всех клиентов
     * @return array
     */
    public static function getClients(): array
    {
        try {
            $clients = Client::query()->get();
            foreach ($clients as $client) {
                self::$clientsCache[$client->id] = $client;
            }
            return self::$clientsCache;
        } catch (\Exception $e) {
            self::$clientsCache = [];
            return [];
        }
    }

    /**
     * ___ Возвращает клиента по его id
     * @param int $id
     * @return Client|null
     */
    public static function getClientById(int $id): ?Client
    {
        if (count(self::$clientsCache) === 0) {
            self::getClients();
        }

        if (isset(self::$clientsCache[$id])) return self::$clientsCache[$id];
        return null;
    }


}

