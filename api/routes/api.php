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
Route::post('/users/changepass', 'UsersController@changePass');
Route::get('/users/list', 'UsersController@fetch');

Route::get('/roles/list', 'RolesController@fetch');
Route::post('/roles/add', 'RolesController@add');
Route::post('/roles/update', 'RolesController@update');
Route::post('/roles/delete', 'RolesController@delete');

Route::get('/categories/list', 'CategoriesController@fetch');
Route::post('/categories/add', 'CategoriesController@add');
Route::post('/categories/update', 'CategoriesController@update');
Route::post('/categories/delete', 'CategoriesController@delete');

Route::get('/expenses/list', 'ExpensesController@fetch');
Route::post('/expenses/add', 'ExpensesController@add');
Route::post('/expenses/update', 'ExpensesController@update');
Route::post('/expenses/delete', 'ExpensesController@delete');