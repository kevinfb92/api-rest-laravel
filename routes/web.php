<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddleware;

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

//Production routes
Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@login');
Route::put('/user/update', 'UserController@update');
Route::get('/user/detail/{id}', 'UserController@detail');
Route::post('/video/create', 'VideoController@create')->middleware(ApiAuthMiddleware::class);
Route::get('/video', 'VideoController@listAll');
Route::get('/video/detail/{id}', 'VideoController@detail');
