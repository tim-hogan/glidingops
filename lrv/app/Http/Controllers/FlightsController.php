<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DateTimeZone;
use DateTime;

use App\Organisation;
use App\Flight;

class FlightsController extends Controller
{
    /**
     * Display a listing of all the flights.
     *
     * @return \Illuminate\Http\Response
     */
    public function allFlightsReport()
    {
        $orgId = Input::get('org');
        $flights  = Flight::where(['org' => $orgId])->get();

        return response()->view('allFlightsReport', ['flights' => $flights]);
    }
}