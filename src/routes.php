<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'lighthouse-dashboard', 'middleware' => 'bindings', 'namespace' => 'App\Http\Controllers'], function () {

    Route::get('/', 'WelcomeController@index');

    Route::get('/operations', 'OperationController@index');
    Route::get('/types', 'TypeStatisticController@index');
    Route::get('/fields/{field}/sumary', 'FieldSumaryController@index');
});
