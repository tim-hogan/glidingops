<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use DateTimeZone;
use DateTime;

use App\Models\Organisation;
use App\Models\Flight;

class FlightsController extends Controller
{
    /**
     * Display a listing of all the flights.
     *
     * @return \Illuminate\Http\Response
     */
    public function allFlightsReport(Request $request)
    {
        $user = Auth::user();

        $dateTimeZone = new DateTimeZone($_SESSION['timezone']);
        $dateTime = new DateTime('now', $dateTimeZone);
        $dateStr = $dateTime->format('Y-m-d');

        $strDateFrom  = $request->input("fromdate", $dateStr);
        $strDateTo    = $request->input("todate", $dateStr);

        $dateStart2 = substr($strDateFrom,0,4) . substr($strDateFrom,5,2) . substr($strDateFrom,8,2);
        $dateEnd2 = substr($strDateTo,0,4) . substr($strDateTo,5,2) . substr($strDateTo,8,2);

        $flights = Flight::with(['picMember', 'p2Member', 'towPilotMember'])
                        ->where('org', $_SESSION['org'])
                        ->where('localdate', '>=', $dateStart2)
                        ->where('localdate', '<=', $dateEnd2)
                        ->orderBy('localdate')
                        ->orderBy('seq');

        $allFlights = $flights->get();
        $towTotalTime = $allFlights->reduce(function ($carry, $flight) {
            return $carry + $flight->getTowDuration();
        }, 0);
        $gliderTotalTime = $allFlights->reduce(function ($carry, $flight) {
            return $carry + $flight->getFlightDuration();
        }, 0);

        return response()->view('allFlightsReport', [
            'organisation' => $user->organisation,
            'flights' => $allFlights,
            'strDateFrom' => $strDateFrom,
            'strDateTo' => $strDateTo,
            'towTotalTime' => $towTotalTime,
            'gliderTotalTime' => $gliderTotalTime
        ]);
    }
}