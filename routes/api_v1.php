<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CellItemController;
use App\Http\Controllers\Api\V1\Cells\sewing\CellSewingController;
use App\Http\Controllers\Api\V1\CellsGroupController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\ModelController;
use App\Http\Controllers\Api\V1\OrderController;
//use App\Http\Controllers\AuthController;
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
Route::get('/model/{code1C}', [ModelController::class, 'model']);
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
Route::put('/worker', [WorkerController::class, 'update'])->middleware('jwt.auth');
Route::post('/worker', [WorkerController::class, 'create'])->middleware('jwt.auth');
Route::delete('/worker', [WorkerController::class, 'destroy'])->middleware('jwt.auth');


//hr--------------------------------------------------------------------------------------------------------------------
