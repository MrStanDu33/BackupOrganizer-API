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
        Route::get('/{id}', 'api\v1\customer\CustomerController@show');
        Route::put('/{id}', 'api\v1\customer\CustomerController@update');
        Route::delete('/{id}', 'api\v1\customer\CustomerController@delete');
    });
});
