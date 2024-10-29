<?php

use App\Http\Controllers\Api\V1\ModelController;
use App\Http\Controllers\UpdateData1CController as Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Model;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

//Route::get('/models', function (Request $request) {
//
//    return Model::all();
//});
//
//
//route::get('/models/update', [Update::class, 'updateModelsAndCollections'])->name('models.update');
//route::get('/models/update', [Update::class, 'updateModelsAndCollections'])->name('models.update');

Route::get('models', [ModelController::class, 'all'])->name('models.all');
Route::get('model/{id}', [ModelController::class, 'show'])->name('models.show');
