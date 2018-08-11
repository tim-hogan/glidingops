<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Models\Organisation;
use App\Models\Flight;
use App\Models\Member;
use App\Models\Role;
use DB;

class UsersController extends Controller
{
  /**
     * Display a listing of all user roles sorted by number of roles held.
     *
     * @return \Illuminate\Http\Response
     */
    public function userRolesStatsReport(Request $request)
    {
      $user = Auth::user();
      $org = $_SESSION['org'];

      $roleNames = Role::all()->pluck('name', 'id')->all();

      $roleCountSql = DB::table('members')
        ->select('members.id AS memberId', DB::raw('count(member_roles.id) as roleCount'))
        ->leftJoin('role_member AS member_roles', 'member_roles.member_id', '=', 'members.id')
        ->where('members.org', $org)
        ->groupBy('members.id');

      $members = DB::table(DB::raw('(' . $roleCountSql->toSql() . ')  as roleCounts'))
        ->mergeBindings($roleCountSql)
        ->select('members.id', 'members.displayname', 'roles.name AS roleName', 'roleCounts.roleCount')
        ->leftJoin('members', 'members.id', '=', 'roleCounts.memberId')
        ->leftJoin('role_member AS member_roles', 'member_roles.member_id', '=', 'members.id')
        ->leftJoin('roles AS roles', 'roles.id', '=', 'member_roles.role_id')
        ->where('members.org', $org)
        ->orderBy('roleCounts.roleCount', 'desc')
        ->orderBy('members.id');

      return response()->view('userRolesStatsReport', [
          'roleNames' => $roleNames,
          'members' => $members->get(),
      ]);
    }
}