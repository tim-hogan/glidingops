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

Route::get('/members', function (Request $request) {
  return response()->json([
      'data' => App\Member::all()
  ]);
}); //->middleware('auth:api');

Route::get('/flights', function (Request $request) {
  $orgInput       = $request->input('org');
  $localdateInput = $request->input('localdate');

  $org = App\Organisation::find($orgInput);
  if(! $localdateInput) {
    $dateTimeZone = new DateTimeZone($org->timezone);
    $dateTime     = new DateTime('now', $dateTimeZone);
    $localdate    = $dateTime->format('Ymd');
  } else {
    $localdate = $localdateInput;
  }

  return response()->json([
      'data' => App\Flight::where(['org' => $org->id, 'localdate' => $localdate])->get()
  ]);
}); //->middleware('auth:api');