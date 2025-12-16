<?php

//hr--------------------------------------------------------------------------------------------------------------------
// __ Блок Стежки (ПС)
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricMachineController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricOrderController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricPictureController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricPictureSchemaController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricServiceController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricTaskContextController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricTaskController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricTaskRollController;
use App\Http\Controllers\Api\V1\Cells\Fabric\CellFabricTasksDateController;

Route::post('/fabrics/upload', [CellFabricController::class, 'uploadFabrics'])->middleware('jwt.auth');
Route::get('/fabrics', [CellFabricController::class, 'fabrics'])->middleware('jwt.auth');
Route::get('/fabric/{id}', [CellFabricController::class, 'fabric'])->middleware('jwt.auth');;
Route::put('/fabric', [CellFabricController::class, 'update'])->middleware('jwt.auth');
Route::post('/fabric', [CellFabricController::class, 'create'])->middleware('jwt.auth');
Route::delete('/fabric', [CellFabricController::class, 'destroy'])->middleware('jwt.auth');
Route::get('/fabrics/buffer/update/', [CellFabricController::class, 'updateFabricsBuffer'])->middleware('jwt.auth');
Route::get('/fabric/average/length/', [CellFabricController::class, 'getFabricAverageLength'])->middleware('jwt.auth');
Route::get('/fabrics/tuning/time/{from}/{to}', [CellFabricController::class, 'getFabricsBetweenTuningTime'])->middleware('jwt.auth');


// __ Рисунки стежки ПС
Route::get('/fabrics/pictures', [CellFabricPictureController::class, 'getFabricPictures'])->middleware('jwt.auth');
Route::put('/fabrics/pictures/update', [CellFabricPictureController::class, 'updateFabricPictures'])->middleware('jwt.auth');
Route::post('/fabrics/pictures/create', [CellFabricPictureController::class, 'createFabricPictures'])->middleware('jwt.auth');
Route::get('/fabrics/picture/{id}', [CellFabricPictureController::class, 'getFabricPicture'])->middleware('jwt.auth');
Route::get('/fabrics/picture-name/{id}', [CellFabricPictureController::class, 'getFabricPictureByName'])->middleware('jwt.auth');
Route::post('/fabrics/pictures/upload', [CellFabricPictureController::class, 'uploadFabricPictures'])->middleware('jwt.auth');


// __ Время переналадки стежки ПС
Route::get('fabrics/pictures/tuning/time/', [CellFabricPictureController::class, 'getFabricsPicturesTuningTime'])->middleware('jwt.auth');
Route::post('fabrics/pictures/tuning/time/', [CellFabricPictureController::class, 'setFabricsPicturesTuningTime'])->middleware('jwt.auth');
Route::delete('fabrics/pictures/tuning/time/', [CellFabricPictureController::class, 'deleteFabricsPicturesTuningTime'])->middleware('jwt.auth');
Route::get('fabrics/pic/tuning/time/{from}/{to}', [CellFabricPictureController::class, 'getFabricsPicturesBetweenTuningTime'])->middleware('jwt.auth');
//Route::post('/fabrics/pictures/upload', function(Request $request){
//    return json_encode($request->all());
//})->middleware('jwt.auth');

// __ Схемы рисунков стежки ПС
Route::get('/fabrics/pictures/schemas', [CellFabricPictureSchemaController::class, 'getFabricPictureSchemas'])->middleware('jwt.auth');


// __ Стегальные машины
Route::get('/fabrics/machines', [CellFabricMachineController::class, 'machines'])->middleware('jwt.auth');
Route::get('/fabrics/machine/{id}', [CellFabricMachineController::class, 'machine'])->middleware('jwt.auth');
Route::patch('/fabrics/machine/set/active', [CellFabricMachineController::class, 'setMachineActive'])->middleware('jwt.auth');


// __ Загрузка расхода ПС
Route::post('/fabrics/orders/upload', [CellFabricOrderController::class, 'uploadFabricOrders'])->middleware('jwt.auth');
Route::get('/fabrics/orders', [CellFabricOrderController::class, 'getFabricOrders'])->middleware('jwt.auth');
Route::patch('/fabrics/orders/close', [CellFabricOrderController::class, 'closeFabricOrder'])->middleware('jwt.auth');
Route::patch('/fabrics/orders/set/active/', [CellFabricOrderController::class, 'setFabricOrderActive'])->middleware('jwt.auth');
Route::post('/fabrics/orders/order/save', [CellFabricOrderController::class, 'saveFabricsOrdersOrder'])->middleware('jwt.auth');


// __ Блок Заказов Стежки на производство
Route::prefix('/fabrics/tasks')
    ->middleware('jwt.auth')
    ->group(function () {

        // descr: Получаем список СЗ, период в query
        Route::get('/', [CellFabricTasksDateController::class, 'tasks']);

        Route::get('/last/done/', [CellFabricTasksDateController::class, 'getLastDoneTask']);
        Route::patch('/status/change/', [CellFabricTasksDateController::class, 'statusChange']);
        Route::put('/create/', [CellFabricTasksDateController::class, 'create']);
        Route::put('/workers/update/', [CellFabricTasksDateController::class, 'workersUpdate']);
        Route::get('/executing/', [CellFabricTasksDateController::class, 'getFabricExecutingTasks']);
        Route::get('/not-done/', [CellFabricTasksDateController::class, 'getFabricNotDoneTasks']);
        Route::get('/close/', [CellFabricTasksDateController::class, 'closeFabricTasks']);


        Route::delete('/context/delete/', [CellFabricTaskContextController::class, 'deleteContext']);
        Route::get('/context/not-done/', [CellFabricTaskContextController::class, 'getContextNotDone']);
        Route::put('/context/expense/create/', [CellFabricTaskContextController::class, 'createContextExpense']);
        Route::put('/context/change-order/', [CellFabricTaskContextController::class, 'changeContextOrder']);
        Route::get('/context/', [CellFabricTaskContextController::class, 'getOrderContext']);
        Route::post('/context/add/roll', [CellFabricTaskContextController::class, 'addOrderContextRoll']);
        Route::get('/context/optimize/{task}/{machine}/{statistic?}', [CellFabricTaskContextController::class, 'optimizeOrderContext']);


        Route::put('/execute/roll/update/', [CellFabricTaskRollController::class, 'update']);
        Route::post('/execute/roll/add/', [CellFabricTaskRollController::class, 'addExecuteRoll']);
        Route::get('/rolls/done/', [CellFabricTaskRollController::class, 'getNotAcceptedToCutRolls']);
        Route::patch('/execute/roll/registered/', [CellFabricTaskRollController::class, 'setRollRegisteredStatus']);
        Route::patch('/execute/roll/moved/', [CellFabricTaskRollController::class, 'setRollMovedStatus']);
        Route::post('/execute/save/rolls/order/', [CellFabricTaskRollController::class, 'saveExecuteRollsOrder']);
        Route::patch('/roll/update/comment/', [CellFabricTaskRollController::class, 'updateRollComment']);
        Route::get('/execute/roll/last/{date}/{machine}', [CellFabricTaskRollController::class, 'getLastRoll']);

        // __ Обновляем комментарий к СЗ на данной СМ
        Route::patch('/comment/update', [CellFabricTaskController::class, 'updateTaskComment']);

        // __ Тут просто точки доступа для разных действий
        Route::get('/team/number/', [CellFabricServiceController::class, 'getFabricTeamNumberByDate']);

        // __ Тесты: Очистка таблицы Заданий Стежки
        //Route::get('/fabrics/tasks/clear/', [CellFabricServiceController::class, 'clearFabricTasksDateTable']);
    });





