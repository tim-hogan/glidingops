<?php session_start(); ?>
<?php $org=0; if(isset($_SESSION['org'])) $org=$_SESSION['org'];?>
<?php
require "./includes/moduleEnvironment.php";

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
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style type="text/css">
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container{margin: 5px;border: 0px;}
#messagearea{margin: 5px;border: 0px;background-color:#e0e0e0;padding:1px;border-radius: 5px;}
#messagearea2{margin: 5px;border: 0px;background-color:#e0e0e0;}
#menu1{margin: 5px;border: 0px;background-color:#e0e0e0;padding:1px;border-radius: 5px;}
#menu2{margin: 5px;border: 0px;background-color:#e0e0e0;}
#menu2 td {vertical-align: top;}
a {text-decoration: none;border-left: 5px;}
a:link{color: #000000;}
a:visited {color: #000000;}
a:hover {color: #0000FF;}
p.u {font-size: 12px;margin-top: 0px;margin-left: 0px;margin-bottom: 0px;}
p.u2 {font-size: 12px;margin-top: 0px;margin-left: 20px;margin-bottom: 0px;}
p.p2 {font-size: 12px;margin: 0;font-weight: bold;}
p.p3 {font-size: 12px;margin: 0;font-weight: bold;}
p.p4 {font-size: 12px;margin-left: 10px;margin-top: 0px;margin-bottom: 0px;}
h1 {font-size: 14px; color: #0000e0}
h2.u {font-size: 14px;}
h3.u {font-size: 12px; margin-left: 10px;}
table {border-collapse:collapse;}
table.tbl1 {width: 100%;table-layout: fixed;}
.s1 {font-weight: bold;color: #000080}
</style>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</head>
<body>
<?php include __DIR__.'/helpers/dev_mode_banner.php' ?>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<div id='container'>
<div id='messagearea'>
<div id='messagearea2'>
<p class='p2'>CLUB MESSAGES:</p>
<?php include __DIR__.'/helpers/club_messages.php' ?>
</div>
</div>
<div id="menu1">
<div id="menu2">
<table class='tbl1'>
<?php
echo "<tr>";
$col = 0;
$totcol = 6;
if (intval($_SESSION['memberid']) > 0)
{
   echo "<td><h2 class='u'><a href='MyFlights'>MY FLIGHTS</a></h2></td>";
   $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}
echo "<td><h2 class='u'><a href='FlyingNow?org=".$org."'>FLYING NOW</a></h2>";
if ($org==1)
	echo "<p class='u'><a href='wgc'>Real Time map</a></p>";
if ($org==2)
	echo "<p class='u'><a href='ssb'>Real Time map</a></p>";
if ($org==3)
	echo "<p class='u'><a href='cgc'>Real Time map</a></p>";
if ($org==4)
	echo "<p class='u'><a href='agc'>Real Time map</a></p>";
echo "</td>";
$col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";

echo "<td><h2 class='u'><a href='ViewBookings'>BOOKINGS</a></h2>";
if (($_SESSION['security'] & 2)) echo "<p class='u'><a href='bookings.php'>New</a></p>";
echo "</td>";
$col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";

echo "<td><h2 class='u'><a href='Rosters'>ROSTERS</a></h2></td>";
$col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";

echo "<td><h2 class='u'><a href='AllMembers'>MEMBERS</a></h2></td>";
$col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";

echo "<td><h2 class='u'><a href='Documents'>DOCUMENTS</a></h2></td>";
$col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";

if (($_SESSION['security'] & 4))
{
 echo "<td><h2 class='u'><a href='MessagingPage'>MESSAGING</a></h2></td>";
 $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

if ($_SESSION['security'] >= 1)
{
 echo "<td><h2 class='u'>DAILY OPS</h2>";
 if ($_SESSION['security'] >= 4)
    echo "<p class='u'><a href='DailySheet?org=" .$org."'>New Daily Timesheet</a></p>";
 echo "<p class='u'><a href='DailyLogSheet.php?org=" .$org."'>View Daily Timesheet</a></p>";

 echo "</td>";
 $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

if ($_SESSION['security'] >= 1)
{
  echo "<td><h2 class='u'>REPORTS</h2>";
  if (($_SESSION['security'] & 8))
     echo "<p class='u'><a href='Treasurer.php'>Treasurer Report</a></p>";
  if (($_SESSION['security'] & 1))
     echo "<p class='u'><a href='/app/allFlightsReport'>All Flights Report</a></p>";
  if (($_SESSION['security'] & 24))
     echo "<p class='u'><a href='/app/reports/membersRolesStatsReport'>Members roles Report</a></p>";
  if (($_SESSION['security'] & 24))
     echo "<p class='u'><a href='Instructors.php'>Instructors Report</a></p>";
  if (($_SESSION['security'] & 24))
     echo "<p class='u'><a href='MedicalBfr.php'>Medical/BFR Report</a></p>";
  if (($_SESSION['security'] & 24))
     echo "<p class='u'><a href='Towy.php'>Tow Pilots Report</a></p>";
  if (($_SESSION['security'] & 32))
     echo "<p class='u'><a href='Engineer.php'>Engineer Report</a></p>";
   if (($_SESSION['security'] & 32))
     echo "<p class='u'><a href='last-flights-list.php?col=1&descsort=1'>Currency Report</a></p>";

  echo "</td>";
  $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

if (($_SESSION['security'] & 120))
{
  echo "<td><h2 class='u'>DATA MAINTENANCE</h2>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='address_type-list.php'>Address Types</a></p>";
  if (($_SESSION['security'] & 104))
     echo "<p class='u'><a href='AllAircraft'>Aircraft</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='AircraftTypes'>Aircraft Types</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='airspace-list.php'>Airspace</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='airspacecoords-list.php'>Airspace coordiantes</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='BookingTypes'>Booking Types</a></p>";
  if (($_SESSION['security'] & 128))
     echo "<p class='u'><a href='BillingOptions'>Charging Options</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='DutyTypes'>Duty Types</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='flights-list.php'>Flights Raw</a></p>";
  if (($_SESSION['security'] & 72))
     echo "<p class='u'><a href='IncentiveSchemes'>Incentive Schemes</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='membership_class-list.php'>Membership Classes</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='membership_status-list.php'>Membership Statuses</a></p>";
  if (($_SESSION['security'] & 72))
     echo "<p class='u'><a href='OtherCharges'>Other Charges</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='Roles'>Roles</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='AssignRoles'>Role Assigment</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='spots-list.php'>Spots</a></p>";
  if (($_SESSION['security'] & 72))
     echo "<p class='u'><a href='SubsToSchemes'>Subs to Incentives</a></p>";
  if (($_SESSION['security'] & 72))
     echo "<p class='u'><a href='TowCharges'>Tow Charging</a></p>";
  if (($_SESSION['security'] & 64))
     echo "<p class='u'><a href='maintenance/duplicates_index.php'>Manage duplicate memberships</a></p>";
  if (($_SESSION['security'] & 64))
      echo "<p class='u'><a href='app/vectors'>Manage vector definitions</a></p>";
  if (($_SESSION['security'] & 64))
      echo "<p class='u'><a href='manage-secret-code.php'>Manage secret code</a></p>";

  echo "</td>";
  $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

if (($_SESSION['security'] & 64))
{
  echo "<td><h2 class='u'>USERS</h2>";
  echo "<p class='u'><a href='users.php'>Create User</a></p>";
  echo "<p class='u'><a href='users-list.php'>View users</a></p>";

  echo "</td>";
  $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

if (($_SESSION['security'] & 64))
{
 echo "<td><h2 class='u'>DIAGNOSTICS & RECOVERY</h2>";
 echo "<p class='u'><a href='Recovery.php'>Get Local Browser Cache</a></p>";

 echo "</td>";
 $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

if (($_SESSION['security'] & 128))
{
 echo "<td><h2 class='u'>SUPER ADMIN</h2>";
 echo "<p class='u'><a href='Organisations'>Organisations</a></p>";
 echo "</td>";
 $col = ($col + 1) % $totcol;if ($col == 0) echo "</tr><tr>";
}

echo "</tr>";
?>
</table>
<?php
if ($org == 1)
{
echo "<h1>KEEP UP TO DATE ON TWITTER</h1>";
echo "<a href='https://twitter.com/glidingwlgtn' class='twitter-follow-button' data-show-count='false' data-lang='en'>Follow @glidingwlgtn</a>";
}
?>
</div>
</div>
</div>
</body>
</html>
