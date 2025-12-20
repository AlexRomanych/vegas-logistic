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

    /**
     *  ___ Возвращает клиента по его имени
     * @param string $fullName Имя клиента
     * @param string|null $addName Дополнительное имя клиента
     * @return Client|null
     */
    public static function getClientByName(string $fullName, string | null $addName = null): ?Client
    {
        if (count(self::$clientsCache) === 0) {
            self::getClients();
        }

        foreach (self::$clientsCache as $client) {
            if (mb_strtolower($client->name) === mb_strtolower($fullName)) {

                if ($addName) {
                    if (mb_strtolower($client->add_name) === mb_strtolower($addName)) {
                        return $client;
                    }
                } else {
                    return $client;
                }

            }
        }

        return null;
    }

    /**
     *  ___ Возвращает клиента по его коду в 1С
     * @param string|null $code_1c Код клиента из 1С
     * @return Client|null
     */
    public static function getClientByCode_1c(string | null $code_1c = null): ?Client
    {
        if (!$code_1c) return null;

        if (count(self::$clientsCache) === 0) {
            self::getClients();
        }

        foreach (self::$clientsCache as $client) {
            if ($client->$code_1c === $code_1c) {
                return $client;
            }
        }

        return null;
    }


    /**
     * __ Проверяет является ли клиент ЛММ
     * @param Client|string|int $entity
     * @return bool
     */
    public static function isClient_LMM(Client|string|int $entity): bool
    {
        if (is_string($entity)) {
            return mb_stripos($entity, 'ЛММ') !== false;
        }

        if (is_int($entity)) {
            $client = Client::query()->find($entity);
            if (!$client) return false;
            return self::isClient_LMM($client->name);
        }

        if ($entity instanceof Client) {
            return self::isClient_LMM($entity->name);
        }

        return false;
    }


}

