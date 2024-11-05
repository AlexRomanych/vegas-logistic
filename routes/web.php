<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
////    return view('welcome.blade.php');
//});
//
//Route::get('/ui', function () {
//    return view('welcome');
////    return view('welcome.blade.php');
//});


//Route::any('/', function () {
//    return view('welcome');
////    return view('welcome.blade.php');
//});

Route::get('/test', function () {
    $user = User::where('email', '1')->first();

    return $user;
})->where('any', '.*');


Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
