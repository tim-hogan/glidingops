<?php session_start(); ?>

<?php
  $con_params = require('./config/database.php');
  $con_params = $con_params['gliding'];
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
function printit(){window.print();}
<?php
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   echo "var strGlider=\"" . $_POST["glider"] . "\";";
   echo "var strFrom=\"" . $_POST["fromdate"] . "\";";
   echo "var strTo=\"" . $_POST["todate"] . "\";";
}
else
{
   echo "var strGlider=\"\";";
   echo "var strFrom=\"" . $dateStr . "\";";
   echo "var strTo=\"" . $dateStr . "\";";
}
?>
function ModForm(){var d = document.getElementById('inform');if (null !=d)d.setAttribute("action","/Engineering.csv");}
function ModForm2(){var d = document.getElementById('inform');if (null !=d)d.setAttribute("action","/Engineer.php");}
function s(){document.getElementById('fmdate').value = strFrom;document.getElementById('todate').value = strTo;
if (strGlider.length != 0)
{
 var y=document.getElementById('glider').childNodes;
 for(x=0;x<y.length;x++)
 {
   if (y[x].value == strGlider)
     y[x].selected=true;
 }
}
}
</script>
</head>
<body onload='s()'>
<?php
if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 32))
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
<?php include __DIR__.'/helpers/dev_mode_banner.php' ?>
<div id='divhdr'>
<form id='inform' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2>Engineers Report</h2>
<table>
<tr><td>Choose Aircraft</td><td>
<select name='glider' id='glider'>
<?php
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
  echo "<p>Unable to connect to database</p>";
  exit();
}
$q="SELECT id,rego_short from aircraft where aircraft.org = ".$_SESSION['org']." order by rego_short";
$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
   echo "<option value='" .$row[1]."'>".$row[1]."</option>";
}
mysqli_close($con);
?>
</select></td>
<tr><td>From:</td><td><input type="date" id='fmdate' name="fromdate" value=""></td></tr>
<tr><td>To:</td><td><input type="date" id='todate' name="todate" value=""></td></tr>
</table>
<input type='hidden' name='org' value='<?php echo $_SESSION['org'];?>'>
<br><input type='submit' name='view' value='View Report' onclick='ModForm2()'>
<button form='inform' type='submit' name='export' onclick='ModForm()'>Export to Excel</button>
</form>
</div>
<?php
$DEBUG=1;
$diagtext="";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

  $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
  if (mysqli_connect_errno())
  {
   echo "<p>Unable to connect to database</p>";
   exit();
  }

  $strDateFrom = $_POST["fromdate"];
  $strDateTo = $_POST["todate"];
  $dateStart2 = substr($strDateFrom,0,4) . substr($strDateFrom,5,2) . substr($strDateFrom,8,2);
  $dateEnd2 = substr($strDateTo,0,4) . substr($strDateTo,5,2) . substr($strDateTo,8,2);

  echo "<h2>Flight Data For " .$_POST['glider'] . "</h2>";

  echo "<table><tr><th>DATE</th><th>FLIGHT DURATION</th><th>LAUNCH TYPE</th></tr>";
  $totalcnt = 0;
  $totaltime = 0;
  $q="SELECT flights.localdate, (flights.land-flights.start) ,a.name from flights LEFT JOIN launchtypes a ON a.id = flights.launchtype where flights.org = ".$_SESSION['org']." and flights.glider = '".$_POST['glider']."' and localdate >= " . $dateStart2 . " and localdate <= " . $dateEnd2 . " order by localdate,seq";
  $r = mysqli_query($con,$q);
  while ($row = mysqli_fetch_array($r) )
  {
  	  $totalcnt = $totalcnt+1;
          $strdate=$row[0];
          echo "<td>";
          echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);
	  echo "</td>";
          $duration = intval($row[1] / 1000);
          $hours = intval($duration / 3600);
          $mins = intval(($duration % 3600) / 60);
          $totMins = (($hours*60) + $mins);
          $totaltime = $totaltime + $totMins;

  	  $timeval = sprintf("%02d:%02d",$hours,$mins);
          echo "<td class='right'>";
	  echo $timeval;

          echo "</td>";
          echo "<td>" . $row[2] . "</td>";
          echo "</tr>";
  }
  echo "<tr><td>Total</td>";
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
