<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pruebas', 'Pruebas@index');


Route::get('/test', 'Pruebas@testORM');

//Test routes
Route::get('/usuario/pruebas', 'UserController@pruebas');
Route::get('/video/pruebas', 'VideoController@pruebas');

//Production routes
Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@login');
Route::put('/user/update', 'UserController@update');