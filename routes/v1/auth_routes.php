<?php
use App\Http\Controllers\Api\AuthController;
// use App\Http\Middleware\GuestMiddleware;
// use App\Http\Middleware\AuthMiddleware;

// Тут уже будем работать над функционалом.
// LaravelCreative утверждает, что вместо
// ->middleware('auth') нужно использовать ->middleware('jwt.auth')
// это работает


//attract: Блок Аутентификации
Route::get('auth/me', [AuthController::class, 'me'])
    ->name('me');


// Это, что касается аутентификации
Route::group([
    'middleware' => 'api',
    'prefix'     => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']); // Тут без middleware
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});


