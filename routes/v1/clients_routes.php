<?php

use App\Http\Controllers\Api\V1\ClientController;

//___ Блок Клиентов
Route::middleware('jwt.auth')
    ->group(function () {
        Route::get('/clients/{status?}', [ClientController::class, 'getClients']);
        Route::get('/client/{id}', [ClientController::class, 'getClient']);
        Route::post('/client/create', [ClientController::class, 'createClient']);
        Route::put('/client/update', [ClientController::class, 'updateClient']);
        Route::delete('/client/delete', [ClientController::class, 'deleteClient']);

        Route::get('/clients/load', [ClientController::class, 'clientsLoad']);
    });


//загружаем из файла клиентов
// Route::get('/clients', [ClientController::class, 'getClients'])->middleware('jwt.auth');
// Route::get('/clients/load', [ClientController::class, 'clientsLoad'])->middleware('jwt.auth');
//Route::get('/clients/load', [UpdateData1CController::class, 'clientsLoad'])->middleware('jwt.auth');
//Route::get('/clients/load', [Update::class, 'updateManagers']);

