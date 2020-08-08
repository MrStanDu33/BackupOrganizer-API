<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/user')->group(function() {
    Route::post('/register', 'api\v1\AuthController@register');
    Route::post('/login', 'api\v1\AuthController@login');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/', 'api\v1\user\UserController@index');
        Route::get('/me', function(Request $request) { return response(['user' => Auth::user()], config('httpcodes.OK')); });
    });
});

Route::middleware(['auth:api'])->group(function() {
    Route::group(['prefix' =>'/customer'], function() {
        Route::get('/', 'api\v1\customer\CustomerController@index');
        Route::post('/', 'api\v1\customer\CustomerController@create');
        Route::get('/{customerId}', 'api\v1\customer\CustomerController@show');
        Route::put('/{customerId}', 'api\v1\customer\CustomerController@update');
        Route::delete('/{customerId}', 'api\v1\customer\CustomerController@delete');
    });
    Route::group(['prefix' =>'/project'], function() {
        Route::get('/', 'api\v1\project\ProjectController@index');
        Route::post('/', 'api\v1\project\ProjectController@create');
        Route::get('/{projectId}', 'api\v1\project\ProjectController@show');
        Route::put('/{projectId}', 'api\v1\project\ProjectController@update');
        Route::delete('/{projectId}', 'api\v1\project\ProjectController@delete');
    });
    Route::group(['prefix' =>'/website'], function() {
        Route::get('/', 'api\v1\website\WebsiteController@index');
        Route::post('/', 'api\v1\website\WebsiteController@create');
        Route::get('/{websiteId}', 'api\v1\website\WebsiteController@show');
        Route::put('/{websiteId}', 'api\v1\website\WebsiteController@update');
        Route::delete('/{websiteId}', 'api\v1\website\WebsiteController@delete');
    });
    Route::group(['prefix' =>'/database'], function() {
        Route::get('/', 'api\v1\database\DatabaseController@index');
        Route::post('/', 'api\v1\database\DatabaseController@create');
        Route::get('/{databaseId}', 'api\v1\database\DatabaseController@show');
        Route::put('/{databaseId}', 'api\v1\database\DatabaseController@update');
        Route::delete('/{databaseId}', 'api\v1\database\DatabaseController@delete');
    });
});
