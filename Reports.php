<?php session_start(); ?>
<?php
if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 1))
 {
  die("Secruity level too low for this page");
 }
}
else
{
 header('Location: Login.php');
 die("Please logon");
}
?>
<?php
include 'helpers.php';
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
tr.blank {height: 20px;}
td.right {text-align: right;}
</style>
<script>
function printit(){window.print();}
</script>
<?php
$org = 0;
if(isset($_SESSION['security']))
    $org = $_SESSION['org'];
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
?>
</head>
<body>
<h2>Statistics</h2>
<div id='divhdr'>
<form id='inform' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2>Select Year</h2>
<select name='year' id='yrs'>
<option value='2014'>2014</option>
<option value='2015'>2015</option>
<option value='2016'>2016</option>
<option value='2017'>2017</option>
</select>
<input type='hidden' name='org' value='<?php echo $_SESSION['org'];?>'>
<br><input type='submit' name='view' value='View Report'>
</form>
</div>
<?php
function IsClubGlider($db,$rego)
{
 $ret = false;
 $q1 = "SELECT club_glider from aircraft where rego_short = '".$rego."'";
 $r1 = mysqli_query($db,$q1);
 if (mysqli_num_rows($r1) > 0)
 {
    $row = mysqli_fetch_array($r1);
    if ($row[0] == 1)
      $ret = true;
 }
 return $ret;
}

function IsTwin($db,$rego)
{
 $ret = false;
 $q1 = "SELECT seats from aircraft where rego_short = '".$rego."'";
 $r1 = mysqli_query($db,$q1);
 if (mysqli_num_rows($r1) > 0)
 {
    $row = mysqli_fetch_array($r1);
    if ($row[0] > 1)
      $ret = true;
 }
 return $ret;
}

function IsMemberInstructor2($db,$member)
{
 $ret = false;
 $q1 = "SELECT a.name from role_member LEFT JOIN roles a ON a.id = role_member.role_id where role_member.member_id = '".$member."'";
 $r1 = mysqli_query($db,$q1);
 if (mysqli_num_rows($r1) > 0)
 {
    while ($row = mysqli_fetch_array($r1) )
    {
    	if (strpos($row[0],'Instructor') !== false) 
      	   $ret = true;
    }
 }
 return $ret;
}

function percy($d1,$d2)
{
 return sprintf("%2.1f%%",($d1*100/$d2));
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{


$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
  echo "<p>Unable to connect to database</p>";
  exit();
}
$thisyear=$_POST["year"];
$dt1=$thisyear*10000;
$dt2=($thisyear+1)*10000;
$flighttype = array();
$totFlights=0;
$totSolo=0;
$totDual=0;
$totClubGlider=0;
$totPrivGlider=0;
$totSoloInTwin=0;
$totTrial=0;
$totInstructorFlight=0;

echo "<h3>Flights ".$thisyear."</h3>";

$q="SELECT flights.id ,flights.type , a.name, flights.pic ,flights.p2, flights.glider, b.name from flights LEFT JOIN flighttypes a ON a.id = flights.type LEFT JOIN billingoptions b ON b.id = flights.billing_option where flights.org = ".$org." and flights.localdate > ".$dt1." and flights.localdate < ".$dt2;
// Row Field
// 0 id
// 1 type
// 2 type name
// 3 pic
// 4 p2
// 5 glider
// 6 billing option name

$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
  $totFlights += 1;
  $flighttype[$row[1]] += 1;
  if ($row[2] ==  "Glider")
  {
      if ($row[3] > 0 && $row[4] == null)
      {
         $totSolo += 1;
         if (IsTwin($con,$row[5]))
            $totSoloInTwin += 1;
      }
      if ($row[3] > 0 && $row[4] > 0)
         $totDual += 1;

      if (IsClubGlider($con,$row[5]))
         $totClubGlider += 1;
      else
         $totPrivGlider += 1;
      if (strpos($row[6],'Trial') !== false) 
      {
          $totTrial += 1;
      }
      else
      {
           if (IsMemberInstructor2($con,$row[3]) )
           {
               if ($row[4] > 0)
                   $totInstructorFlight += 1;
           }
      }
  } 	
}

echo "<table>";
$q="SELECT id, name from flighttypes";
$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
  echo "<tr><td>";
  echo $row[1];
  echo "</td><td>";
  echo $flighttype[$row[0]];
  echo "</td></tr>";
}
echo "<tr><td>Total Flights</td><td>".$totFlights."</td></tr>";
echo "<tr class='blank'><td></td><td></td></tr>";
echo "<tr><td>Solo Flights</td><td>".$totSolo."</td><td>".percy($totSolo,$totFlights)."</td></tr>";
echo "<tr><td>Dual Flights</td><td>".$totDual."</td><td>".percy($totDual,$totFlights)."</td></tr>";
echo "<tr><td>Solo flights in Twin</td><td>".$totSoloInTwin."</td><td>".percy($totSoloInTwin,$totFlights)."</td></tr>";
echo "<tr class='blank'><td></td><td></td></tr>";
echo "<tr><td>Club Gliders</td><td>".$totClubGlider."</td><td>".percy($totClubGlider,$totFlights)."</td></tr>";
echo "<tr><td>Private Gliders</td><td>".$totPrivGlider."</td><td>".percy($totPrivGlider,$totFlights)."</td></tr>";
echo "<tr class='blank'><td></td><td></td></tr>";
echo "<tr><td>Trial Flights</td><td>".$totTrial."</td><td>".percy($totTrial,$totFlights)."</td></tr>";
echo "<tr><td>Instructional flights excluding Trials</td><td>".$totInstructorFlight."</td><td>".percy($totInstructorFlight,$totFlights)."</td></tr>";

echo "</table>";
echo "<h3>Glider Flights Daily Detail ".$thisyear."</h3>";
echo "<table>";
echo "<tr><th>Date</th><th>Total Flights</th><th>Solo Flights</th><th>Dual Flights</th><th>Solo In Twin</th><th>Club Gliders</th><th>Private Gliders</th><th>Trials</th><th>Instructional</th></tr>";
$q="SELECT flights.id ,flights.type , a.name, flights.pic ,flights.p2, flights.glider, b.name, flights.localdate from flights LEFT JOIN flighttypes a ON a.id = flights.type LEFT JOIN billingoptions b ON b.id = flights.billing_option where flights.org = ".$org." and flights.localdate > ".$dt1." and flights.localdate < ".$dt2." order by flights.localdate";
// Row Field
// 0 id
// 1 type
// 2 type name
// 3 pic
// 4 p2
// 5 glider
// 6 billing option name
// 7 Localdate

$saveDate = 0;
$doneRow = 0;
$totFlights = 0;
$totSolo = 0;
$totSoloInTwin = 0;
$totDual = 0;
$totClubGlider=0;
$totPrivGlider=0;
$totTrial=0;
$totInstructorFlight=0;
$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
   if ($saveDate != $row[7])
   {
      //Write out the stats
      if ($doneRow)
      {
          $dtfmt= substr($saveDate,6,2) . "/" . substr($saveDate,4,2) . "/" . substr($saveDate,0,4);
          echo "<td>".$dtfmt."</td><td class='right'>".$totFlights."</td><td class='right'>".$totSolo."</td><td class='right'>".$totDual."</td><td class='right'>".$totSoloInTwin."</td><td class='right'>".$totClubGlider."</td><td class='right'>".$totPrivGlider."</td><td class='right'>".$totTrial."</td><td class='right'>".$totInstructorFlight."</td>";
	  $totFlights = 0;
	  $totSolo = 0;
	  $totSoloInTwin = 0;
          $totDual=0;
	  $totClubGlider=0;
	  $totPrivGlider=0;
	  $totTrial=0;
	  $totInstructorFlight=0;
      }
      $saveDate = $row[7];
      if ($doneRow > 0)
          echo "</tr>";
      echo "<tr>";
      $doneRow = 1;
   }
  if ($row[2] ==  "Glider")
  {
      $totFlights += 1;
      if ($row[3] > 0 && $row[4] == null)
      {
         $totSolo += 1;
         if (IsTwin($con,$row[5]))
            $totSoloInTwin += 1;
      }
      if ($row[3] > 0 && $row[4] > 0)
         $totDual += 1;
      if (IsClubGlider($con,$row[5]))
         $totClubGlider += 1;
      else
         $totPrivGlider += 1;
      if (strpos($row[6],'Trial') !== false) 
      {
          $totTrial += 1;
      }
      else
      {
           if (IsMemberInstructor2($con,$row[3]) )
           {
               if ($row[4] > 0)
                   $totInstructorFlight += 1;
           }
      }
  }
}
if ($doneRow > 0)
{
    $dtfmt= substr($saveDate,6,2) . "/" . substr($saveDate,4,2) . "/" . substr($saveDate,0,4);
    echo "<td>".$dtfmt."</td><td class='right'>".$totFlights."</td><td class='right'>".$totSolo."</td><td class='right'>".$totDual."</td><td class='right'>".$totSoloInTwin."</td><td class='right'>".$totClubGlider."</td><td class='right'>".$totPrivGlider."</td><td class='right'>".$totTrial."</td><td class='right'>".$totInstructorFlight."</td>";
    echo "</tr>";
}
echo "</table>";
$stClass = getShortTermClass($con,$org);
$glidType = getGlidingFlightType($con);
echo "<h3>Member Flights ".$thisyear."</h3>";
echo "<table>";

$q = "SELECT id, displayname from members where org = ".$org." and class <> ".$stClass." order by surname";
$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
    $cnt = 0;
    $q1 = "SELECT id from flights where org = ".$org." and (pic = ".$row[0]." or p2 = ".$row[0].") and type = ".$glidType." and flights.localdate > ".$dt1." and flights.localdate < ".$dt2." order by localdate";
    $r1 = mysqli_query($con,$q1);
    while ($row1 = mysqli_fetch_array($r1))
    {
        $cnt += 1;
    }
    if ($cnt > 0)
        echo "<tr><td>".$row[1]."</td><td>".$cnt."</td></tr>";
}
echo "<table>";
mysqli_close($con);
}
?>
</body>
</html>
