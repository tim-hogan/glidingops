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

Route::resource('members', 'MembersController', ['only' => [
    'index'
  ]
]);

Route::resource('flights', 'FlightsController', ['only' => [
    'index'
  ]
]);

Route::resource('launch-types', 'LaunchTypesController', ['only' => [
    'index'
  ]
]);

Route::resource('aircrafts', 'AircraftsController', ['only' => [
    'index'
  ]
]);