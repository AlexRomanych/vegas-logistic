<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelCollection;
use App\Http\Resources\ModelResource;
use App\Models\Model;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    //

    public function show(string $id)
    {
        return new ModelResource(Model::query()->find($id));
    }

    public function all()
    {
        return new ModelCollection(Model::all());
    }
}
