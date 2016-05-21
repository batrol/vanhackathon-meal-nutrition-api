<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::group([
    'prefix'     => 'api/v1',
    'namespace' => 'Api'
], function () {

    Route::get('recipe/{id}/nutrition-info', 'RecipeController@nutritionInfo');
    Route::get('recipe/name/{name}/', 'RecipeController@searchByName');
    Route::get('recipe/user/{id}/', 'RecipeController@searchByUser');
    Route::get('recipe/calories/min/{min}', 'RecipeController@searchByCaloriesMin');
    Route::get('recipe/calories/max/{max}', 'RecipeController@searchByCaloriesMax');
    Route::get('recipe/calories/range/{min}/{max}', 'RecipeController@searchByCaloriesRange');
    Route::post('recipe', 'RecipeController@store');
    Route::put('recipe/{id}', 'RecipeController@update');
});

