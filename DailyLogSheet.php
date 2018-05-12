<?php session_start(); ?>
<?php
include 'helpers.php';
include 'timehelpers.php';
$org=0;
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
    echo "<p>Unable to connect to database</p>";
    exit();
}
$DEBUG=0;
$diagtext="";
$flightTypeGlider = getGlidingFlightType($con);
$flightTypeCheck = getCheckFlightType($con);
$flightTypeRetrieve = getRetrieveFlightType($con);

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
   $org=$_GET['org'];
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 $org = $_POST['org'];
}
if ($org == 0)
{
   echo "<p>Error: No organisation specified</p>";
   exit();
}
$dateTimeZone = new DateTimeZone(orgTimezone($con,$org));
$dateTime = new DateTime("now", $dateTimeZone);
$dateStr = $dateTime->format('Ymd');
$dateStr2 = $dateTime->format('Y-m-d');
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style>
@media print {
    #print-button {display: none;}
    #divform {display: none;}
    #head {display: none;}
    @page {size: landscape;}
}

body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
table {border-collapse: collapse;}
th {font-size: 14px;padding-left: 5px;padding-right: 8px;}
td {font-size: 14px;border-style: dotted;border-color:#404040;border-width: 1px;padding-left: 5px;padding-right: 5px;}
td.n {font-size: 14px; border-style: none;}
h1 {font-size: 16px;}
h2 {font-size: 14px;}
.right {text-align: right;}
.bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
</style>
<script>
function printit(){window.print();}
</script>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<div id='divform'>
<form id='inform' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<tr><td class='n'>Enter Date:</td><td class='n'><input type="date" id='fmdate' name="date" value=<?php echo "'" . $dateStr2 . "'";?>></td></tr>
</table>
<input type='hidden' name='org' value ='<?php echo $org;?>'>
<input type='submit' name='view' value='View'>
</form>
</div>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 if($_POST['date'] != "" )
 {
  $dateTime = new DateTime($_POST['date']);
  $dateStr=$dateTime->format('Ymd');
  $towluanch =  getTowLaunchType($con);
  $r = mysqli_query($con,"SELECT * FROM billingoptions where bill_other = 1");
  $billother=9999;
  if (mysqli_num_rows($r) > 0)
  {
	$row = mysqli_fetch_array($r);
	$billother=$row['id'];
	$diagtext .= "Bill Other = " . $billother ."<br>";
  }
  echo "<h1>Daily Time Sheet for: ";
  echo $dateTime->format('d/m/Y');
  echo "</h1>";


  echo "<table><tr><th>SEQ</th><th>TOWPLANE</th><th>GLIDER</th><th>Vector</th><th>TOW PILOT</th><th>PIC</th><th>P2</th><th>DURATION</th><th>TOW HEIGHT</th><th>CHARGE</th><th>COMMENTS</th>";
  $sql= "SELECT flights.seq,e.rego_short,flights.glider, a.displayname,b.displayname,c.displayname, (flights.land - flights.start), flights.height, flights.billing_option, d.displayname,flights.billing_member2, comments, f.name , flights.launchtype, flights.location , flights.type, flights.start, flights.land, flights.vector from flights LEFT JOIN members a ON a.id = flights.towpilot LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 LEFT JOIN members d ON d.id = flights.billing_member1 LEFT JOIN aircraft e ON e.id = flights.towplane LEFT JOIN launchtypes f ON f.id = flights.launchtype where flights.org = ".$org." and flights.localdate=" . $dateStr . " order by flights.seq ASC";
  $diagtext .= $sql . "<br>";
  $r = mysqli_query($con,$sql);
  $rownum = 0;
  while ($row = mysqli_fetch_array($r) )
  {
   if ($rownum == 0)
     echo "<h1>LOCATION: ".$row[14]."</h1>";
   $rownum = $rownum + 1;
   echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
   echo "<td>";echo $row[0];echo "</td>";
   if ($towluanch == $row[13])
   {
       echo "<td>";echo $row[1];echo "</td>";
   }
   else
   {
	     echo "<td>";echo $row[12];echo "</td>";
   }
   echo "<td>";echo $row[2];echo "</td>";
   echo "<td>";echo $row[18];echo "</td>";
   echo "<td>";echo $row[3];echo "</td>";
   echo "<td>";echo $row[4];echo "</td>";
   echo "<td>";echo $row[5];echo "</td>";
   $duration = intval($row[6] / 1000);
   $hours = intval($duration / 3600);
   $mins = intval(($duration % 3600) / 60);
   $timeval = sprintf("%02d:%02d",$hours,$mins);
   echo "<td class='right'>";echo $timeval;echo "</td>";

   if ($towluanch == $row[13] && $row[15] == $flightTypeGlider)
   {
      echo "<td class='right'>";echo $row[7];echo "</td>";
   }
   else
   {
      echo "<td></td>";
   }
   echo "<td>";
   if ($row[8] == $billother)
   {
      echo $row[9];
   }
   else
   {     $q1 = "SELECT name FROM billingoptions where id = " . $row[8];
  	$r1 = mysqli_query($con,$q1);
        $row2 = mysqli_fetch_array($r1);
        if ($row2)
           echo $row2[0];
   }
   echo "</td>";
   echo "<td>";
   echo $row[11];
  if ($row[15] == $flightTypeCheck)
  {
     if (strlen($row[11]) > 0)
         echo " ";
     echo "Tow plane check flight";
  }
  if ($row[15] == $flightTypeRetrieve)
  {
     if (strlen($row[11]) > 0)
         echo " ";
     echo "Retrieve";
  }     echo "</td>";
 }
 echo "</table>";
 echo "<p></p>";
 echo "<button onclick='printit()' id='print-button'>Print Sheet</button>";
 mysqli_close($con);
}
}
?>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
