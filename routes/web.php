<?php

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

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');


