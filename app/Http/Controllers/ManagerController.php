<?php

namespace App\Http\Controllers;


use App\Contracts\VegasDataGetContract;
use App\Models\Manager;
use App\Services\ManagersService;
use Illuminate\Support\Facades\App;

class ManagerController extends Controller
{
    public function fillManagersFromEPSReport(VegasDataGetContract $getter)
    {
        App::make(ManagersService::class, [$getter])->updateData();
    }

    public function show($id)
    {
//        $manager = Manager::query()->where('id', $id)->with('clients')->get();
        $manager = Manager::query()->where('id', $id)->get();
        $managers = Manager::query()->get();
//        $clients = $manager->clients;
        $clients = $manager->load('clients');
        dump($manager);
        dump($clients);

        return view('welcome');
    }

    public function index()
    {
//        DB::statement("ALTER TABLE `managers` AUTO_INCREMENT = 0;");
        return view('home');
    }
}
