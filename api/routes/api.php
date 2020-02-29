<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/users/login', 'LoginController@login');
Route::post('/users/add', 'UsersController@add');
Route::post('/users/update', 'UsersController@update');
Route::post('/users/delete', 'UsersController@delete');
Route::get('/users/list', 'UsersController@fetch');

Route::get('/roles/list', 'RolesController@fetch');
Route::post('/roles/add', 'RolesController@add');
Route::post('/roles/update', 'RolesController@update');
Route::post('/roles/delete', 'RolesController@delete');
