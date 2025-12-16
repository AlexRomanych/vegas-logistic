<?php

use App\Http\Controllers\Api\V1\Cells\Logs\FabricTaskRollLogController;

// ___ Блок Логов

Route::prefix('/logs')
    ->middleware('jwt.auth')
    ->group(function () {

        Route::get('/fabrics/rolls/execute/period', [FabricTaskRollLogController::class, 'getLogsFabricsExecuteRollsByPeriod']);
        Route::get('/fabrics/rolls/execute/roll-number', [FabricTaskRollLogController::class, 'getLogsFabricsExecuteRollsByRollNumber']);

    });

