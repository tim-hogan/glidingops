<?php
session_start();
require "./includes/classSecure.php";
require "./includes/classTime.php";
require "./includes/classGlidingDB.php";
$DB = new GlidingDB($devt_environment->getDatabaseParameters());
$tracks_params = ["dbname" => $devt_environment->getkey('TRACKS_DATABASE_NAME'),
                  "username" => $devt_environment->getkey('TRACKS_DATABASE_USER'),
                  "password" => $devt_environment->getkey('TRACKS_DATABASE_PW'),
                  "hostname" => $devt_environment->getkey('TRACKS_DATABASE_HOST')
                  ];
$DBArchive = new TracksDB($tracks_params);

if (! Secure::isSignedIn())
{
    header("Location: Login.php");
    exit();
}

$org = 0;
$organistaion=null;
$user = $DB->getUserWithMember($_SESSION['userid']);
if ($user && isset($user['org']))
{
    if ($organistaion = $DB->getOrganisation($user['org']) )
        $org = $user['org'];
}

if ($org == 0)
{
    header("Location: Login.php");
    exit();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width" />
<meta name="viewport" content="initial-scale=1.0" />
<title>MyFlights</title>
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style><?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<style>
@media print {
    th {font-size: 12px;}
    td {font-size: 12px;}
    h2 {font-size: 14px;}
    #print-button {display: none;}
    #head {display: none;}
    @page {size: landscape;}
}
@media screen {
     th {font-size: 14px;padding-left:10px;}
     td {font-size: 14px;}
     h2 {font-size: 16px;}
}
#head1 {padding-left:10px;}
#flights1 {margin:12px;}
#flights2 {padding: 10px;background-color: #e0e0f0;border-radius: 8px;box-shadow: 10px 10px 5px #888888;}
#summary1 {margin:12px;}
#summary2 {padding: 10px;background-color: #f0f0e0;border-radius: 8px;box-shadow: 10px 10px 5px #888888;}
#charges1 {margin:12px;}
#charges2 {padding: 10px;background-color: #e0f0f0;border-radius: 8px;box-shadow: 10px 10px 5px #888888;}
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
h1 {font-family: Calibri, Arial, Helvetica, sans-serif;}
table {border-collapse: collapse;}
.right {text-align: right;}
.bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
td.cmnt {padding-left: 10px;}
td.loc {padding-left: 10px;}
td.lnk {padding-left: 10px;}
a {text-decoration: none;}
</style>
<script>
function printit(){window.print();}
</script>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<?php
if (intval($_SESSION['memberid']) <= 0)
{
 echo "<p>Error: Your Login Credentials of " . $_SESSION['who'] . " are not linked to a Gliding Member</p>";
}
$DEBUG=0;
$inc = "./orgs/" . $org . "/accountrules.php"; include $inc;
include 'helpers.php';
$totMins=0;
$diagtext="";
$pageid=22;
$pkcol=2;
$pagesortdata = $_SESSION['pagesortdata'];
$colsort = $pagesortdata[$pageid];

$db_params = require('./config/database.php');
$con_params = $db_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
$con2_params = $db_params['tracks'];
$con2=mysqli_connect($con2_params['hostname'],$con2_params['username'],$con2_params['password'],$con2_params['dbname']);
if (mysqli_connect_errno())
{
  error_log("Cannot open tracksarchive database");
  $con2 = null;
}

//Duplicate to above but in transition to newer database scheme
require dirname(__FILE__) . '/includes/classGlidingDB.php';
require dirname(__FILE__) . '/includes/classTracksDB.php';
$db_params = require( dirname(__FILE__) .'/config/database.php');


$towlaunch = $DB->getTowLaunchTypeId();
$selflaunch = $DB->getSelfLaunchTypeId();
$winchlaunch = $DB->getWinchLaunchTypeId();

$flightTypeGlider = $DB->getGlidingFlightTypeId();
$flightTypeCheck = $DB->getCheckFlightTypeId();
$flightTypeRetrieve = $DB->getRetrieveFlightTypeId();


//find the juinor class id
$juniorclass = $DB->getJuniorClassId($org);
$istowy = $DB->IsMemberTowy($user['member']);

$row = $DB->getMemberWithClass($user['member']);
if (!$row)
{
    echo "<p>Error: User logged does not appear to be a member, unable to display flights.</p>";
    exit();
}

/*
$sql="SELECT members.displayname, members.class, a.class from members LEFT JOIN membership_class a ON a.id = members.class where members.id = " .  $_SESSION['memberid'];
$r = mysqli_query($con,$sql);
$row_cnt = mysqli_num_rows($r);
if ($row_cnt <= 0)
{
   echo "<p>Error: No member id available to display flights</p>";
   echo "<p>SQL: ".$sql."</p>";
   exit();
}
$row = mysqli_fetch_array($r);
*/


$iScheme=0;
$iRateGlider=0;
$iChargeTow=1;
$dispname = $row[0];
$memberclass=$row[1];
$strMemberClass=$row[2];
$clubgliders = array();

//Build array of club gliders
$r = $DB->getClubGliders($org);
//$sql="SELECT rego_short FROM aircraft where aircraft.org = ".$org." and club_glider > 0";
//$r = mysqli_query($con,$sql);
//$cnt = 0;
while ($aircraft = $r->fetch_array(MYSQLI_ASSOC) )
    array_push($clubgliders,$aircraft['rego_short']);

?>
<div id='container'>
<div id='head1'>
<h1>My Flights</h1>
<h2><?php echo $dispname;?></h2>
</div>
<div id='flights1'>
<div id='flights2'>
    <?php
$totMins=0;
$totMinsP=0;
$totMinsP1=0;
$totMinsP2=0;
$totMinsI=0;
$cntP=0;
$cntP1=0;
$cntP2=0;
$cntI=0;
$memberInstructor = false;
$memberInstructor = $DB->IsMemberInstructor($user['member']);

$rownum = 0;
$r = $DB->allGliderFlightsForMember($memid);
while ($flight = $r->fetch_array(MYSQLI_ASSOC))

/*
$sql= "SELECT flights.localdate,flights.glider,(flights.land-flights.start),flights.height, flights.pic, flights.p2, flights.comments, flights.launchtype , flights.location ,(flights.start/1000),(flights.land/1000),id FROM flights WHERE flights.type = " .$flightTypeGlider." and (flights.pic=" . $_SESSION['memberid'] .  " OR flights.p2=" .  $_SESSION['memberid'] . ") ORDER BY localdate,seq ASC";


$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
*/


{
    $rownum = $rownum + 1;
    if ($rownum == 1)
    {
        if ($istowy) echo "<h2>Gliding Flights</h2>";
        echo "<table><tr><th>DATE</th><th>GLIDER</th><th>MAKE/MODEL</th><th>LOCATION</th><th>DURATION</th><th>START</th><th>LAND</th><th>TOW HEIGHT</th><th>LAUNCH TYPE</th><th>TYPE</th><th>COMMENTS</th><th>TRACK</th></tr>";
    }



    echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
        $datestr=$flight['localdate'];
        echo "<td>";
            echo substr($datestr,6,2) . "/" . substr($datestr,4,2) . "/" . substr($datestr,0,4);
        echo "</td>";
        echo "<td class='right'>";
            echo $flight['glider'];
        echo "</td>";
        echo "<td class='right'>";
            echo htmlspecialchars($DB->getGliderModel($org,$flight['rego_short']));
        echo "</td>";
        echo "<td class='right'>";
            echo htmlspecialchars($flight['location']);
        echo "</td>";

        $timeval = 'In Progress';
        if ($flight['land'])
        {
            $duration = (($flight['land'] - $flight['start']) / 1000);
            $hours = intval($duration / 3600);
            $mins = intval(($duration % 3600) / 60);
            $timeval = sprintf("%02d:%02d",$hours,$mins);
            $totMins = $totMins + (($hours*60) + $mins);
        }
        echo "<td class='right'>";
            echo $timeval;
        echo "</td>";


        $otherperson=0;
        $type=0;  //1 = P1 2 = p2 3 = PIC Solo


        $start = (new DateTime())->setTimestamp($flight['start'] / 1000);
        $land  = (new DateTime())->setTimestamp($flight['land'] / 1000);

        $nz_timezone = new DateTimeZone("Pacific/Auckland");
        $start->setTimezone($nz_timezone);
        $land->setTimezone($nz_timezone);

        $start_time = ($flight['start'] == 0) ? "" : $start->format('G:i:s');
        $land_time = ($flight['land'] == 0) ? "" : $land->format('G:i:s');

        echo "<td class='right' style='padding-left:5px;'>{$start_time}</td>";
        echo "<td class='right' style='padding-left:5px;'>{$land_time}</td>";

        echo "<td class='right'>";
            if ($flight['launchtype'] == $towlaunch)
                echo $flight['height'];
            elseif ($flight['launchtype'] == $selflaunch)
                echo "SELF LAUNCH";
            elseif ($flight['launchtype'] == $winchlaunch)
                echo "WINCH";
        echo "</td>";

        echo "<td class='right'>";
            if ($flight['launchtype'] == $towlaunch)
                echo "A";
            elseif ($flight['launchtype'] == $selflaunch)
                echo "S";
            elseif ($flight['launchtype'] == $winchlaunch)
                echo "W";
        echo "</td>";

        echo "<td class='right'>";
            if ($flight['pic'] == $user['member'] && ! $flight['p2'])
            {
                echo "P";
                $type = 3;
                $totMinsP = $totMinsP + (($hours*60) + $mins);
                $cntP = $cntP + 1;
            }
            elseif ($flight['pic'] == $user['member'])
            {
                if ($memberInstructor)
                {
                    echo "I";
                    $cntI = $cntI + 1;
                    $totMinsI = $totMinsI + (($hours*60) + $mins);
                }
                else
                {
                    echo "P1";
                    $cntP1 = $cntP1 + 1;
                    $totMinsP1 = $totMinsP1 + (($hours*60) + $mins);
                }
                $otherperson=$flight['p2'];
                $type=1;
            }
            elseif ($flight['p2'] == $user['member'] && ! $flight['pic'] )
            {
                echo "P";
                $type=3;
                $totMinsP = $totMinsP + (($hours*60) + $mins);
                $cntP = $cntP + 1;
            }
            else
            {
                echo "P2";
                $otherperson = $row[4];
                $type=2;
                $totMinsP2 = $totMinsP2 + (($hours*60) + $mins);
                $cntP2 = $cntP2 + 1;
            }
        echo "</td>";

        $comment="";
        if ($type != 3)
        {
            if ($othermember = $DB->getMember($otherperson) )
                $comment .= "Other POB: " . htmlspecialchars($othermember['displayname']) . " ";
        }
        $comment .= htmlspecialchars($flight['comments']);
        echo "<td class='cmnt'>";
            echo $comment;
        echo "</td>";

        //Do we have any tracking data
        $trDateStart = new DateTime();
        $trDateLand = new DateTime();
        $trDateStart->setTimestamp(intval(floor($flight['start'] / 1000)));
        $trDateLand->setTimestamp(intval(floor($flight['land'] / 1000)));

        if ($DB->numTracksForFlight($trDateStart,$trDateLand,$flight['glider']) > 0 || $DBArchive->numTracksForFlight($trDateStart,$trDateLand,$flight['glider']) > 0)
        {
            echo "<td class='lnk'><a href='MyFlightMap.php?glider=".$flight['glider']."&from=".$trDateStart->format('Y-m-d H:i:s')."&to=".$trDateLand->format('Y-m-d H:i:s')."&flightid=".$flight['id']."'>MAP</a></td>";
            echo "<td class='lnk'><a href='OlcFile.igc?flightid=".$flight['id']."'>IGC FILE</a></td>";
        }

    echo "</tr>";
}
if ($rownum > 0)
   echo "</table>";
    ?>

<?php 
$towcnt=0;
if ($istowy) 
{
  $rownum = 0;
  echo "<h2>Tows</h2>";
  echo "<table><tr><th>DATE</th><th>PLANE</th><th>GLIDER</th><th>TOW HEIGHT</th></tr>";
  $sql= "SELECT flights.localdate,a.rego_short,flights.glider,flights.height FROM flights LEFT JOIN aircraft a ON a.id = flights.towplane WHERE flights.towpilot=" . $_SESSION['memberid'] .  " ORDER BY localdate,seq ASC";
  $r = mysqli_query($con,$sql);
  while ($row = mysqli_fetch_array($r) )
  {
    $towcnt = $towcnt + 1;
    $rownum = $rownum + 1;
    echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
    $datestr=$row[0];
    echo "<td>";echo substr($datestr,6,2) . "/" . substr($datestr,4,2) . "/" . substr($datestr,0,4);echo "</td>";
    echo "<td class='right'>" . $row[1] . "</td>";
    echo "<td class='right'>" . $row[2] . "</td>";  
    echo "<td class='right'>" . $row[3] . "</td>";
    echo "</tr>";  
  }
  echo "</table>";
}
?>
</div>
</div>
<div id='summary1'>
<div id='summary2'>
<h2>Flights Summary</h2>
<table><tr>
<th>TYPE</th>
<th>NUMBER</th>
<th>TOTAL TIME</th>
</tr>
<?php 
$timeval = sprintf("%02d:%02d",$totMinsI/60,$totMinsI%60);
echo "<tr><td>I</td><td class='right'>".$cntI."</td><td class='right'>".$timeval."</td></tr>";
$timeval = sprintf("%02d:%02d",$totMinsP/60,$totMinsP%60);
echo "<tr><td>P</td><td class='right'>".$cntP."</td><td class='right'>".$timeval."</td></tr>";
$timeval = sprintf("%02d:%02d",$totMinsP1/60,$totMinsP1%60);
echo "<tr><td>P1</td><td class='right'>".$cntP1."</td><td class='right'>".$timeval."</td></tr>";
$timeval = sprintf("%02d:%02d",$totMinsP2/60,$totMinsP2%60);
echo "<tr><td>P2</td><td class='right'>".$cntP2."</td><td class='right'>".$timeval."</td></tr>";
$timeval = sprintf("%02d:%02d",$totMins/60,$totMins%60);
$totcnt=$cntP+$cntP1+$cntP2;
if ($istowy)
  echo "<tr><td class='bordertop'>TOTAL GLIDING</td>";
else
  echo "<tr><td class='bordertop'>TOTAL</td>";
echo "<td class='bordertop right'>".$totcnt."</td><td class='bordertop right'>".$timeval."</td></tr>";
if ($istowy)
  echo "<tr><td>TOTAL TOWS</td><td class='right'>".$towcnt."</td>";
?>
</table>
</div>
</div>
<div id='charges1'>
<div id='charges2'>
<h2>My Charges</h2>
<table><tr>
<th>DATE</th>
<th>GLIDER</th>
<th>DURATION</th>
<th>TOW HEIGHT</th>
<th>CHARGING</th>
<th>TOW</th>
<th>GLIDER</th>
<th>AIRWAYS</th>
<th>TOTAL</th>
<th>COMMENTS</th>
</tr>
<?php
$sumtow=0.0;
$sumglid=0.0;
$sumairways=0.0;
$sql= "SELECT flights.localdate,flights.glider,(flights.land-flights.start),flights.height, flights.pic, flights.p2, flights.comments, a.name, a.bill_pic , a.bill_p2, a.bill_other , flights.billing_member1, flights.billing_member2 ,b.displayname , c.displayname, flights.launchtype, flights.towplane , flights.location , flights.type , (flights.towland-flights.start) , seq from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b ON b.id = flights.billing_member1 LEFT JOIN members c ON c.id = flights.billing_member2 WHERE flights.billing_member1=" . $_SESSION['memberid'] .  " OR flights.billing_member2=" .  $_SESSION['memberid'] . " ORDER BY localdate,seq ASC";
//0 localdate
//1 glider
//2 land - start
//3 height
//4 pic
//5 p2
//6 comments
//7 billing option name
//8 a.bill pic
//9 a bill p2
//10 a bill other
//11 billing member 1
//12 billing member 2
//13 billing member display name 1
//14 billing member display name 1
//15 launchtype
//16 tow plane
//17 location
//18 type
//19 tow duration
//20 seq


$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
  
  $rownum = $rownum + 1;
  $datestr=$row[0];
  $flightDate = new DateTime();
  $flightDate->setDate(substr($datestr,0,4),substr($datestr,4,2),substr($datestr,6,2));
  
  $iScheme = 0;
  $iRateGlider = 0;
  $iChargeTow=1;
  $iChargeAirways=1;
  $schemename="";
  $comments="";
 
  //Check that this person has an ecentive scheme valid for this date.
  if (MemberScheme($org,$_SESSION['memberid'],$flightDate,$row[1],$iRateGlider,$iChargeTow,$iChargeAirways,$schemename) > 0)
     $iScheme = 1;
  
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
  echo "<td>";echo substr($datestr,6,2) . "/" . substr($datestr,4,2) . "/" . substr($datestr,0,4);echo "</td>";
  echo "<td class='right'>";echo $row[1];echo "</td>";
  
  $duration = intval($row[2] / 1000);
  $hours = intval($duration / 3600);
  $mins = intval(($duration % 3600) / 60);
  $timeval = sprintf("%02d:%02d",$hours,$mins);
  $totMins = (($hours*60) + $mins);
  $otherperson=0;
  $type=0;  //1 = P1 2 = p2 3 = PIC Solo

  echo "<td class='right'>";echo $timeval;echo "</td>";
 
  echo "<td class='right'>";
  
  if ($row[15] == $towlaunch && $row[18]==$flightTypeGlider)
      echo $row[3];
  if ($row[15] == $selflaunch)
     echo "SELF LAUNCH";		 
  if ($row[15] == $winchlaunch)
     echo "WINCH";

  echo "</td>";
  echo "<td>";echo $row[7];echo "</td>";

  //Find tow charge
  //Is this a club glider
  $towcost=0.0;
  $clubGlid=0;
  if (in_array($row[1],$clubgliders))
    $clubGlid=1;
  
  $is5050 = 0;
  //Is this a 50/50
  if ($row[8] > 0 && $row[9] > 0)
     $is5050=1;


  $SchemeCharge = $iScheme;
  //If this person is on a scheme he/she must be PIC or P2
  if ($iScheme)
  {
     if (intval($_SESSION['memberid']) == $row[4] ||
         intval($_SESSION['memberid']) == $row[5])
        $SchemeCharge = 1;
      else
      {
        $SchemeCharge = 0;
	$iChargeAirways=1;
      }
  }

  //Calculate tow charges
  $towcost=0.0;
  if ($row[18]==$flightTypeRetrieve)  
      $towcost = CalcTowRetrieve($org,$row[16],$duration);
  else
  {
    if ($row[15] == $towlaunch)
        $towcost = CalcTowCharge2($org,$row[15],$row[16],$row[19],$row[3],$strMemberClass,$clubGlid,$is5050);
    else
        if ($row[15] == $winchlaunch)
            $towcost = CalcWinchCharge($con,$_SESSION['org'],$row[17],$flightDate);
        
    if ($SchemeCharge > 0 && $iChargeTow == 0)
       $towcost=0.0;  
  }
  echo "<td class='right'>";
  echo "$";
  if ($towcost < 0.0)
    echo "ERROR";
  else
    echo sprintf("%01.2f",$towcost);
  echo "</td>";

  $glidcost=0.0;
  $glidcost=CalcGliderCharge($org,$clubGlid,$row[1],$SchemeCharge,$iRateGlider,$is5050,$totMins,$strMemberClass);   
  echo "<td class='right'>$";
  echo sprintf("%01.2f",$glidcost);
  echo "</td>";

  $airways=0.0;
  $airways = CalcOtherCharges($org,$row[17],$clubGlid,$memberclass,$juniorclass,$flightDate,$is5050,$_SESSION['memberid'],$row[20]);
  if ($iChargeAirways == 0)
     $airways=0.00;
  echo "<td class='right'>$";
  echo sprintf("%01.2f",$airways);
  echo "</td>";       
  


   echo "<td class='right'>$";
   echo sprintf("%01.2f",($towcost+$glidcost+$airways));
   echo "</td>";
   $comments = "";
   if ($is5050)
   {
      $comments = "Charge 50/50 with ";
      if ($row[11] != $_SESSION['memberid'])
          $comments .= $row[13];
      if ($row[12] != $_SESSION['memberid'])
          $comments .= $row[14];
       

   }
   if ($row[18]==$flightTypeRetrieve)
       $comments .= "Charges for retrieve";   
   echo "<td>";
   echo $comments;
   echo "</td>";
   
   $sumtow = $sumtow + $towcost;
   $sumglid = $sumglid + $glidcost;
   $sumairways = $sumairways + $airways;
   echo "</tr>";
}


echo "<tr><td>TOTAL</td><td></td><td></td><td></td><td></td>";
echo "<td class='right bordertop'>$";
echo sprintf("%01.2f",$sumtow);
echo "</td>";
echo "<td class='right bordertop'>$";
echo sprintf("%01.2f",$sumglid);
echo "</td>";
echo "<td class='right bordertop'>$";
echo sprintf("%01.2f",$sumairways);
echo "</td>";
echo "<td class='right bordertop'>$";
echo sprintf("%01.2f",($sumtow+$sumglid+$sumairways));
echo "</td>";
echo "</tr>";
?>
</table>
</div>
</div>
<button onclick='printit()' id='print-button'>Print MyFlights</button>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</div>
</body>
</html>
