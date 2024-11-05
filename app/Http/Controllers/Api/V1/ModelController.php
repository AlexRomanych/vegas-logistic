<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\ModelCollection;
use App\Http\Resources\Model\ModelResource;
use App\Models\Model;
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
//        return new ModelCollection(Model::query()->get(10));
        return new ModelCollection(DB::table('models')->get());
    }

//    public function show(string $id)
//    {
//        return new ModelResource(Model::query()->find($id));
//    }
//
//    public function all()
//    {
//        return new ModelCollection(Model::all());
//    }
}
