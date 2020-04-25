<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::match(['get', 'post'], '/allFlightsReport', ['uses' => 'FlightsController@allFlightsReport'])->name('flights.allFlightsReport');
Route::match(['get'], '/reports/membersRolesStatsReport', ['uses' => 'ReportsController@membersRolesStatsReport'])->name('reports.membersRolesStatsReport');

Route::resource('vectors', 'VectorsController');
Route::resource('members', 'MembersController')->only(['create', 'edit', 'store', 'update']);
Route::resource('members.documents', 'DocumentsController')->only(['store', 'update']);