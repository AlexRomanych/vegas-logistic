<?php

use App\Http\Controllers\Api\V1\CellItemController;
use App\Http\Controllers\Api\V1\Cells\Blocks\BlockCollectionController;
use App\Http\Controllers\Api\V1\Cells\Cutting\CellCuttingProcedureController;
use App\Http\Controllers\Api\V1\Cells\Sewing\CellSewingTaskController;
use App\Http\Controllers\Api\V1\Cells\Sewing\CellSewingDayController;
use App\Http\Controllers\Api\V1\Cells\Sewing\CellSewingOperationController;
use App\Http\Controllers\Api\V1\Cells\Sewing\CellSewingOperationSchemaController;
use App\Http\Controllers\Api\V1\Cells\Sewing\CellSewingStatusController;
use App\Http\Controllers\Api\V1\Cells\Cutting\CellCuttingTaskController;
use App\Http\Controllers\Api\V1\Cells\Cutting\CellCuttingDayController;
use App\Http\Controllers\Api\V1\Cells\Cutting\CellCuttingOperationController;
use App\Http\Controllers\Api\V1\Cells\Cutting\CellCuttingOperationSchemaController;
use App\Http\Controllers\Api\V1\Cells\Cutting\CellCuttingStatusController;
use App\Http\Controllers\Api\V1\CellsGroupController;
use App\Http\Controllers\Api\V1\Documents\TextileDesignDocumentController;
use App\Http\Controllers\Api\V1\Materials\MaterialController;
use App\Http\Controllers\Api\V1\Models\ModelConstructController;
use App\Http\Controllers\Api\V1\Models\ModelConstructProcedureController;
use App\Http\Controllers\Api\V1\Models\ModelController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
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

        Route::get('/procedures', [ModelConstructProcedureController::class, 'getModelProcedures']);
        Route::get('/procedure/{code1c}', [ModelConstructProcedureController::class, 'getModelProcedure']);
        Route::post('/procedures/upload', [ModelConstructProcedureController::class, 'modelConstructProceduresUpload']);

        Route::get('/constructs', [ModelConstructController::class, 'getModelConstructs']);
        Route::get('/construct/{code1c}', [ModelConstructController::class, 'getConstructByModelCode1c']);
        Route::post('/constructs/upload', [ModelConstructController::class, 'modelConstructsUpload']);

        Route::post('/update', [ModelConstructController::class, 'modelsUpdate']);
    });


Route::get('/model/{code1c}', [ModelController::class, 'model'])->middleware('jwt.auth');
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
// __ Блок Заказов

// Route::prefix('/order')
//     ->middleware('jwt.auth')
//     ->group(function () {
//
//         Route::get('/{id}', [OrderController::class, 'getOrderById']);
//     });

Route::get('/orders/types/fill', [OrderController::class, 'fillOrderTypes']);

Route::prefix('/orders')
    ->middleware('jwt.auth')
    ->group(function () {
        //Route::get('/types/fill', [OrderController::class, 'fillOrderTypes']);
        Route::get('/types', [OrderController::class, 'getOrderTypes']);    // __ Должен быть первым, чтобы не было конфликта с '/{id}'

        Route::get('/', [OrderController::class, 'getOrders']);
        Route::get('/{id}', [OrderController::class, 'getOrderById']);
        Route::post('/upload', [OrderController::class, 'uploadOrders']);
        Route::post('/validate', [OrderController::class, 'validateOrders']);
        Route::post('/add/average', [OrderController::class, 'addOrdersAverage']);

        Route::delete('/delete/{id}', [OrderController::class, 'deleteOrders']);
        Route::delete('/line/delete/{id}', [OrderController::class, 'deleteOrderLine']);

        Route::patch('/patch/load-at', [OrderController::class, 'patchLoadAtDate']);
        Route::patch('/patch/description', [OrderController::class, 'patchDescription']);


        Route::patch('/types/color/patch', [OrderController::class, 'patchOrderTypeColor']);
    });
//hr--------------------------------------------------------------------------------------------------------------------

//hr--------------------------------------------------------------------------------------------------------------------
// attract: Блок производства
Route::get('/manufacture/cells/groups', [CellsGroupController::class, 'getCellsGroups'])->middleware('jwt.auth');
Route::get('/manufacture/cells', [CellItemController::class, 'getCells'])->middleware('jwt.auth');

//hr--------------------------------------------------------------------------------------------------------------------
// __ Блок Пошива - Sewing
Route::prefix('sewing')
    ->middleware('jwt.auth')
    ->middleware('sewing_tasks_check')
    ->group(function () {
        // __ СЗ Пошива
        Route::get('tasks', [CellSewingTaskController::class, 'getSewingTasks']);
        Route::get('tasks/order/{id}', [CellSewingTaskController::class, 'getSewingTasksByOrderId']);
        Route::get('tasks/status', [CellSewingTaskController::class, 'getSewingTasksByStatus']);
        Route::get('tasks/status/period', [CellSewingTaskController::class, 'getSewingTasksByStatusAndPeriod']);
        Route::get('tasks/status/date/before', [CellSewingTaskController::class, 'getSewingTasksByStatusBeforeDate']);
        Route::get('tasks/status/date/on', [CellSewingTaskController::class, 'getSewingTasksByStatusOnDate']);
        Route::get('tasks/status/date/on/check', [CellSewingTaskController::class, 'checkSewingTasksByStatusOnDate']);
        Route::post('tasks/update', [CellSewingTaskController::class, 'updateSewingTasks']);
        Route::post('tasks/comment', [CellSewingTaskController::class, 'setSewingTaskComment']);
        Route::post('tasks/action/set', [CellSewingTaskController::class, 'setSewingTaskActionAt']);
        Route::post('tasks/line/done', [CellSewingTaskController::class, 'setSewingTaskLinesDone']);
        Route::post('tasks/line/false', [CellSewingTaskController::class, 'setSewingTaskLinesFalse']);
        Route::post('tasks/line/reset', [CellSewingTaskController::class, 'setSewingTaskLinesReset']);
        Route::post('tasks/add/order', [CellSewingTaskController::class, 'addSewingTasksByOrderId']);
        Route::delete('tasks/delete/order', [CellSewingTaskController::class, 'deleteSewingTasksByOrderId']);

        // __ Типовые операции
        Route::get('operations', [CellSewingOperationController::class, 'getSewingOperations']);
        Route::get('operations/{id}', [CellSewingOperationController::class, 'getSewingOperation']);
        Route::post('operations', [CellSewingOperationController::class, 'createSewingOperation']);
        Route::put('operations', [CellSewingOperationController::class, 'updateSewingOperation']);

        // __ Схемы Типовых операций
        Route::get('operation/schemas', [CellSewingOperationSchemaController::class, 'getSewingOperationSchemas']);
        Route::get('operation/schemas/{id}', [CellSewingOperationSchemaController::class, 'getSewingOperationSchema']);
        Route::get('operation/schemas/check/{id}', [CellSewingOperationSchemaController::class, 'checkSewingOperationSchemaForSummaryTime']);
        Route::post('operation/schemas/create', [CellSewingOperationSchemaController::class, 'createSewingOperationSchema']);
        Route::put('operation/schemas/update', [CellSewingOperationSchemaController::class, 'updateSewingOperationSchema']);
        Route::delete('operation/schemas/delete', [CellSewingOperationSchemaController::class, 'deleteSewingOperationFromSchema']);
        Route::post('operation/schemas/add', [CellSewingOperationSchemaController::class, 'addSewingOperationToSchema']);

        // __ Модели + Типовые операции пошива
        Route::get('operation/models', [ModelController::class, 'getModelsForSewingLabors']);
        Route::patch('operation/schemas/models', [ModelController::class, 'updateModelSewingOperationSchema']);
        Route::post('operation/models/delete', [ModelController::class, 'deleteSewingOperationFromModel']);
        Route::post('operation/models/add', [ModelController::class, 'addSewingOperationToModel']);


        // __ Статусы СЗ Пошива
        Route::get('/task/statuses', [CellSewingStatusController::class, 'getSewingTaskStatuses']);
        Route::patch('/task/statuses/color/patch', [CellSewingStatusController::class, 'patchSewingTaskStatusColor']);
        Route::post('/task/statuses/set', [CellSewingStatusController::class, 'setSewingTasksStatuses']);

        // __ Производственный день
        Route::get('/day/{date}/{change}', [CellSewingDayController::class, 'getSewingDayByDateAndChange']);
        Route::get('/day/dates', [CellSewingDayController::class, 'getSewingDaysByDates']);
        Route::post('/day/comment', [CellSewingDayController::class, 'setSewingDayComment']);
        Route::post('/day/worker/add', [CellSewingDayController::class, 'addWorkerToSewingDay']);
        Route::post('/day/workers/add', [CellSewingDayController::class, 'addWorkersToSewingDay']);
        Route::post('/day/worker/remove', [CellSewingDayController::class, 'removeWorkerFromSewingDay']);
        Route::patch('/day/responsible/add', [CellSewingDayController::class, 'addResponsibleToSewingDay']);
        Route::patch('/day/responsible/remove', [CellSewingDayController::class, 'removeResponsibleFromSewingDay']);
        Route::patch('/day/start', [CellSewingDayController::class, 'startSewingDay']);
        Route::patch('/day/finish', [CellSewingDayController::class, 'finishSewingDay']);
        Route::get('/day/ready/get/{date}/{change}', [CellSewingDayController::class, 'readyGetSewingDay']);
        Route::patch('/day/ready/set', [CellSewingDayController::class, 'readySetSewingDay']);
        Route::patch('/day/ready/unset', [CellSewingDayController::class, 'readyUnsetSewingDay']);
    });
//hr--------------------------------------------------------------------------------------------------------------------

//hr--------------------------------------------------------------------------------------------------------------------
// __ Блок Раскроя - Cutting
Route::prefix('cutting')
    ->middleware('jwt.auth')
    ->middleware('cutting_tasks_check')
    ->group(function () {
        Route::get('test', [CellCuttingTaskController::class, 'test']);

        // __ СЗ Раскроя
        Route::get('tasks', [CellCuttingTaskController::class, 'getCuttingTasks']);
        Route::get('tasks/order/{id}', [CellCuttingTaskController::class, 'getCuttingTasksByOrderId']);
        Route::get('tasks/status', [CellCuttingTaskController::class, 'getCuttingTasksByStatus']);
        Route::get('tasks/status/period', [CellCuttingTaskController::class, 'getCuttingTasksByStatusAndPeriod']);
        Route::get('tasks/status/date/before', [CellCuttingTaskController::class, 'getCuttingTasksByStatusBeforeDate']);
        Route::get('tasks/status/date/on', [CellCuttingTaskController::class, 'getCuttingTasksByStatusOnDate']);
        Route::get('tasks/status/date/on/check', [CellCuttingTaskController::class, 'checkCuttingTasksByStatusOnDate']);
        Route::post('tasks/update', [CellCuttingTaskController::class, 'updateCuttingTasks']);
        Route::post('tasks/comment', [CellCuttingTaskController::class, 'setCuttingTaskComment']);
        Route::post('tasks/action/set', [CellCuttingTaskController::class, 'setCuttingTaskActionAt']);
        Route::post('tasks/lines/table/set', [CellCuttingTaskController::class, 'taskLinesTableSet']);
        Route::post('tasks/line/done', [CellCuttingTaskController::class, 'setCuttingTaskLinesDone']);
        Route::post('tasks/line/false', [CellCuttingTaskController::class, 'setCuttingTaskLinesFalse']);
        Route::post('tasks/line/reset', [CellCuttingTaskController::class, 'setCuttingTaskLinesReset']);
        Route::post('tasks/add/order', [CellCuttingTaskController::class, 'addCuttingTasksByOrderId']);
        Route::delete('tasks/delete/order', [CellCuttingTaskController::class, 'deleteCuttingTasksByOrderId']);

        // __ Типовые операции
        Route::get('operations', [CellCuttingOperationController::class, 'getCuttingOperations']);
        Route::get('operations/{id}', [CellCuttingOperationController::class, 'getCuttingOperation']);
        Route::post('operations', [CellCuttingOperationController::class, 'createCuttingOperation']);
        Route::put('operations', [CellCuttingOperationController::class, 'updateCuttingOperation']);

        // __ Схемы Типовых операций
        Route::get('operation/schemas', [CellCuttingOperationSchemaController::class, 'getCuttingOperationSchemas']);
        Route::get('operation/schemas/{id}', [CellCuttingOperationSchemaController::class, 'getCuttingOperationSchema']);
        Route::get('operation/schemas/check/{id}', [CellCuttingOperationSchemaController::class, 'checkCuttingOperationSchemaForSummaryTime']);
        Route::post('operation/schemas/create', [CellCuttingOperationSchemaController::class, 'createCuttingOperationSchema']);
        Route::put('operation/schemas/update', [CellCuttingOperationSchemaController::class, 'updateCuttingOperationSchema']);
        Route::delete('operation/schemas/delete', [CellCuttingOperationSchemaController::class, 'deleteCuttingOperationFromSchema']);
        Route::post('operation/schemas/add', [CellCuttingOperationSchemaController::class, 'addCuttingOperationToSchema']);

        // __ Модели + Типовые операции Раскроя
        Route::get('operation/models', [ModelController::class, 'getModelsForCuttingLabors']);
        Route::patch('operation/schemas/models', [ModelController::class, 'updateModelCuttingOperationSchema']);
        Route::post('operation/models/delete', [ModelController::class, 'deleteCuttingOperationFromModel']);
        Route::post('operation/models/add', [ModelController::class, 'addCuttingOperationToModel']);


        // __ Статусы СЗ Раскроя
        Route::get('/task/statuses', [CellCuttingStatusController::class, 'getCuttingTaskStatuses']);
        Route::patch('/task/statuses/color/patch', [CellCuttingStatusController::class, 'patchCuttingTaskStatusColor']);
        Route::post('/task/statuses/set', [CellCuttingStatusController::class, 'setCuttingTasksStatuses']);

        // __ Производственный день
        Route::get('/day/{date}/{change}', [CellCuttingDayController::class, 'getCuttingDayByDateAndChange']);
        Route::get('/day/dates', [CellCuttingDayController::class, 'getCuttingDaysByDates']);
        Route::post('/day/comment', [CellCuttingDayController::class, 'setCuttingDayComment']);
        Route::post('/day/worker/add', [CellCuttingDayController::class, 'addWorkerToCuttingDay']);
        Route::post('/day/workers/add', [CellCuttingDayController::class, 'addWorkersToCuttingDay']);
        Route::post('/day/worker/remove', [CellCuttingDayController::class, 'removeWorkerFromCuttingDay']);
        Route::patch('/day/responsible/add', [CellCuttingDayController::class, 'addResponsibleToCuttingDay']);
        Route::patch('/day/responsible/remove', [CellCuttingDayController::class, 'removeResponsibleFromCuttingDay']);
        Route::patch('/day/start', [CellCuttingDayController::class, 'startCuttingDay']);
        Route::patch('/day/finish', [CellCuttingDayController::class, 'finishCuttingDay']);
        Route::get('/day/ready/get/{date}/{change}', [CellCuttingDayController::class, 'readyGetCuttingDay']);
        Route::patch('/day/ready/set', [CellCuttingDayController::class, 'readySetCuttingDay']);
        Route::patch('/day/ready/unset', [CellCuttingDayController::class, 'readyUnsetCuttingDay']);

        // __ Процедуры Раскроя
        Route::get('/procedures', [CellCuttingProcedureController::class, 'getCuttingProcedures']);
        Route::get('/procedures/{id}', [CellCuttingProcedureController::class, 'getCuttingProcedure']);
        Route::post('/procedures', [CellCuttingProcedureController::class, 'createCuttingProcedure']);
        Route::put('/procedures', [CellCuttingProcedureController::class, 'updateCuttingProcedure']);
        Route::patch('/models/procedures', [ModelController::class, 'updateModelCuttingProcedure']);
    });
//hr--------------------------------------------------------------------------------------------------------------------


// __ Блок Планов
Route::prefix('/plan')
    ->middleware('jwt.auth')
    ->group(function () {
        Route::post('/loads/upload', [PlanLoadsController::class, 'uploadLoads']);      // ___ Загрузка плана
        Route::post('/loads/validate', [PlanLoadsController::class, 'validateLoads']);  // ___ Проверка плана с Бэка

        Route::get('/loads', [PlanLoadsController::class, 'getPlanLoads']);
        Route::get('/loads/default/period', [PlanLoadsController::class, 'getPlanLoadsDefaultPeriod']);


        Route::get('/business-process-node', [PlanController::class, 'getPlanBusinessProcessNode']);
    });


//hr--------------------------------------------------------------------------------------------------------------------

// __ Блок Документации
Route::prefix('documents')
    ->middleware('jwt.auth')
    ->group(function () {

        //return ['data' => '123'];

        // __ КДЧ
        Route::get('kdch', [TextileDesignDocumentController::class, 'getDocuments']);
        Route::get('kdch/blob/{id}', [TextileDesignDocumentController::class, 'getTextileDesignDocumentByIdBlob']);
        Route::get('kdch/kdch/{kdch}', [TextileDesignDocumentController::class, 'getTextileDesignDocumentByKdch']);
        Route::post('kdch', [TextileDesignDocumentController::class, 'uploadDocument']);
        Route::delete('kdch', [TextileDesignDocumentController::class, 'deleteTextileDesignDocumentById']);
    });

//hr--------------------------------------------------------------------------------------------------------------------

//// descr: Тут просто точки доступа для разных действий
//Route::get('fabrics/tasks/team/number/', [CellFabricServiceController::class, 'getFabricTeamNumberByDate'])->middleware('jwt.auth');
//


//hr--------------------------------------------------------------------------------------------------------------------

// __ Блок Блоков
Route::prefix('blocks')
    ->middleware('jwt.auth')
    ->group(function () {

        //return ['data' => '123'];

        Route::get('collections', [BlockCollectionController::class, 'getBlockCollections']);
        //Route::get('kdch/blob/{id}', [TextileDesignDocumentController::class, 'getTextileDesignDocumentByIdBlob']);
        //Route::get('kdch/kdch/{kdch}', [TextileDesignDocumentController::class, 'getTextileDesignDocumentByKdch']);
        //Route::post('kdch', [TextileDesignDocumentController::class, 'uploadDocument']);
        //Route::delete('kdch', [TextileDesignDocumentController::class, 'deleteTextileDesignDocumentById']);
    });
