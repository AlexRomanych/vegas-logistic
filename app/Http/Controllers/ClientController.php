<?php

namespace App\Http\Controllers;

use App\Contracts\VegasDataGetContract;
use App\Models\Client;
use App\Services\ClientsService;
use Illuminate\Support\Facades\App;

class ClientController extends Controller
{
    public function fillClientsFromEPSReport(VegasDataGetContract $getter)
    {
        App::make(ClientsService::class, [$getter])->updateData();
    }

    public function show($id)
    {
//        $clients = Client::query()->with('manager')->get();
        $clients = Client::query()->get();

        $manager = $clients[$id];
        $manager = $clients[$id]->manager;
//        $client = $clients[1];

//        dd($manager);
//        dd($clients);

        return view('welcome');

    }

}
