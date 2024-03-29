<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'lighthouse-dashboard', 'middleware' => SubstituteBindings::class, 'namespace' => 'App\Http\Controllers'], function () {

    Route::get('/', 'WelcomeController@index');

    Route::get('/operations', 'OperationController@index');
    Route::get('/operations/{operation}', 'OperationController@show');
    Route::get('/operations/{operation}/sumary', 'OperationController@sumary');

    Route::get('/types', 'TypeController@index');
    Route::get('/fields/{field}/sumary', 'FieldController@sumary');

    Route::get('/errors', 'ErrorsController@index');
});
