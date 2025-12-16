<?php

use App\Http\Controllers\Api\V1\ReasonController;

// ___ Блок Причин

Route::prefix('/reasons')
    ->middleware('jwt.auth')
    ->group(function () {

        // ___ Получаем список причин
        Route::get('/', [ReasonController::class, 'reasons']);
        Route::get('/{reason}', [ReasonController::class, 'reason']);
        Route::post('/', [ReasonController::class, 'store']);
        Route::put('/{reason}', [ReasonController::class, 'update']);
        Route::delete('/{reason}', [ReasonController::class, 'delete']);
        Route::get('/{cellsGroupId}/{reasonsCategoryId}', [ReasonController::class, 'getReasonsByCellsGroupAndReasonCategory']);

    });
