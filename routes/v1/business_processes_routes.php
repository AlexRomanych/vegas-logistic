<?php

use App\Http\Controllers\Api\V1\Processes\BusinessProcessController;
use App\Http\Controllers\Api\V1\Processes\BusinessProcessNodeController;

// ___ Блок Бизнес процессов

Route::prefix('/business-processes')
    ->middleware('jwt.auth')
    ->group(function () {

        Route::get('/node/{id}', [BusinessProcessNodeController::class, 'getBusinessProcessNodeById']);

        Route::get('/', [BusinessProcessController::class, 'getBusinessProcesses']);
        Route::get('/{id}', [BusinessProcessController::class, 'getBusinessProcessById']);
        Route::get('/adjacency-list/{id}', [BusinessProcessController::class, 'getBusinessProcessAdjacencyList']);

    });

