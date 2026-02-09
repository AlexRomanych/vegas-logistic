<?php

// ___ Блок Персонала
use App\Http\Controllers\Api\V1\WorkerController;

Route::prefix('workers')
    ->middleware('jwt.auth')
    ->group(function () {

        Route::get('/', [WorkerController::class, 'getWorkers']);
        Route::get('/{id}', [WorkerController::class, 'getWorkerById']);
        Route::patch('/', [WorkerController::class, 'updateWorker']);
        Route::post('/', [WorkerController::class, 'createWorker']);
        Route::delete('/', [WorkerController::class, 'deleteWorker']);

        // __ Заполняем таблицу тестовыми данные
        Route::get('/workers/test/fill', [WorkerController::class, 'testFill']);
    });
