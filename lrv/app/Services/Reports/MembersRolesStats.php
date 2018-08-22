<?php

namespace App\Services\Reports;

use App\Models\Organisation;
use App\Models\Flight;
use App\Models\Member;
use App\Models\Role;
use App\Models\MembershipStatus;
use Illuminate\Support\Collection;
use DB;
use stdClass;

class MembersRolesStats
{
  public static function build($user, $org) {
    $roleNames = new Collection([
      Role::ROLE_AB_CAT_INSTRUCTOR,
      Role::ROLE_C_CAT_INSTRUCTOR,
      Role::ROLE_WINCH_DRIVER,
      Role::ROLE_LPC,
      Role::ROLE_ENGINEER,
      Role::ROLE_TOW_PILOT,
      Role::ROLE_CMT
    ]);

    $roleCountSql = DB::table('members')
      ->select('members.id AS memberId', DB::raw('count(member_roles.id) as roleCount'))
      ->leftJoin('role_member AS member_roles', 'member_roles.member_id', '=', 'members.id')
      ->leftJoin('roles AS roles', 'roles.id', '=', 'member_roles.role_id')
      ->where(function($query) use ($roleNames) {
        $query->whereIn('roles.name', $roleNames)
              ->orWhere('roles.name', NULL);
      })
      ->where('members.org', $org)
      ->groupBy('members.id');

    $members = DB::table(DB::raw('(' . $roleCountSql->toSql() . ')  as roleCounts'))
      ->mergeBindings($roleCountSql)
      ->select('members.id', 'members.displayname', 'roles.name AS roleName', 'roleCounts.roleCount')
      ->leftJoin('members', 'members.id', '=', 'roleCounts.memberId')
      ->leftJoin('role_member AS member_roles', 'member_roles.member_id', '=', 'members.id')
      ->leftJoin('roles AS roles', 'roles.id', '=', 'member_roles.role_id')
      ->where('members.org', $org)
      ->where('members.status', MembershipStatus::activeStatus()->id)
      ->orderBy('roleCounts.roleCount', 'desc')
      ->orderBy('members.id');

    $byMemberId = $members->get()->groupBy(function($item){
      return $item->id;
    });
    $membersWithRoles = $byMemberId->map(function($rows, $id){
      $member = new stdClass();
      $member->id = $id;
      $member->roles = new Collection([]);
      $rows->each(function($item) use ($member) {
        $member->displayname = $item->displayname;
        $member->roles->push($item->roleName);
      });

      $member->roles = self::regroupRoles($member->roles);
      return $member;
    });

    return [
      'roleNames' => self::regroupRoles($roleNames),
      'members' => $membersWithRoles,
    ];
  }

  private static function regroupRoles($rolesCollection) {
    return $rolesCollection->map(function ($roleName) {
      switch ($roleName) {
        case Role::ROLE_AB_CAT_INSTRUCTOR:
        case Role::ROLE_C_CAT_INSTRUCTOR:
          return 'Instructor';
        default:
          return $roleName;
      };
    })->unique();
  }
}