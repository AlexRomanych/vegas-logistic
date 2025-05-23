<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\Controller;
use App\Http\Resources\Model\ModelCollection;
use App\Http\Resources\Model\ModelResource;
use App\Models\Model;
use App\Services\CollectionsService;
use App\Services\ModelsService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ModelController extends Controller
{
    //
    public function model($code1C)
    {
        return new ModelResource(Model::query()->find($code1C));
    }

    public function models()
    {
//        return 11111;
//        return new ModelCollection(Model::all());
        return new ModelCollection(
            Model::query()
                ->orderBy('type')
                ->orderBy('name')
                ->with('collection')
                ->get()
        );

//        $models = $users = DB::table('models')
//            ->select(DB::raw('*'))
//            ->where('type', '<>', '')
//            ->groupBy('serial')
//            ->get();

//        $models = $users = DB::table('models')
//            ->select('*', 'name', 'type')
////            ->where('type', '<>', '')
//            ->orderBy('type')
//            ->get();
//
//        $models = $models->orderBy('type');

//        return $models;
//        return new ModelCollection(DB::table('models')->get());
//        return new ModelCollection($models);
    }

    public function show(string $id)
    {
        return new ModelResource(Model::query()->find($id));
    }

    public function all()
    {
        return new ModelCollection(Model::all());
    }


    public function modelsLoad(VegasDataGetContract $getter)
    {
        try {
            App::make(CollectionsService::class, [$getter])->updateData();
            App::make(ModelsService::class, [$getter])->updateData();
            return '';

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

}
