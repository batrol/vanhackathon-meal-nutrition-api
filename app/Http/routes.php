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
    Route::get('recipe/energy/min/{min}', 'RecipeController@searchByEnergyMin');
    Route::get('recipe/energy/max/{max}', 'RecipeController@searchByEnergyMax');
    Route::get('recipe/energy/range/{min}/{max}', 'RecipeController@searchByEnergyRange');
    Route::post('recipe', 'RecipeController@store');
    Route::put('recipe/{id}', 'RecipeController@update');
    Route::get('recipe/{id}', 'RecipeController@show');
});

