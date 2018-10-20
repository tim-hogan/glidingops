<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Services\Reports\MembersRolesStats;
use App\Services\Reports\Treasurer;
use App\Models\Organisation;
use DateTime;

class ReportsController extends Controller
{
  /**
     * Display a listing of all user roles sorted by number of roles held.
     *
     * @return \Illuminate\Http\Response
     */
    public function membersRolesStatsReport(Request $request)
    {
      $user = Auth::user();
      $org = $_SESSION['org'];

      $report = MembersRolesStats::build($user, $org);
      return response()->view('reports/membersRolesStats', $report);
    }

    public function treasurerReport(Request $request)
    {
      $user = Auth::user();
      $orgId = $_SESSION['org'];
      $organisation = Organisation::find($orgId);

      if($request->has('monthYear')){
        $monthYearDate = new DateTime($request->input("monthYear"));
      } else {
        $monthYearDate = (new DateTime('now'))->modify('-1 month');
      }

      $report = Treasurer::build($user, $organisation, $monthYearDate);
      $report['monthYear'] = $monthYearDate->format('Y-m');

      return response()->view('reports/treasurer', $report);
    }
}