<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style>
@media print {
    #print-button {display: none;}
    @page {size: landscape;}
}

body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
table {border-collapse: collapse;}
th {font-size: 14px;padding-left: 5px;padding-right: 8px;}
td {font-size: 14px;border-style: dotted;border-color:#404040;border-width: 1px;padding-left: 5px;padding-right: 5px;}
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
<?php
include 'helpers.php';
include 'timehelpers.php';
$DEBUG=0;
$org=0;
$diagtext="";
$dateStr = '';
$dateStr2='';
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 $org=$_GET['org'];
 if ($org < 1)
 {
    die("ERROR: No organisation specified");
 }
 $dateTimeZone = new DateTimeZone(orgTimezone($con,$org));
 $dateTime = new DateTime("now", $dateTimeZone);
 $dateStr = $dateTime->format('Ymd');
 $dateStr2=$dateTime->format('d/m/Y'); 
 
 if(isset($_GET['date']))
 {
  $dateStr=$_GET['date'];
  $dateTime->setDate ( substr($dateStr,0,4), substr($dateStr,4,2), substr($dateStr,6,2) );
 }
}
?>
<?php

$towlaunch = getTowLaunchType($con);
$flightTypeGlider = getGlidingFlightType($con); 
$flightTypeCheck = getCheckFlightType($con); 
$flightTypeRetrieve = getRetrieveFlightType($con); 
$orgname = getOrganisationName($con,$org);
$towChargeType = getTowChargeType($con,$org);

$r = mysqli_query($con,"SELECT * FROM billingoptions where bill_other = 1");
$billother=9999;
if (mysqli_num_rows($r) > 0)
{
	$row = mysqli_fetch_array($r);
	$billother=$row['id'];
	$diagtext .= "Bill Other = " . $billother ."<br>";
}

?>
<?php
$message="";
$stClass = getShortTermClass($con,$org);
$towLaunchType = getTowLaunchType($con);
$q= "SELECT members.id, members.email from members where class <> " . $stClass . " and enable_email > 0 and localdate_lastemail <> " .$dateStr;

$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r) )
{
    $isInstructor = false;
    $isInstructor = IsMemberInstructor($con,$row[0]);
    $done=0;
    if (strlen($row[1]) > 0)
    {
        $q1= "SELECT flights.glider, flights.location, (flights.land - flights.start), flights.height, flights.launchtype, a.acronym, flights.pic , flights.p2, flights.start, flights.land from flights LEFT JOIN launchtypes a on a.id = flights.launchtype where flights.org = ".$org." and flights.localdate=" . $dateStr . " and flights.finalised = 1 and (flights.pic = ".$row[0]." or flights.p2 = ".$row[0].") order by flights.seq ASC";
        $r2 = mysqli_query($con,$q1);
        while ($row2 = mysqli_fetch_array($r2) )
        {
          if ($done==0)
          {
             $message="<!DOCTYPE HTML>
<html>
<head>
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
tr.tr1 {background-color:#000040;}
td.td1 {font-family: Arial, Helvetica, sans-serif; color:#000040;font-size:20px}
td.td2 {font-family: Arial, Helvetica, sans-serif; color:#000040;font-size:12px}
td {font-size:14px;}
th {font-size:14px;}
table {border-collapse: collapse;}
.headp1 {font-family: Calibri, Arial, Helvetica, sans-serif; color:white;font-size:50px}
.headp2 {font-family: Calibri, Arial, Helvetica, sans-serif; color:white;font-size:20px}
#container {background-color: #e0e0ff;}
#id1 {background-color: #a0a0ff;border-radius: 5px;margin-left: 20px;}
.right {text-align: right;}
</style>
</head>
<body>
<table>
<tr class='tr1'><td colspan='7' class='headp1'>".$orgname."</td></tr>
<tr class='tr1'><td colspan='7'class='headp2'>operations</td></tr>
<tr><td colspan='7' class='td1'>Your flights for ";
             $message .= $dateStr2;
             $message .= "</td></tr><tr><td colspan='7' class='td1'></td></tr>
<tr><th>GLIDER</th><th>MAKE/MODEL</th><th>LOCATION</th><th>DURATION</th><th>START</th><th>LAND</th>";
if ($towChargeType==1)
   $message .= "<th>HEIGHT</th>";
$message .= "<th>LAUNCH TYPE</th><th>TYPE</th></tr>";
             $done=1;
          }
          $model=getGliderModel($con,$org,$row2[0]);
	  $duration = intval($row2[2] / 1000);
  	  $hours = intval($duration / 3600);
          $mins = intval(($duration % 3600) / 60);
          $timeval = sprintf("%02d:%02d",$hours,$mins);
          if ($row2[4] == $towLaunchType)
              $height = $row2[3];
          $tr = "<tr>";
	        $tr .= "<td>".$row2[0]."</td>";
          $tr .= "<td>".$model."</td>";
          $tr .= "<td>".$row2[1]."</td>";
          $tr .= "<td class='right'>".$timeval."</td>";

          $start_ts = (int)$row2[8] / 1000;
          $land_ts  = (int)$row2[9] / 1000;
          $start = (new DateTime())->setTimestamp($start_ts);
          $land  = (new DateTime())->setTimestamp($land_ts);

          $nz_timezone = new DateTimeZone("Pacific/Auckland");
          $start->setTimezone($nz_timezone);
          $land->setTimezone($nz_timezone);

          $start_time = ($start_ts == 0) ? "" : $start->format('G:i:s');
          $land_time = ($land_ts == 0) ? "" : $land->format('G:i:s');
          $tr .= "<td class='right' style='padding-left:5px;'>".$start_time."</td>";
          $tr .= "<td class='right' style='padding-left:5px;'>".$land_time."</td>";          

	  if ($towChargeType==1)
          {
            $tr .= "<td class='right'>";
            if ($row2[4] == $towLaunchType)
                $tr .= $row2[3];
	    $tr .= "</td>";
          }
	  $tr .= "<td class='right'>".$row2[5]."</td>";
          $tr .= "<td class='right'>";
          if ($row2[6] == $row[0])         
          {
              if ($row2[7] > 0)
              {
                 if ($isInstructor)
	         	$tr .= "I";
                 else
	         	$tr .= "P1";
              }
              else
		 $tr .= "P";
          }
	  else
             $tr .= "P2";

	  $tr .= "</td>";
          $tr .= "</tr>";
          $message .= $tr;
        }
        if ($done==1)
        {
           $message .= "<tr><td colspan='7' class='td1'></td></tr>
<tr><td colspan='7' class='td2'>To check out more go to <a href='glidingops.com'>glidingops.com</a></td></tr>
</table>
</body>
</html>";
	   $headers = 'From: operations@glidingops.com' . "\r\n" .
                 'Reply-To: wgcoperations@gmail.com' . "\r\n" .
                 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();
           mail($row[1], "Your flight summary", $message, $headers);
           $q5 = "UPDATE members SET localdate_lastemail = ".$dateStr." WHERE members.id = ".$row[0];
           $r5 = mysqli_query($con,$q5);

        }
    }
}
?>
<h1>Daily Log Sheet for:<?php echo " ".$dateStr2; ?> </h1>
<table><tr>
<th>SEQ</th>
<th>TOWPLANE</th>
<th>GLIDER</th>
<th>TOW PILOT</th>
<th>PIC</th>
<th>P2</th>
<th>DURATION</th>
<th>TOW HEIGHT</th>
<th>CHARGE</th>
<th>COMMENTS</th>
<?php
$sql= "SELECT flights.seq,e.rego_short,flights.glider, a.displayname,b.displayname,c.displayname, (flights.land - flights.start), flights.height, flights.billing_option, d.displayname,flights.billing_member2, comments, f.name , flights.launchtype, flights.type from flights LEFT JOIN members a ON a.id = flights.towpilot LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 LEFT JOIN members d ON d.id = flights.billing_member1 LEFT JOIN aircraft e ON e.id = flights.towplane LEFT JOIN launchtypes f on f.id = flights.launchtype where flights.org = ".$org." and flights.finalised = 1 and flights.localdate=" . $dateStr . " order by flights.seq ASC";
$diagtext .= $sql . "<br>";
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
  $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
  echo "<td>";echo $row[0];echo "</td>";
  if ($row[13] == $towlaunch)
  {
     echo "<td>";echo $row[1];echo "</td>";
  }
  else
  {
     echo "<td>";echo $row[12];echo "</td>";
  }
  echo "<td>";echo $row[2];echo "</td>";
  echo "<td>";echo $row[3];echo "</td>";
  echo "<td>";echo $row[4];echo "</td>";
  echo "<td>";echo $row[5];echo "</td>";
  $duration = intval($row[6] / 1000);
  $hours = intval($duration / 3600);
  $mins = intval(($duration % 3600) / 60);
  $timeval = sprintf("%02d:%02d",$hours,$mins);    
  echo "<td class='right'>";echo $timeval;echo "</td>";
  if ($row[13] == $towlaunch && $row[14] == $flightTypeGlider)
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
  if ($row[14] == $flightTypeCheck)
  {
     if (strlen($row[11]) > 0)
         echo " ";
     echo "Tow plane check flight";
  }
  if ($row[14] == $flightTypeRetrieve)
  {
     if (strlen($row[11]) > 0)
         echo " ";
     echo "Retrieve";
  }  
  echo "</td>";
}
?>
</table>
<p></p>
<button onclick='printit()' id='print-button'>Print Sheet</button>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
<?php mysqli_close($con);?>
</body>
</html>