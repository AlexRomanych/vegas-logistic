<?php

use App\Http\Controllers\Api\V1\CellItemController;
use App\Http\Controllers\Api\V1\Cells\sewing\CellSewingController;
use App\Http\Controllers\Api\V1\CellsGroupController;
use App\Http\Controllers\Api\V1\Materials\MaterialController;
use App\Http\Controllers\Api\V1\Models\ModelConstructProcedureController;
use App\Http\Controllers\Api\V1\Models\ModelController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\Plans\PlanController;
use App\Http\Controllers\Api\V1\Plans\PlanLoadsController;
use App\Http\Controllers\UpdateData1CController as Update;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


require_once 'v1/auth_routes.php';
require_once 'v1/workers_routes.php';
require_once 'v1/clients_routes.php';
require_once 'v1/reasons_routes.php';
require_once 'v1/logs_routes.php';
require_once 'v1/fabrics_routes.php';
require_once 'v1/business_processes_routes.php';



//hr--------------------------------------------------------------------------------------------------------------------
//attract: Блок Пользователей
Route::get('/user', [UserController::class, 'index']);
Route::get('/users', [UserController::class, 'all']);
//hr--------------------------------------------------------------------------------------------------------------------

//hr--------------------------------------------------------------------------------------------------------------------
// __ Блок Моделей

Route::prefix('/models')
    ->middleware('jwt.auth')
    ->group(function () {

        Route::get('/', [ModelController::class, 'getModels']);
        Route::post('/upload', [ModelController::class, 'modelsUpload']);

        Route::get('/procedures', [ModelConstructProcedureController::class, 'getProcedures']);
        Route::post('/procedures/upload', [ModelConstructProcedureController::class, 'modelConstructProceduresUpload']);
    });




Route::get('/model/{code1C}', [ModelController::class, 'model'])->middleware('jwt.auth');
Route::get('/models/load', [ModelController::class, 'modelsLoad'])->middleware('jwt.auth');

Route::get('/models/update', [Update::class, 'updateModelsAndCollections'])->name('models.update');
//hr--------------------------------------------------------------------------------------------------------------------

//hr--------------------------------------------------------------------------------------------------------------------
// __ Блок Maтериалов

Route::prefix('/materials')
    ->middleware('jwt.auth')
    ->group(function () {

        Route::get('/', [MaterialController::class, 'getMaterials']);
        Route::post('/upload', [MaterialController::class, 'materialsUpload']);

    });

//hr--------------------------------------------------------------------------------------------------------------------




//hr--------------------------------------------------------------------------------------------------------------------
//attract: Блок Заказов
//Route::get('/orders', [OrderController::class, 'getOrders'])->middleware('jwt.auth');
Route::get('/orders', [OrderController::class, 'getOrders'])->middleware('jwt.auth');;
Route::post('/orders/upload', [OrderController::class, 'uploadOrders'])->middleware('jwt.auth');
Route::delete('/orders/delete', [OrderController::class, 'deleteOrders'])->middleware('jwt.auth');
//Route::delete('/orders/delete', function (Request $request) {
//    return $request->all();
//});
//hr--------------------------------------------------------------------------------------------------------------------

//hr--------------------------------------------------------------------------------------------------------------------
// attract: Блок производства
Route::get('/manufacture/cells/groups', [CellsGroupController::class, 'getCellsGroups'])->middleware('jwt.auth');
Route::get('/manufacture/cells', [CellItemController::class, 'getCells'])->middleware('jwt.auth');

// attract: Sewing
Route::get('/manufacture/cells/sewing/{type}', [CellSewingController::class, 'getSewingCellData'])->middleware('jwt.auth');
Route::get('/manufacture/cells/sewing/solid/{type}', [CellSewingController::class, 'getSewingCellData'])->middleware('jwt.auth');


//Route::get('/manufacture/cells/groups', function(Request $request) {
//    return json_encode(['cells_groups' => '11111111111111']);
//})->middleware('jwt.auth');;
//hr--------------------------------------------------------------------------------------------------------------------



// __ Блок Планов
Route::prefix('/plan')
    ->middleware('jwt.auth')
    ->group(function () {

        Route::post('/loads/upload', [PlanLoadsController::class, 'uploadLoads']);  // ___ Загрузка плана
        Route::get('/loads', [PlanLoadsController::class, 'getPlanLoads']);
        Route::get('/loads/default/period', [PlanLoadsController::class, 'getPlanLoadsDefaultPeriod']);


        Route::get('/business-process-node', [PlanController::class, 'getPlanBusinessProcessNode']);


    });





//// descr: Тут просто точки доступа для разных действий
//Route::get('fabrics/tasks/team/number/', [CellFabricServiceController::class, 'getFabricTeamNumberByDate'])->middleware('jwt.auth');
//


//hr--------------------------------------------------------------------------------------------------------------------

