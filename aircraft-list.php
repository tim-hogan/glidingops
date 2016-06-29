<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 120)) {die("Secruity level too low for this page");}
}else{
 header('Location: Login.php');
 die("Please logon");
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<title>Aircraft</title>
<style>
<?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?>
</style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styletable1.css">
<script>function goBack() {window.history.back()}</script>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<?php
include 'timehelpers.php';
function dtfmt($dt)
{
	if (substr($dt,0,4) != '0000')
		return substr($dt,8,2).'/'.substr($dt,5,2).'/'.substr($dt,0,4);
	else
		return '';
}
$DEBUG=0;
$diagtext="";
$pageid=26;
$pkcol=1;
$pagesortdata = $_SESSION['pagesortdata'];
$colsort = $pagesortdata[$pageid];
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if(isset($_GET['col']))
 {
  if($_GET['col'] != "" && $_GET['col'] != null)
  {
   $colsort = $_GET['col'];
   $pagesortdata[$pageid] = $colsort;
   $_SESSION['pagesortdata'] = $pagesortdata;
  }
 }
}
if ($colsort == 0)
 	$colsort = $pkcol;
?>
<div id="div1">
<div id="div2">
<table><tr>
<?php
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "REGISTRATION";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "REGO SHORT";echo "</th>";}
if (true){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TYPE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "DESCRIPTION";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "SEATS";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "SERIAL";echo "</th>";}
if (true){echo '<th ';if ($colsort == 8) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=8'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "CLUB GLIDER";echo "</th>";}
if (true){echo '<th ';if ($colsort == 9) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=9'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "BOOKABLE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 10) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=10'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "CHARGE PER MINUTE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 11) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=11'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "MAX MINUTES CHARGE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 12) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=12'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "NEXT ANNUAL";echo "</th>";}
if (true){echo '<th ';if ($colsort == 13) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='aircraft-list.php?col=13'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "NEXT SUPPLEMENTARY";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT aircraft.id,aircraft.registration,aircraft.rego_short,b.name,aircraft.make_model,aircraft.seats,aircraft.serial,aircraft.club_glider,aircraft.bookable,aircraft.charge_per_minute,aircraft.max_perflight_charge,aircraft.next_annual,aircraft.next_supplementary FROM aircraft LEFT JOIN aircrafttype b ON b.id = aircraft.type"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE aircraft.org=".$_SESSION['org'];}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "id";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "registration";
   break;
 case 3:
   $sql .= "rego_short";
   break;
 case 4:
   $sql .= "b.name";
   break;
 case 5:
   $sql .= "make_model";
   break;
 case 6:
   $sql .= "seats";
   break;
 case 7:
   $sql .= "serial";
   break;
 case 8:
   $sql .= "club_glider";
   break;
 case 9:
   $sql .= "bookable";
   break;
 case 10:
   $sql .= "charge_per_minute";
   break;
 case 11:
   $sql .= "max_perflight_charge";
   break;
 case 12:
   $sql .= "next_annual";
   break;
 case 13:
   $sql .= "next_supplementary";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='Aircraft?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if (true){echo "<td>";echo $row[3];echo "</td>";}
if (true){echo "<td>";echo $row[4];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[5];echo "</td>";}
if (true){echo "<td>";echo $row[6];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[7];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[8];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[9];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[10];echo "</td>";}
if (true){echo "<td>";if ($row[11]!=0){$next_annual_d=new DateTime($row[11]); echo $next_annual_d->format('d/m/Y');}echo "</td>";}
if (true){echo "<td>";if ($row[12]!=0){$next_supplementary_d=new DateTime($row[12]); echo $next_supplementary_d->format('d/m/Y');}echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<form id="form1" action='Aircraft' method='GET'><input type='submit' value = 'Create New'>
</form>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
