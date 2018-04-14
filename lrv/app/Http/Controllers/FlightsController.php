<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use DateTimeZone;
use DateTime;

use App\Models\Organisation;
use App\Models\Flight;
use App\Models\Member;

class FlightsController extends Controller
{
    /**
     * Display a listing of all the flights.
     *
     * @return \Illuminate\Http\Response
     */
    public function allFlightsReport(Request $request)
    {
        set_time_limit(120);
        $user = Auth::user();

        $dateTimeZone = new DateTimeZone($_SESSION['timezone']);
        $dateTime = new DateTime('now', $dateTimeZone);
        $dateStr = $dateTime->format('Y-m-d');

        $strDateFrom  = $request->input("fromdate", $dateStr);
        $strDateTo    = $request->input("todate", $dateStr);

        $dateStart2 = substr($strDateFrom,0,4) . substr($strDateFrom,5,2) . substr($strDateFrom,8,2);
        $dateEnd2 = substr($strDateTo,0,4) . substr($strDateTo,5,2) . substr($strDateTo,8,2);

        $flights = Flight::with(['picMember', 'p2Member', 'towPilotMember', 'towPlane', 'launchType'])
                        ->where('org', $_SESSION['org'])
                        ->where('localdate', '>=', $dateStart2)
                        ->where('localdate', '<=', $dateEnd2)
                        ->orderBy('localdate')
                        ->orderBy('seq');

        $filterByMember = null;
        if ($request->has('filterByMemberId')) {
            $memberId = $request->input('filterByMemberId');
            $filterByMember = Member::where('id', $memberId)->first();
        }
        if($filterByMember) {
            $flights = $flights->where(function($query) use($memberId){
                $query->where('pic', $memberId)->orWhere('p2', $memberId);
            });
        }

        $allFlights = $flights->get();

        return response()->view('allFlightsReport', [
            'filterByMember' => $filterByMember,
            'flights' => $allFlights,
            'strDateFrom' => $strDateFrom,
            'strDateTo' => $strDateTo,
            'towChargeType' => $user->organisation->getTowChargeType(),
            'timezone' => $user->organisation->timezone
        ]);
    }
}