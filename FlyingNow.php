<?php session_start(); ?>
<?php
$org=0;
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if (isset($_GET['org']) )
 {
   $org=$_GET['org'];
   if ($org < 1)
   {
     die("Error You must supply and organisation number");
   }
 }
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading1.css"; include $inc; ?></style>
<meta http-equiv="refresh" content="30">
<link rel="stylesheet" type="text/css" href="heading1.css">
<style>
h2.rev{background-color:#000040;color:white;}
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
table {border-collapse: collapse;}
tr.tr1 {height:20px;}
th {font-size: 18px;padding-left: 15px;padding-right: 18px;}
td {font-size: 18px;padding-left: 15px;padding-right: 15px;}
td.b {font-size: 22px;padding-left: 15px;padding-right: 15px;font-weight:bold;}
h1 {font-size: 26px;}
h2 {font-size: 24px;}
.right {text-align: right;}
.bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
</style>
<script>
function printit(){window.print();}
</script>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading1.txt"; include $inc; ?>
<?php
include 'helpers.php';
include 'timehelpers.php';
$DEBUG=0;
$diagtext="";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}


$dateTimeZone = new DateTimeZone(orgTimezone($con,$org));
$dateTime = new DateTime("now", $dateTimeZone);
$dateStr = $dateTime->format('Ymd');
$dateStr2 = $dateTime->format('Y-m-d');
$flightTypeGlider = getGlidingFlightType($con); 
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if (isset($_GET['date']) )
 {
 	if($_GET['date'] != "" )
 	{
  	$dateStr=$_GET['date'];
  	$dateTime->setDate ( substr($dateStr,0,4), substr($dateStr,4,2), substr($dateStr,6,2) );
 	}
 }
}
?>

<table>
<?php
$dateTimeNow = new DateTime("now");
$sql= "SELECT a.name , b.displayname from duty LEFT JOIN dutytypes a ON a.id = duty.type LEFT JOIN members b ON b.id = duty.member where duty.org = ".$org." and duty.localdate = '" .$dateStr2. "'";

$r = mysqli_query($con,$sql);
while ($row = mysqli_fetch_array($r) )
{
    echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td></td><td></td><td></td></tr>";
}
echo "<tr class='tr1'><td colspan = '5' class='b'></td></tr>";
echo "<tr><td colspan = '5' class='b'>FLYING NOW</td></tr>";


$sql= "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.start/1000) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".$org." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 and flights.land=0 order by flights.seq ASC";
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
  $rownum = $rownum + 1;
  if ($rownum == 1)
  {
    echo "<tr><th>SEQ</th><th>GLIDER</th><th>PIC</th><th>P2</th><th>ELAPSED TIME</th></tr>";
  }

  
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
  echo "<td>";echo $row[0];echo "</td>";
  echo "<td>";echo $row[1];echo "</td>";
  echo "<td>";echo $row[2];echo "</td>";
  echo "<td>";echo $row[3];echo "</td>";
  
  $elapsed = $dateTimeNow->getTimestamp() - $row[4];

  $hours = intval($elapsed / 3600);
  $mins = intval(($elapsed % 3600) / 60);
  $timeval = sprintf("%02d:%02d",$hours,$mins);    

  echo "<td class='right'>"; echo $timeval; echo "</td>";
  echo "</tr>";
}
if ($rownum==0)
 echo "<tr><td></td><td colspan = '4'>None</td></tr>";
echo "<tr><td colspan = '5' class='b'>COMPLETED TODAY</td></tr>";
$sql= "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.land-flights.start) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".$org." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 and flights.land>0 order by flights.seq ASC";
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
  $rownum = $rownum + 1;
  if ($rownum == 1)
  {
    echo "<tr><th>SEQ</th><th>GLIDER</th><th>PIC</th><th>P2</th><th>FLIGHT TIME</th></tr>";
  }
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
  echo "<td>";echo $row[0];echo "</td>";
  echo "<td>";echo $row[1];echo "</td>";
  echo "<td>";echo $row[2];echo "</td>";
  echo "<td>";echo $row[3];echo "</td>";
  
  $elapsed = $row[4]/1000;

  $hours = intval($elapsed / 3600);
  $mins = intval(($elapsed % 3600) / 60);
  $timeval = sprintf("%02d:%02d",$hours,$mins);    

  echo "<td class='right'>"; echo $timeval; echo "</td>";
  echo "</tr>";
}
if ($rownum==0)
 echo "<tr><td></td><td colspan = '4'>None</td></tr>";
?>
</table>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
<?php mysqli_close($con);?>
</body>
</html>
