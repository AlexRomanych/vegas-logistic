<?php
/** @noinspection DuplicatedCode */

namespace App\Http\Controllers\Api\V1;

use App\Classes\EndPointStaticRequestAnswer;
use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\Controller;


use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Resources\Client\ClientResource;
use App\Models\Client;
use App\Services\ClientsService;
use App\Services\ManagersService;
use App\Services\SharedService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;

// use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    //__ Загружаем клиентов из файла
    public function clientsLoad(VegasDataGetContract $getter)
    {
        try {
            App::make(ManagersService::class, [$getter])->updateData();
            App::make(ClientsService::class, [$getter])->updateData();
            return '';

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }


    /**
     * ___ Отдаем список клиентов
     * @param $status  : нет (null) - все, 0 (false) - неактивные, 1 (true) - активные
     * @return AnonymousResourceCollection|string
     */
    public function getClients($status = null)
    {
        try {
            $validator = validator([
                'status' => (int)$status,
            ], [
                'status' => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $status = is_null($status) ? -1 : (int)$status;

            match ($status) {
                0       => $result = Client::query()->where('active', false)->get(),
                1       => $result = Client::query()->where('active', true)->get(),
                default => $result = Client::all(),
            };

            return ClientResource::collection($result);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * ___ Отдаем клиента по id
     * @param  int  $id
     * @return ClientResource|string
     */
    public function getClient(int $id)
    {
        try {
            $validator = validator([
                'id' => $id,
            ], [
                'id' => 'required|exists:clients,id',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            return new ClientResource(Client::query()->find($id));
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * ___ Сохраняем клиента
     * @param  StoreClientRequest  $request
     * @return string
     * @noinspection PhpUndefinedFieldInspection
     */
    public function createClient(StoreClientRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data['id'] == 0 || ($data['id'] < 0 && $data['id'] != -1)) {
                throw new Exception('Неверный id = '.$data['id']);
            }

            $client = Client::query()->find($data['id']);
            if ($client) {
                throw new Exception('Клиент c id = '.$data['id'].' уже существует');
            }

            $client = Client::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($data['name'])])
                ->whereRaw('LOWER(add_name) = ?', [strtolower($data['add_name'])])
                ->first();

            if ($client) {
                throw new Exception('Дубликат имен: '.$data['name'].' / '.$data['add_name']);
            }

            $client = Client::query()
                ->whereRaw('LOWER(short_name) = ?', [strtolower($data['short_name'])])
                ->first();

            if ($client) {
                throw new Exception('Дубликат отображаемых имен: '.$data['short_name']);
            }

            // __ Сырой код по вставке клиента
            /*
            $binds = [
                // Если вы вставляете ID вручную, добавьте его первым:
                'id'          => $data['id'],
                'name'        => $data['name'],
                'add_name'    => $data['add_name'] ?? '', // Обработка null (как в вашем примере)
                'short_name'  => $data['short_name'],
                // Логические значения (active) должны быть правильно приведены для SQL (0 или 1)
                'active'      => (int)$data['active'],
                'region'      => $data['region'],
                'description' => $data['description'],
                'comment'     => $data['comment'],
                // Добавление временных меток вручную
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if ($data['id'] === -1) {
                unset($binds['id']);          // Задаем id вручную нового клиента
            }

            // Формируем список столбцов и плейсхолдеров (?) для привязки
            $columns = implode(', ', array_keys($binds));
            $placeholders = implode(', ', array_fill(0, count($binds), '?'));

            $sql = "INSERT INTO clients ({$columns}) VALUES ({$placeholders})";

            DB::statement($sql, array_values($binds));
            */

            $client = new Client();
            // $client->fill($data);

            if ($data['id'] != -1) {
                $client->id = $data['id'];          // Задаем id вручную нового клиента
            }

            $client->name        = $data['name'];
            $client->add_name    = $data['add_name'] ?? '';
            $client->short_name  = $data['short_name'];
            $client->active      = $data['active'];
            $client->region      = $data['region'];
            $client->description = $data['description'];
            $client->comment     = $data['comment'];

            $client->save();

            if ($data['id'] != -1) {
                SharedService::setSequence('clients');  // Если установили id вручную, то корректируем sequence
            }

            return EndPointStaticRequestAnswer::ok('Сохранено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
            // return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Обновляем клиента
     * @param  StoreClientRequest  $request
     * @return string
     */
    public function updateClient(StoreClientRequest $request)
    {
        try {
            // $all = $request->all();
            $data   = $request->validated();
            $client = Client::query()->find($data['id']);
            if (!$client) {
                throw new Exception('Клиент c id = '.$data['id'].' не найден');
            }

            $client->name        = $data['name'];
            $client->add_name    = $data['add_name'] ?? '';
            $client->short_name  = $data['short_name'];
            $client->active      = $data['active'];
            $client->region      = $data['region'];
            $client->description = $data['description'];
            $client->comment     = $data['comment'];

            $client->save();

            return EndPointStaticRequestAnswer::ok('Обновлено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Удаляем клиента
     * @param  Request  $request
     * @return string
     */
    public function deleteClient(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer|min:1|exists:clients,id',
            ]);

            $client = Client::query()->find($validatedData['id']);

            if (!$client) {
                throw new Exception('Клиент c id = '.$validatedData['id'].' не найден');
            }

            $client->delete();

            return EndPointStaticRequestAnswer::ok('Удалено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

}
