<?php

// ___ Блок Персонала
use App\Http\Controllers\Api\V1\WorkerController;

Route::get('/workers', [WorkerController::class, 'workers'])->middleware('jwt.auth');
Route::get('/worker/{id}', [WorkerController::class, 'show'])->middleware('jwt.auth');
Route::get('/worker/edit/{id}', [WorkerController::class, 'show'])->middleware('jwt.auth');
Route::put('/worker', [WorkerController::class, 'update'])->middleware('jwt.auth');
Route::post('/worker', [WorkerController::class, 'create'])->middleware('jwt.auth');
Route::delete('/worker', [WorkerController::class, 'destroy'])->middleware('jwt.auth');

// __ Заполняем таблицу тестовыми данные
Route::get('/workers/test/fill', [WorkerController::class, 'testFill']);
