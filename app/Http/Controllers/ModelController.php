<?php

namespace App\Http\Controllers;

use App\Contracts\VegasDataGetContract;
use App\Models\Model;
use App\Services\ModelsService;
use Illuminate\Support\Facades\App;

class ModelController extends Controller
{

    public function show($id)
    {
        $model = Model::query()->with('collection')->find($id);

        if (is_null($model)) {
            abort(404);
        }

        dump($model);
    }

    public function fillModelsFrom1CReport(VegasDataGetContract $getter)
    {
        App::make(ModelsService::class, [$getter])->updateData();
    }
}
