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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/token', 'UsersController@refreshOrCreate');
Route::middleware('tokenValidator')->group(function(){
    Route::get('/users', 'UsersController@get');
    Route::post('/test', 'TestController@test');
    Route::post('/items', 'ItemsController@create');
    Route::post('/channel', 'LiveStreamController@start');
    Route::get('/items', 'ItemsController@get');
});
