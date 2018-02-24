<?php session_start(); ?>
<?php
include 'helpers.php';
include 'timehelpers.php';

if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 4))
 {
  die("Secruity level too low for this page");
 }
}
else
{
 header('Location: Login.php');
 die("Please logon");
}

// decide what organization are we talking about
$org=0;
if (isset($_SESSION['org']))
{
  $org = $_SESSION['org'];
}

// initialize database connection parameters
$db_params = require('./config/database.php');
$con_params = $db_params['gliding'];
$con2_params = $db_params['tracks'];

$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
  http_response_code(500);
  exit('Could not connect to database');
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
  $strDateFrom = $_POST["fromdate"];
  $strDateTo = $_POST["todate"];
  $strMember = $_POST["member"];
}

// utility functions definitions
function renderMemberOptions($connection, $org, $currentMemberId) {
  $activeStatusID = getActiveStatusId($connection);
  $q = "SELECT id, displayname FROM members WHERE members.org = {$org} AND members.status = {$activeStatusID} ORDER BY displayname";
  $result = mysqli_query($connection,$q);
  echo("<option value=''>--</option>");
  while ($row = mysqli_fetch_array($result) ){
    $id = $row[0];
    $displayname = $row[1];
    $selected = ($currentMemberId == $id) ? 'selected' : '';
    echo "<option value='{$id}' {$selected}>{$displayname}</option>";
  }
}
?>

<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.js-select2').select2();
    });
  </script>
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
function printit(){window.print();}
<?php
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   echo "var strFrom=\"" . $_POST["fromdate"] . "\";";
   echo "var strTo=\"" . $_POST["todate"] . "\";";
}
else
{
   echo "var strFrom=\"" . $dateStr . "\";";
   echo "var strTo=\"" . $dateStr . "\";";
}
?>
function ModForm2(){var d = document.getElementById('inform');if (null !=d)d.setAttribute("action","/AllFlights.php");}
function s(){document.getElementById('fmdate').value = strFrom;document.getElementById('todate').value = strTo;}
</script>
</head>
<body onload='s()'>
<div id='divhdr'>
<form id='inform' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2>All Flights Report</h2>
<table>
<tr><td>From:</td><td><input type="date" id='fmdate' name="fromdate" value=""></td></tr>
<tr><td>To:</td><td><input type="date" id='todate' name="todate" value=""></td></tr>
<tr>
  <td>Member:</td>
  <td>
    <select class='js-select2' name='member'>
      <?php renderMemberOptions($con, $org, $strMember) ?>
    </select>
  </td>
</tr>
</table>
<br><input type='submit' name='view' value='View Report' onclick='ModForm2()'>
</form>
</div>
<?php
$DEBUG=1;
$diagtext="";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $con2=mysqli_connect($con2_params['hostname'],$con2_params['username'],$con2_params['password'],$con2_params['dbname']);
  if (mysqli_connect_errno())
  {
    error_log("Cannot open tracksarchive database");
    $con2 = null;
  }

  $strTz = orgTimezone($con,$_SESSION['org']);
  $towlaunch = getTowLaunchType($con);
  $flightTypeGlider = getGlidingFlightType($con);
  $flightTypeCheck = getCheckFlightType($con);
  $flightTypeRetrieve = getRetrieveFlightType($con);
  $TowChargeType = getTowChargeType($con,$org);

  $dateStart2 = substr($strDateFrom,0,4) . substr($strDateFrom,5,2) . substr($strDateFrom,8,2);
  $dateEnd2 = substr($strDateTo,0,4) . substr($strDateTo,5,2) . substr($strDateTo,8,2);

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
  $q="SELECT flights.localdate, flights.seq, e.rego_short, flights.glider, a.displayname , b.displayname , c.displayname ,(flights.land-flights.start), flights.height, d.name ,flights.comments, f.name, flights.launchtype, (flights.start/1000), (flights.land/1000), flights.id , flights.type , flights.location, (flights.towland/1000) , (flights.towland-flights.start) from flights LEFT JOIN members a ON a.id = flights.towpilot LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 LEFT JOIN billingoptions d ON d.id = flights.billing_option LEFT JOIN aircraft e ON e.id = flights.towplane LEFT JOIN launchtypes f ON f.id = flights.launchtype where flights.org = ".$_SESSION['org']." and localdate >= " . $dateStart2 . " and localdate <= " . $dateEnd2;
  if(!empty($strMember)){
    $q = $q." and (pic = {$strMember} or p2 = {$strMember})";
  }
  $q = $q." order by localdate,seq";
  //0 - localdate
  //1 - seq
  //2 - rego
  //3 - glider
  //4 - TOWY
  //5 - PIC
  //6 - p2
  //7 duration
  //8 height
  //9 billing options
  //10 comments
  //11 launch type name
  //12 launch type number
  //13 start
  //14 land
  //15 id
  //16 type
  //17 location
  //18 tow land
  //19 tow duration

  $r = mysqli_query($con,$q);
  while ($row = mysqli_fetch_array($r) )
  {
  	  $totalcnt = $totalcnt+1;
          $strdate=$row[0];
  	  $trDateStart = new DateTime();
  	  $trDateLand = new DateTime();
          $trDateTowLand = new DateTime();
  	  $trDateStart->setTimestamp(intval(floor($row[13])));
  	  $trDateLand->setTimestamp(intval(floor($row[14])));
	  $trDateTowLand->setTimestamp(intval(floor($row[18])));

          echo "<td>";
          echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);
	  echo "</td>";
          echo "<td class='right'>" . $row[1] . "</td>";
	  echo "<td>" . $row[17] . "</td>";
	  echo "<td>". $row[11] . "</td>";
          echo "<td class='right'>" . $row[2] . "</td>";
	  echo "<td class='right'>" . $row[3] . "</td>";
	  echo "<td class='right'>" . $row[4] . "</td>";
	  echo "<td class='right'>" . $row[5] . "</td>";
	  echo "<td class='right'>" . $row[6] . "</td>";

          echo "<td class='right'>" .timeLocalFormat($trDateStart,$strTz,'H:i') . "</td>";
  	  if ($TowChargeType==2)
          {
             if ($row[18] > 0)
                 echo "<td class='right'>" .timeLocalFormat($trDateTowLand,$strTz,'H:i') . "</td>";
             else
                 echo "<td></td>";
          }
          echo "<td class='right'>" .timeLocalFormat($trDateLand,$strTz,'H:i') . "</td>";

	  if ($TowChargeType==2)
          {
          	if (intval($row[19] > 0) )
                {
                   echo "<td class='right'>" . strDuration($row[19]) . "</td>";
		   $totaltowtime = $totaltowtime + (intval($row[19] / 1000)) / 60;
                }
                else
                   echo "<td></td>";
          }
          echo "<td class='right'>" . strDuration($row[7]) . "</td>";

          $totaltime = $totaltime + (intval($row[7] / 1000)) / 60;

          if ($TowChargeType==1)
          {
            if ($row[12] == $towlaunch && $row[16] == $flightTypeGlider)
              echo "<td class='right'>" . $row[8] . "</td>";
            else
              echo "<td></td>";
	    echo "<td class='right'>" . $row[9] . "</td>";
          }

          $comments = $row[10];
          if ($row[16] == $flightTypeCheck)
          {
             if (strlen($comments) > 0 )
                $comments .= " ";
             $comments .= "Tow plane check flight";
          }
          if ($row[16] == $flightTypeRetrieve)
          {
             if (strlen($comments) > 0 )
                $comments .= " ";
             $comments .= "Retrieve Flight";
          }
           echo "<td>" . $comments . "</td>";

  	  $trStart = new DateTime();
  	  $trLand = new DateTime();
  	  $trStart->setTimestamp(intval(floor($row[13])));
  	  $trLand->setTimestamp(intval(floor($row[14])));



          //$q4 = "SELECT * from tracks where glider = '".$row[3]."' and point_time > '".$trStart->format('Y-m-d H:i:s')."' and point_time < '".$trLand->format('Y-m-d H:i:s')."'";
          //$r4 = mysqli_query($con,$q4);
          //if (mysqli_num_rows($r4) > 0)
          if (tracksforFlight($con,$con2,$row[3],$trStart->format('Y-m-d H:i:s'),$trLand->format('Y-m-d H:i:s')) )
          {
                echo "<td><a href='MyFlightMap.php?glider=".$row[3]."&from=".$trStart->format('Y-m-d H:i:s')."&to=".$trLand->format('Y-m-d H:i:s')."&flightid=".$row[15]."'>MAP</a></td>";
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

  mysqli_close($con);
}
?>

<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
