<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CellItemController;
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
use App\Http\Controllers\Api\V1\Cells\sewing\CellSewingController;
use App\Http\Controllers\Api\V1\CellsGroupController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\ModelController;
use App\Http\Controllers\Api\V1\OrderController;

//use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\ReasonController;
use App\Http\Controllers\Api\V1\WorkerController;
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
Route::get('/models/update', [Update::class, 'updateModelsAndCollections'])->name('models.update');
//route::get('/models/update', [Update::class, 'updateModelsAndCollections'])->name('models.update');

//Route::get('models', [ModelController::class, 'all'])->name('models.all');
//Route::get('model/{id}', [ModelController::class, 'show'])->name('models.show');

//Route::group([
//
//    'middleware' => 'api',
//    'prefix' => 'auth'
//
//], function ($router) {
//
////    dd($router);
//
//    Route::post('login', [AuthController::class, 'login']);
//    Route::post('logout', [AuthController::class, 'logout']);
//    Route::post('refresh', [AuthController::class, 'refresh']);
//    Route::post('me', [AuthController::class, 'me']);
//
//
//});

//Route::
//    prefix('auth')
//    ->group(function () {
//        Route::post('login', [AuthController::class, 'login']);
//        Route::post('logout', [AuthController::class, 'logout']);
//        Route::post('refresh', [AuthController::class, 'refresh']);
//        Route::post('me', [AuthController::class, 'me']);
//    });

//Route::middleware('auth:api', ['except' => ['login', 'logout', 'refresh', 'me']])
//    ->prefix('auth')
//    ->group(function () {
//        Route::post('login', [AuthController::class, 'login']);
//        Route::post('logout', [AuthController::class, 'logout']);
//        Route::post('refresh', [AuthController::class, 'refresh']);
//        Route::post('me', [AuthController::class, 'me']);
//    });


//use App\Http\Controllers\JWTAuthController;
//use App\Http\Middleware\JwtMiddleware;
//
//Route::prefix('auth')->group(function () {
//    Route::post('register', [JWTAuthController::class, 'register']);
//    Route::post('login', [JWTAuthController::class, 'login']);
//
//    Route::middleware([JwtMiddleware::class])->group(function () {
//        Route::get('user', [JWTAuthController::class, 'getUser']);
//        Route::post('logout', [JWTAuthController::class, 'logout']);
//    });
//});
//


use App\Http\Controllers\UserController;

//use App\Http\Controllers\AuthController;
use App\Http\Middleware\GuestMiddleware;
use App\Http\Middleware\AuthMiddleware;

//Route::middleware(GuestMiddleware::class)
//    ->prefix('auth')
//    ->group(function () {
//        Route::post('login', [AuthController::class, 'login'])
//    ->name('login');
//});
//
//Route::middleware(AuthMiddleware::class)
//    ->prefix('auth')
//    ->group(function () {
//        Route::get('me', [AuthController::class, 'me'])
//    ->name('me');
//});
//
//Route::apiResource('users', UserController::class);


// Тут уже будем работать над функционалом.
// LaravelCreative утверждает, что вместо
// ->middleware('auth') нужно использовать ->middleware('jwt.auth')
// это работает


//todo: Разбить все эндпоинты на несколько файлов

//hr--------------------------------------------------------------------------------------------------------------------
//attract: Блок Пользователей
Route::get('/user', [UserController::class, 'index']);
Route::get('/users', [UserController::class, 'all']);
//hr--------------------------------------------------------------------------------------------------------------------

//hr--------------------------------------------------------------------------------------------------------------------
//attract: Блок Моделей
Route::get('/models', [ModelController::class, 'models'])->middleware('jwt.auth');
Route::get('/model/{code1C}', [ModelController::class, 'model'])->middleware('jwt.auth');;
Route::get('/models/load', [ModelController::class, 'modelsLoad'])->middleware('jwt.auth');
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

//Route::post('/orders/upload', [OrderController::class, 'uploadOrders'])->middleware('jwt.auth');
//Route::('/orders/upload', [OrderController::class, 'uploadOrders']);
//Route::patch('/orders/upload', [OrderController::class, 'uploadOrders']);
//Route::put('/orders/upload', [OrderController::class, 'uploadOrders']);

//hr--------------------------------------------------------------------------------------------------------------------
//attract: Блок Клиентов
//загружаем из файла клиентов
Route::get('/clients', [ClientController::class, 'getClients'])->middleware('jwt.auth');
Route::get('/clients/load', [ClientController::class, 'clientsLoad'])->middleware('jwt.auth');
//Route::get('/clients/load', [UpdateData1CController::class, 'clientsLoad'])->middleware('jwt.auth');
//Route::get('/clients/load', [Update::class, 'updateManagers']);
//hr--------------------------------------------------------------------------------------------------------------------


//hr--------------------------------------------------------------------------------------------------------------------
//attract: Блок Аутентификации
Route::get('auth/me', [AuthController::class, 'me'])
    ->name('me');


// Это, что касается аутентификации
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']); // Тут без middleware
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});
//hr--------------------------------------------------------------------------------------------------------------------


//hr--------------------------------------------------------------------------------------------------------------------
// attract: Блок Персонала
Route::get('/workers', [WorkerController::class, 'workers'])->middleware('jwt.auth');
Route::get('/worker/{id}', [WorkerController::class, 'show'])->middleware('jwt.auth');
Route::get('/worker/edit/{id}', [WorkerController::class, 'show'])->middleware('jwt.auth');
Route::put('/worker', [WorkerController::class, 'update'])->middleware('jwt.auth');
Route::post('/worker', [WorkerController::class, 'create'])->middleware('jwt.auth');
Route::delete('/worker', [WorkerController::class, 'destroy'])->middleware('jwt.auth');

// заполняем таблицу тестовыми данные
Route::get('/workers/test/fill', [WorkerController::class, 'testFill']);

//hr--------------------------------------------------------------------------------------------------------------------


//hr--------------------------------------------------------------------------------------------------------------------
// __ Блок Стежки (ПС)
Route::post('/fabrics/upload', [CellFabricController::class, 'uploadFabrics'])->middleware('jwt.auth');
Route::get('/fabrics', [CellFabricController::class, 'fabrics'])->middleware('jwt.auth');
Route::get('/fabric/{id}', [CellFabricController::class, 'fabric'])->middleware('jwt.auth');;
Route::put('/fabric', [CellFabricController::class, 'update'])->middleware('jwt.auth');
Route::post('/fabric', [CellFabricController::class, 'create'])->middleware('jwt.auth');
Route::delete('/fabric', [CellFabricController::class, 'destroy'])->middleware('jwt.auth');
Route::get('/fabrics/buffer/update/', [CellFabricController::class, 'updateFabricsBuffer'])->middleware('jwt.auth');
Route::get('/fabric/average/length/', [CellFabricController::class, 'getFabricAverageLength'])->middleware('jwt.auth');

// __ Рисунки стежки ПС
Route::get('/fabrics/pictures', [CellFabricPictureController::class, 'getFabricPictures'])->middleware('jwt.auth');
Route::put('/fabrics/pictures/update', [CellFabricPictureController::class, 'updateFabricPictures'])->middleware('jwt.auth');
Route::post('/fabrics/pictures/create', [CellFabricPictureController::class, 'createFabricPictures'])->middleware('jwt.auth');
Route::get('/fabrics/picture/{id}', [CellFabricPictureController::class, 'getFabricPicture'])->middleware('jwt.auth');
Route::post('/fabrics/pictures/upload', [CellFabricPictureController::class, 'uploadFabricPictures'])->middleware('jwt.auth');
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


        Route::put('/execute/roll/update/', [CellFabricTaskRollController::class, 'update']);
        Route::post('/execute/roll/add/', [CellFabricTaskRollController::class, 'addExecuteRoll']);
        Route::get('/rolls/done/', [CellFabricTaskRollController::class, 'getNotAcceptedToCutRolls']);
        Route::patch('/execute/roll/registered/', [CellFabricTaskRollController::class, 'setRollRegisteredStatus']);
        Route::patch('/execute/roll/moved/', [CellFabricTaskRollController::class, 'setRollMovedStatus']);
        Route::post('/execute/save/rolls/order/', [CellFabricTaskRollController::class, 'saveExecuteRollsOrder']);


        // descr: Тут просто точки доступа для разных действий
        Route::get('/team/number/', [CellFabricServiceController::class, 'getFabricTeamNumberByDate']);


    });


//attract: Тесты: Очистка таблицы Заданий Стежки
//Route::get('/fabrics/tasks/clear/', [CellFabricServiceController::class, 'clearFabricTasksDateTable']);


// __ Блок Причин
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



        //        Route::get('/', [CellFabricTasksDateController::class, 'tasks']);
//
//        Route::get('/last/done/', [CellFabricTasksDateController::class, 'getLastDoneTask']);
//        Route::patch('/status/change/', [CellFabricTasksDateController::class, 'statusChange']);
//        Route::put('/create/', [CellFabricTasksDateController::class, 'create']);
//        Route::put('/workers/update/', [CellFabricTasksDateController::class, 'workersUpdate']);
//        Route::get('/executing/', [CellFabricTasksDateController::class, 'getFabricExecutingTasks']);
//        Route::get('/not-done/', [CellFabricTasksDateController::class, 'getFabricNotDoneTasks']);
//        Route::get('/close/', [CellFabricTasksDateController::class, 'closeFabricTasks']);
//
//        Route::delete('/context/delete/', [CellFabricTaskContextController::class, 'deleteContext']);
//        Route::get('/context/not-done/', [CellFabricTaskContextController::class, 'getContextNotDone']);
//        Route::put('/context/expense/create/', [CellFabricTaskContextController::class, 'createContextExpense']);
//
//        Route::put('/execute/roll/update/', [CellFabricTaskRollController::class, 'update']);
//        Route::post('/execute/roll/add/', [CellFabricTaskRollController::class, 'addExecuteRoll']);
//        Route::get('/rolls/done/', [CellFabricTaskRollController::class, 'getNotAcceptedToCutRolls']);
//        Route::patch('/execute/roll/registered/', [CellFabricTaskRollController::class, 'setRollRegisteredStatus']);
//        Route::patch('/execute/roll/moved/', [CellFabricTaskRollController::class, 'setRollMovedStatus']);
//
//
//        // descr: Тут просто точки доступа для разных действий
//        Route::get('/team/number/', [CellFabricServiceController::class, 'getFabricTeamNumberByDate']);


    });






//
//
//Route::get('/fabrics/tasks', [CellFabricTasksDateController::class, 'tasks'])->middleware('jwt.auth');
//
//Route::get('fabrics/tasks/last/done/', [CellFabricTasksDateController::class, 'getLastDoneTask'])->middleware('jwt.auth');
//Route::patch('fabrics/tasks/status/change/', [CellFabricTasksDateController::class, 'statusChange'])->middleware('jwt.auth');
//Route::put('/fabrics/tasks/create/', [CellFabricTasksDateController::class, 'create'])->middleware('jwt.auth');
//Route::delete('fabrics/tasks/context/delete/', [CellFabricTasksDateController::class, 'deleteContext'])->middleware('jwt.auth');
//Route::put('/fabrics/tasks/workers/update/', [CellFabricTasksDateController::class, 'workersUpdate'])->middleware('jwt.auth');
//
//Route::put('/fabrics/tasks/execute/roll/update/', [CellFabricTaskRollController::class, 'update'])->middleware('jwt.auth');
//
//Route::get('fabrics/tasks/executing/', [CellFabricTaskRollController::class, 'getFabricExecutingTasks'])->middleware('jwt.auth');
//
//// descr: Тут просто точки доступа для разных действий
//Route::get('fabrics/tasks/team/number/', [CellFabricServiceController::class, 'getFabricTeamNumberByDate'])->middleware('jwt.auth');
//

//Route::get('/fabrics/tasks', [CellFabricTaskController::class, 'tasks'])->middleware('jwt.auth');
//Route::put('/fabrics/tasks/create/', [CellFabricTaskController::class, 'create'])->middleware('jwt.auth');
//Route::patch('fabrics/tasks/status/change/', [CellFabricTaskController::class, 'statusChange'])->middleware('jwt.auth');

//hr--------------------------------------------------------------------------------------------------------------------

