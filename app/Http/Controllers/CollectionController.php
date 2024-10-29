<?php

namespace App\Http\Controllers;

use App\Contracts\VegasDataGetContract;
use App\Models\Collection;
use App\Services\CollectionsService;
use Illuminate\Support\Facades\App;

class CollectionController extends Controller
{
    public function show($id)
    {
        $collection = Collection::query()->with('models')->find($id);

        if (is_null($collection)) {
            abort(404);
        }

        foreach ($collection->models as $key => $model) {
//            dump($model->getAttributes());
            dump($key.' '.  $model->active);
        }

//        dd($collection);
    }



    // Заполняем (обновляем) данные в таблице
    public function fillCollectionsFrom1CReport(VegasDataGetContract $getter)
    {
        App::make(CollectionsService::class, [$getter])->updateData();
    }

}
