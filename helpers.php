<?php
require_once 'load_model.php';

function getOrgAircraftPrefix($db,$org)
{
   $ret='';
   $q="SELECT aircraft_prefix from organisations where id = " . $org;
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   return $ret;
}

function getOrgDefautLaunchLat($db,$org)
{
   $ret=0.0;
   $q="SELECT def_launch_lat from organisations where id = " . $org;
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   return $ret;
}

function getOrgDefautLaunchLon($db,$org)
{
   $ret=0.0;
   $q="SELECT def_launch_lon from organisations where id = " . $org;
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   return $ret;
}

function getFlightType($strType)
{
  $flightType= App\Models\FlightType::where('name', $strType)->first();
  if($flightType) {
    return $flightType->id;
  }

  return -1.0;
}

function getGlidingFlightType($db)
{
    return getFlightType('Glider');
}

function getCheckFlightType($db)
{
    return getFlightType('Tow plane check flight');
}

function getRetrieveFlightType($db)
{
    return getFlightType('Tow plane retrieve');
}

function getLandingFeeFlightType($db)
{
    return getFlightType('Landing Charge');
}

function getNoChargeOpt($db)
{
   $ret=-1.0;
   $q="SELECT id from billingoptions where name = 'No Charge'";
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   mysqli_free_result($r);
   return $ret;
}

function getRoleId($db,$strRole)
{
   $ret=-1.0;
   $q="SELECT id from roles where name = '" . $strRole . "'";
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   mysqli_free_result($r);
   return $ret;
}

function getClassId($db,$org,$strClass)
{
   $ret=-1.0;
   $q="SELECT id FROM membership_class WHERE org = ".$org." and class = '" . $strClass . "'";
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   mysqli_free_result($r);
   return $ret;
}
function getJuniorClass($db,$org){return getClassId($db,$org,'Junior');}
function getShortTermClass($db,$org){return getClassId($db,$org,'Short Term');}
function getNonFlyingClass($db,$org){return getClassId($db,$org,'Non Flying');}
function getAircraftType($db,$org,$strType)
{
   $ret=-1.0;
   $q="SELECT id FROM aircrafttype WHERE org = ".$org." and name = '" . $strType . "'";
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   mysqli_free_result($r);
   return $ret;
}

function getTowPlaneType($db,$org){return getAircraftType($db,$org,'Tow Plane');}

function getLaunchType($strType)
{
  $launchType= App\Models\LaunchType::where('name', $strType)->first();
  if($launchType) {
    return $launchType->id;
  }

  return -1.0;
}

//TODO remove $db once we completely switch to eloquent
function getTowLaunchType($db){return getLaunchType('Tow Plane');}
function getSelfLaunchType($db){return getLaunchType('Self Launch');}
function getWinchLaunchType($db){return getLaunchType('Winch');}

function getStatusId($db, $strName) {
  $ret=-1.0;
  $q="SELECT id FROM membership_status WHERE status_name='{$strName}'";
  $r = mysqli_query($db,$q);
  if (mysqli_num_rows($r) > 0)
  {
    $row = mysqli_fetch_array($r);
    $ret=$row[0];
  }
  mysqli_free_result($r);
  return $ret;
}
function getActiveStatusId($db){
  return getStatusId($db, 'Active');
}

function getTowChargeType($db,$org_id)
{
  //Returns 0 (Not defined)
  //Returns 1 (Height Based)
  //Returns 2 (Time bases)
  $org = App\Models\Organisation::find($org_id);
  if($org) {
    if($org->tow_height_charging == 1) {
      return 1;
    }

    if($org->tow_time_based) {
      return 2;
    }
  }

  return 0;
}

function getOrganisationName($db,$org)
{
   $ret='';
   $q="SELECT name FROM organisations WHERE id = " . $org;
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   return $ret;
}

function getOrgOtherChargesName($db,$org)
{
   $ret='';
   $q="SELECT name_othercharges FROM organisations WHERE id = " . $org;
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   return $ret;
}

function IsMemberTowy($db,$memid)
{
  $ret=false;
  $roletow = getRoleId($db,'Tow Pilot');
  $q="SELECT * from role_member where member_id = " . $memid . " and role_id = " . $roletow;
  $r = mysqli_query($db,$q);
  if (mysqli_num_rows($r) > 0)
     $ret= true;
  mysqli_free_result($r);
  return $ret;
}

function IsMemberInstructor($db,$memid)
{
  $ret=false;
  $q1="SELECT id from roles where name LIKE '%Instructor%'";
  $r1 = mysqli_query($db,$q1);
  while ($row1 = mysqli_fetch_array($r1) )
  {
     $q="SELECT * from role_member where member_id = " . $memid . " and role_id = " . $row1[0];
     $r = mysqli_query($db,$q);
     if (mysqli_num_rows($r) > 0)
        $ret= true;
     mysqli_free_result($r);
  }
  mysqli_free_result($r1);
  return $ret;
}

function getGliderModel($db,$org,$short_rego)
{
   $ret='';
   $q="SELECT make_model FROM aircraft WHERE org = " .$org. " and rego_short = '" . $short_rego . "'";
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
     $row = mysqli_fetch_array($r);
     $ret=$row[0];
   }
   mysqli_free_result($r);
   return $ret;
}

function strDuration($v)
{
  $duration = intval($v / 1000);
  $hours = intval($duration / 3600);
  $mins = intval(($duration % 3600) / 60);
  $timeval = sprintf("%02d:%02d",$hours,$mins);
  return $timeval;
}

//TODO remove $db1 once we use Eloquent all over the places
function tracksforFlight($db1,$db2,$glider,$strStart,$strEnd)
{
  $tracks = App\Models\Track::where('glider', $glider)
              ->where('point_time', '>' , $strStart)
              ->where('point_time', '<', $strEnd);

  if(!$tracks->get()->isEmpty()) {
    return true;
  }

   if (null != $db2)
   {
    $q = "SELECT * from tracksarchive where glider = '".$glider."' and point_time > '".$strStart."' and point_time < '".$strEnd."'";
    $r = mysqli_query($db2,$q);
    if (mysqli_num_rows($r) > 0)
    {
      $ret = true;
      return $ret;
    }
   }
   return $ret;
}

function isFirstPilotFlightDay($db,$org,$date,$seq,$memberid)
{
  $q = "SELECT id from flights where flights.org = " .$org. " and flights.localdate = ".$date." and flights.seq < ".$seq." and (flights.billing_member1 = ".$memberid." or flights.billing_member2 = ".$memberid.")";
  $r = mysqli_query($db,$q);
  if (!r)
     error_log("SQL ERROR " . mysqli_error($con) . "SQL: " . $q);
  if (mysqli_num_rows($r) > 0)
    return false;
  else
    return true;
}

function SendMail($to,$subject,$message)
{
  $headers =
    'From: Gliding Operations <operations@glidingops.com>' . "\r\n" .
    'Reply-To: wgcoperations@gmail.com' . "\r\n" .
    'Return-PATH: operations@glidingops.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
  return mail($to, $subject, $message, $headers, '-r operations@glidingops.com');
}

function getMemmbersXmlRows($db, $org, $timesheedDate)
{
  $activeStatusID = getActiveStatusId($db);
  $timesheedDateStr = $timesheedDate->format('Ymd');

  $q1 = "SELECT DISTINCT members.* FROM members
          LEFT JOIN flights pic_flight ON pic_flight.pic = members.id
          LEFT JOIN flights p2_flight ON p2_flight.p2 = members.id
          WHERE members.org={$org} AND (
                (status IS NULL OR status = {$activeStatusID}) OR
                (pic_flight.org={$org} AND pic_flight.localdate={$timesheedDateStr}) OR
                (p2_flight.org={$org} AND p2_flight.localdate={$timesheedDateStr}))
          ORDER BY displayname ASC";

  $members="";
  $r1 = mysqli_query($db,$q1);
  while ($row = mysqli_fetch_array($r1) )
  {
    $members .= "<member><id>";
    $members .= $row['id'];
    $members .= "</id><name>";
    $members .= $row['displayname'];
    $members .= "</name></member>";
  }

  return $members;
}
?>