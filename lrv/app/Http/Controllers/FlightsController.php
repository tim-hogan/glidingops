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
use DB;

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

        // $flights = Flight::with(['picMember', 'p2Member', 'towPilotMember', 'towPlane', 'launchType'])
        //                 ->where('org', $_SESSION['org'])
        //                 ->where('localdate', '>=', $dateStart2)
        //                 ->where('localdate', '<=', $dateEnd2)
        //                 ->orderBy('localdate')
        //                 ->orderBy('seq');

        // $q="SELECT
        //     flights.id ,
        //     flights.localdate,
        //     flights.seq,
        //     flights.glider,
        //     flights.height,
        //     flights.comments,
        //     flights.launchtype,
        //     flights.type ,
        //     flights.location,
        //     (flights.start/1000),
        //     (flights.land/1000),
        //     (flights.towland/1000) ,
        //     (flights.land-flights.start),
        //     (flights.towland-flights.start)
        //     e.rego_short,
        //     a.displayname ,
        //     b.displayname ,
        //     c.displayname ,
        //     d.name ,
        //     f.name,
        //     FROM flights
        //     LEFT JOIN members a ON a.id = flights.towpilot
        //     LEFT JOIN members b ON b.id = flights.pic
        //     LEFT JOIN members c ON c.id = flights.p2
        //     LEFT JOIN billingoptions d ON d.id = flights.billing_option
        //     LEFT JOIN aircraft e ON e.id = flights.towplane
        //     LEFT JOIN launchtypes f ON f.id = flights.launchtype
        //     WHERE flights.org = ".$_SESSION['org']." and localdate >= " . $dateStart2 . " and localdate <= " . $dateEnd2 . " order by localdate,seq";

        $flights = DB::table('flights')
            ->select(
                'flights.id',
                'flights.pic',
                'flights.p2',
                'flights.localdate',
                'flights.seq',
                'flights.glider',
                'flights.height',
                'flights.comments',
                'flights.type',
                'flights.location',
                'flights.start',
                'flights.land',
                'flights.towland',
                'flights.launchtype',
                DB::raw('flights.land-flights.start AS flightDuration'),
                DB::raw('IF(towland != 0, flights.towland-flights.start, 0) AS towDuration'),
                'towplanes.rego_short AS towplane_rego_short',
                'towpilots.displayname AS towpilot_displayname',
                'pics.displayname AS pic_displayname',
                'p2s.displayname AS p2_displayname',
                'launchtypes.name AS launchtype_name',
                'billingoptions.name AS billingoption_name')
            ->leftJoin('members AS towpilots', 'towpilots.id', '=', 'flights.towpilot')
            ->leftJoin('members AS pics', 'pics.id', '=', 'flights.pic')
            ->leftJoin('members AS p2s', 'p2s.id', '=', 'flights.p2')
            ->leftJoin('billingoptions AS billingoptions', 'billingoptions.id', '=', 'flights.billing_option')
            ->leftJoin('aircraft AS towplanes', 'towplanes.id', '=', 'flights.towplane')
            ->leftJoin('launchtypes AS launchtypes', 'launchtypes.id', '=', 'flights.launchtype')
            ->where('flights.org', $_SESSION['org'])
            ->where('flights.localdate', '>=', $dateStart2)
            ->where('flights.localdate', '<=', $dateEnd2)
            ->orderBy('flights.localdate')
            ->orderBy('flights.seq');

        $filterByMember = null;
        if ($request->has('filterByMemberId')) {
            $memberId = $request->input('filterByMemberId');
            $filterByMember = Member::where('id', $memberId)->first();
        }
        if($filterByMember) {
            $flights = $flights->where(function($query) use($memberId){
                $query->where('flights.pic', $memberId)
                      ->orWhere('flights.p2', $memberId);
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