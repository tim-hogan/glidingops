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
Route::match(['get'], '/reports/membersRolesStats', ['uses' => 'ReportsController@membersRolesStatsReport'])->name('reports.membersRolesStats');

Route::match(['get'], '/reports/treasurer', ['uses' => 'ReportsController@treasurerReportNew'])->name('reports.treasurer');
Route::match(['post'], '/reports/treasurer', ['uses' => 'ReportsController@treasurerReport'])->name('reports.treasurer');

Route::resources([
  'vectors' => 'VectorsController'
]);