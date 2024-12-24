<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\Controller;


use App\Http\Resources\Client\ClientCollection;
use App\Models\Client;
use App\Services\ClientsService;
use App\Services\ManagersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class ClientController extends Controller
{
    //attract: Загружаем клиентов из файла
    public function clientsLoad(VegasDataGetContract $getter)
    {
        try {
            App::make(ManagersService::class, [$getter])->updateData();
            App::make(ClientsService::class, [$getter])->updateData();
            return '';

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }


    //attract: Отдаем список клиентов
    public function getClients()
    {
        return new ClientCollection(
            Client::all()

        );
    }


}
