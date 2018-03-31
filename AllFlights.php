<?php
  session_start();

  if(isset($_SESSION['security'])){
    if (!($_SESSION['security'] & 4)){
      die("Secruity level too low for this page");
    }
  } else {
    header('Location: Login.php');
    die("Please logon");
  }

  require_once 'load_model.php';

  include 'helpers.php';
  include 'timehelpers.php';

  $org=0;
  if (isset($_SESSION['org']))
  {
    $org = $_SESSION['org'];
  }
  $dateTimeZone = new DateTimeZone($_SESSION['timezone']);
  $dateTime = new DateTime('now', $dateTimeZone);
  $dateStr = $dateTime->format('Y-m-d');
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    $js_strFrom  = $_POST["fromdate"];
    $js_strTo    = $_POST["todate"];
  }
  else
  {
    $js_strFrom  = $dateStr;
    $js_strTo    = $dateStr;
  }

  $strDateFrom = $_POST["fromdate"];
  $strDateTo = $_POST["todate"];

  $dateStart2 = substr($strDateFrom,0,4) . substr($strDateFrom,5,2) . substr($strDateFrom,8,2);
  $dateEnd2 = substr($strDateTo,0,4) . substr($strDateTo,5,2) . substr($strDateTo,8,2);

  $flights = App\Models\Flight::with(['picMember', 'p2Member', 'towPilotMember'])
                        ->where('org', $_SESSION['org'])
                        ->where('localdate', '>=', $dateStart2)
                        ->where('localdate', '<=', $dateEnd2)
                        ->orderBy('localdate')
                        ->orderBy('seq');
?>

<!DOCTYPE HTML>
<html>
  <meta name="viewport" content="width=device-width">
  <meta name="viewport" content="initial-scale=1.0">
  <head>
    <style>
      @media print {
          th {font-size: 10px;padding-left: 1em;}
          td {font-size: 10px;padding-left: 1em;}
          th.thname {font-size: 12px;padding-left: 0em;text-align: left;}
          h1 {font-size: 18px;}
          h2 {font-size: 16px;}
          h3 {font-size: 14px;}
          .indent1 {padding-left: 2em;}
          #print-button {display: none;}
          #divhdr {display: none;}
          @page {size: landscape;}
      }
      @media screen {
           th {font-size: 12px;padding-left: 20px;}
           td {font-size: 12px;padding-left: 20px;}
           th.thname {font-size: 14px;padding-left: 0px;text-align: left;}
           h1 {font-size: 20px;}
           h2 {font-size: 18px;}
           h3 {font-size: 16px;}
           .indent1 {padding-left: 20px;}
      }
      body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
      table {border-collapse: collapse;}
      .right {text-align: right;}
      .left {text-align: left;}
      .bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
    </style>
    <script>
      function printit(){
        window.print();
      }

      var strFrom = "<?=$js_strFrom?>";
      var strTo   = "<?=$js_strTo?>";

      function ModForm2(){
        var d = document.getElementById('inform');
        if (null !=d) {
          d.setAttribute("action","/AllFlights.php");
        }
      }

      function s(){
        document.getElementById('fmdate').value = strFrom;
        document.getElementById('todate').value = strTo;
      }
    </script>
  </head>

  <body onload='s()'>
    <?php include __DIR__.'/helpers/dev_mode_banner.php' ?>
    <!-- Header -->
    <div id='divhdr'>
      <form id='inform' method="post" action="<?=htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <h2>All Flights Report</h2>
        <table>
          <tr><td>From:</td><td><input type="date" id='fmdate' name="fromdate" value=""></td></tr>
          <tr><td>To:</td><td><input type="date" id='todate' name="todate" value=""></td></tr>
        </table>
        <br>
        <input type='submit' name='view' value='View Report' onclick='ModForm2()'>
      </form>
    </div>

<?php
$DEBUG=1;
$diagtext="";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $db_params = require('./config/database.php');
  $con2_params = $db_params['tracks'];
  $con2=mysqli_connect($con2_params['hostname'],$con2_params['username'],$con2_params['password'],$con2_params['dbname']);
  if (mysqli_connect_errno()) {
    error_log("Cannot open tracksarchive database");
    $con2 = null;
  }

  $strTz              = orgTimezone(null, $_SESSION['org']);
  $towlaunch          = getTowLaunchType(null);
  $flightTypeGlider   = getGlidingFlightType(null);
  $flightTypeCheck    = getCheckFlightType(null);
  $flightTypeRetrieve = getRetrieveFlightType(null);
  $TowChargeType      = getTowChargeType(null,$org);

  echo "<h2>Flights</h2>";

  echo "<table><tr><th>DATE</th><th>SEQ</th><th>LOCATION</th><th>LAUNCH TYPE</th><th>TOW</th><th>GLIDER</th><th>TOWY</th><th>PIC</th><th>P2</th><th>TAKE OFF</th>";
  if ($TowChargeType==2)
      echo "<th>TOW LAND</th>";
  echo "<th>LAND</th>";

  if ($TowChargeType==2)
      echo "<th>TOW DURATION</th>";
  echo "<th>DURATION</th>";

  if ($TowChargeType==1)
      echo "<th>HEIGHT</th>";
  echo "<th>CHARGE</th><th>COMMENTS</th></tr>";
  $totalcnt = 0;
  $totaltime = 0;
  $totaltowtime = 0;

  foreach ($flights->get() as $flight)
  {
    $totalcnt = $totalcnt+1;
    $strdate=$flight->localdate;
    $trDateStart = new DateTime();
    $trDateLand = new DateTime();
    $trDateTowLand = new DateTime();
    $trDateStart->setTimestamp(intval(floor($flight->start/1000)));
    $trDateLand->setTimestamp(intval(floor($flight->land/1000)));
    $trDateTowLand->setTimestamp(intval(floor($flight->towland/1000)));

    echo "<td>";
    echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);
    echo "</td>";
    echo "<td class='right'>" . $flight->seq . "</td>";
    echo "<td>" . $flight->location . "</td>";
    echo "<td>". $flight->launchType->name . "</td>";
    echo "<td class='right'>" . $flight->towPlane->rego_short . "</td>";
    echo "<td class='right'>" . $flight->glider . "</td>";
    echo "<td class='right'>" . $flight->towPilotMember->displayname . "</td>";
    echo "<td class='right'>" . $flight->picMember->displayname . "</td>";
    echo "<td class='right'>" . $flight->p2Member->displayname . "</td>";

    echo "<td class='right'>" .timeLocalFormat($trDateStart,$strTz,'H:i') . "</td>";
    if ($TowChargeType==2) {
      $towLand = ($flight->towland)/1000;
      if ( $towLand > 0) {
        echo "<td class='right'>" .timeLocalFormat($trDateTowLand,$strTz,'H:i') . "</td>";
      } else {
        echo "<td></td>";
      }
    }
    echo "<td class='right'>" .timeLocalFormat($trDateLand,$strTz,'H:i') . "</td>";

    if ($TowChargeType==2) {
      $towDuration = ($flight->towland -$flight->start);
      if (intval($towDuration > 0) ) {
        echo "<td class='right'>" . strDuration($towDuration) . "</td>";
        $totaltowtime = $totaltowtime + (intval($towDuration / 1000)) / 60;
      } else {
        echo "<td></td>";
      }
    }
    $flightDuration = ($flight->land - $flight->start);
    echo "<td class='right'>" . strDuration($flightDuration) . "</td>";

    $totaltime = $totaltime + (intval($flightDuration / 1000)) / 60;
    if ($TowChargeType==1){
      if ($flight->launchtype == $towlaunch && $flight->type == $flightTypeGlider) {
        echo "<td class='right'>" . $flight->height . "</td>";
      } else {
        echo "<td></td>";
      }
      echo "<td class='right'>" . $flight->billingoption->name . "</td>";
    }

    $comments = $flight->comments;
    if ($flight->type == $flightTypeCheck) {
      if (strlen($comments) > 0 ) {
        $comments .= " ";
      }
      $comments .= "Tow plane check flight";
    }

    if ($flight->type == $flightTypeRetrieve) {
      if (strlen($comments) > 0 ) {
        $comments .= " ";
      }
      $comments .= "Retrieve Flight";
    }
    echo "<td>" . $comments . "</td>";

    $trStart = new DateTime();
    $trLand = new DateTime();
    $trStart->setTimestamp(intval(floor($flight->start/1000)));
    $trLand->setTimestamp(intval(floor($flight->land/1000)));



    //$q4 = "SELECT * from tracks where glider = '".$row[3]."' and point_time > '".$trStart->format('Y-m-d H:i:s')."' and point_time < '".$trLand->format('Y-m-d H:i:s')."'";
    //$r4 = mysqli_query($con,$q4);
    //if (mysqli_num_rows($r4) > 0)
    $glider = $flight->glider;
    if (tracksforFlight(null,$con2,$glider,$trStart->format('Y-m-d H:i:s'),$trLand->format('Y-m-d H:i:s')) )
    {
      echo "<td><a href='MyFlightMap.php?glider=".$glider."&from=".$trStart->format('Y-m-d H:i:s')."&to=".$trLand->format('Y-m-d H:i:s')."&flightid=".$flight->id."'>MAP</a></td>";
    }

    echo "</tr>";
  }

  echo "<tr><td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
  if ($TowChargeType==2)
  {
    echo "<td></td>";
        $timeval = sprintf("%d:%02d",($totaltowtime/60),($totaltowtime%60));
        echo "<td class='right'>";
        echo $timeval;
        echo "</td>";
  }
  $timeval = sprintf("%d:%02d",($totaltime/60),($totaltime%60));
  echo "<td class='right'>";
  echo $timeval;
  echo "</td></tr>";
  echo "<tr><td>Count</td>";
  echo "<td class='right'>";
  echo $totalcnt;
  echo "</td></tr>";
  echo "</table>";

  echo "<button onclick='printit()' id='print-button'>Print Report</button>";
}
?>

<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
  </body>
</html>