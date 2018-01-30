<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<link rel="stylesheet" type="text/css" href="heading2.css">
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
    #head {display: none;}
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
</script>
<?php
include 'helpers.php';
?>
</head>
<body>
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
<div id='head'>
<table class='t1'>
<tr><td class='tdhead1'><p class='headp1'>wellington</p></td><td class='head3' rowspan="2"><?php echo $_SESSION['dispname'];?> Signed in as: <?php echo $_SESSION['who'];?><br><a href='SignOut.php' class='wt'>Sign Out</a><br><a href='PasswordChange' class='wt'>Change Password</a></td><td class='head4'></td></tr>
<tr><td class='tdhead1'><p class='headp2'>gliding club operations</p></td></tr>
</table>
</div>
<?php
$DEBUG=1;
$diagtext="";
	
  
  $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
  if (mysqli_connect_errno())
  {
   echo "<p>Unable to connect to database</p>";
   exit();
  }
  
  $role1 = getRoleId($con,'Tow Pilot');
 
  
  $q = "SELECT localdate from flights where flights.org = ".$_SESSION['org']. " order by localdate ASC";
  $r = mysqli_query($con,$q);
  if ($row = mysqli_fetch_array($r) )
  {
    echo "<h2>TOW PILOTS REPORT FOR FLIGHTS FROM " . substr($row[0],6,2) . "/" . substr($row[0],4,2)  . "/" . substr($row[0],0,4) . "</h2>";
  }


  
  echo "<table>";
  $dateNow = new DateTime('now');
  $thisYear = intval($dateNow->format('Y'));
  $lastYear = $thisYear-1;
  
  $q = "SELECT role_member.member_id, a.displayname, a.surname , a.firstname from role_member LEFT JOIN members a ON a.id = role_member.member_id where role_member.org = " .$_SESSION['org']. " and role_id = " . $role1 . " order by a.surname , a.firstname ";
  $r = mysqli_query($con,$q);
  while ($row = mysqli_fetch_array($r) )
  {
    echo "<tr><th class='thname'>" . $row[1] . "</th></tr>";
    $days = 0;
    $first=0;
    $cntThisYr=0;
    $cntLastYr=0;
    $dateFlight = new DateTime();
    $strLastDate="";
    $q1= "SELECT localdate, (flights.land-flights.start) from flights where flights.towpilot = " . $row[0] . " order by localdate DESC";
    $r1 = mysqli_query($con,$q1);
    while ($row1 = mysqli_fetch_array($r1) )
    {
       $strFDate=$row1[0];
       $dateFlight->setDate(substr($strFDate,0,4),substr($strFDate,4,2),substr($strFDate,6,2));
       if ($first == 0)
       {
           $strLastDate = substr($strFDate,6,2) . "/" . substr($strFDate,4,2) . "/" . substr($strFDate,0,4);
           $days = ($dateNow->getTimestamp() - $dateFlight->getTimestamp()) / (3600 * 24);
           $first=1;
       }
       if (intval($dateFlight->format('Y')) == $thisYear)
       {
          $cntThisYr = $cntThisYr + 1;
       }
       
       if (intval($dateFlight->format('Y')) == $lastYear)
       {
          $cntLastYr = $cntLastYr + 1;
       }
      
      
    }
    if (($cntThisYr +  $cntLastYr) > 0)
    {
       
       echo "<tr><td>Last Tow</td><td>" .$strLastDate. "</td><td> ". $days . " days ago </td></tr>";
       echo "<tr><td>Flights</td><td>Number</td></tr>";
       echo "<tr><td>" . $thisYear . "</td><td>" .$cntThisYr . "</td></tr>";
       echo "<tr><td>" . $lastYear . "</td><td>" .$cntLastYr . "</td></tr>";

    }
    else
    {
       echo "<tr><td>No Flights</td></tr>";
    }  
}

  echo "<table>";
  echo "<p></p>";
  
  echo "<button onclick='printit()' id='print-button'>Print Report</button>";

  mysqli_close($con);

?>

<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
