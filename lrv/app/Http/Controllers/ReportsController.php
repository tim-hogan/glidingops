<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Services\Reports\MembersRolesStats;

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
      return response()->view('reports/membersRolesStatsReport', $report);
    }
}