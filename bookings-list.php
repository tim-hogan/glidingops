<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 6)) {die("Secruity level too low for this page");}
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
$pageid=38;
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
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "BOOKING MADE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "BOOKING MODIFIED";echo "</th>";}
if (true){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "FROM";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TO";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TYPE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Description";echo "</th>";}
if (true){echo '<th ';if ($colsort == 8) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=8'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Member";echo "</th>";}
if (true){echo '<th ';if ($colsort == 9) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=9'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Aircraft";echo "</th>";}
if (true){echo '<th ';if ($colsort == 10) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=10'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Instructor";echo "</th>";}
if (true){echo '<th ';if ($colsort == 11) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='bookings-list.php?col=11'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "MADE BY";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT bookings.id,bookings.made,bookings.lastmodify,bookings.start,bookings.end,b.typename,bookings.description,c.displayname,d.registration,e.displayname,bookings.user FROM bookings LEFT JOIN bookingtypes b ON b.id = bookings.type LEFT JOIN members c ON c.id = bookings.member LEFT JOIN aircraft d ON d.id = bookings.aircraft LEFT JOIN members e ON e.id = bookings.instructor"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE bookings.org=".$_SESSION['org'];}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "id";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "made";
   break;
 case 3:
   $sql .= "lastmodify";
   break;
 case 4:
   $sql .= "start";
   break;
 case 5:
   $sql .= "end";
   break;
 case 6:
   $sql .= "b.typename";
   break;
 case 7:
   $sql .= "description";
   break;
 case 8:
   $sql .= "c.displayname";
   break;
 case 9:
   $sql .= "d.registration";
   break;
 case 10:
   $sql .= "e.displayname";
   break;
 case 11:
   $sql .= "f.name";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='bookings.php?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if (true){echo "<td>";if ($row[3]!=0){$start_d=new DateTime($row[3]); echo timeLocalFormat($start_d,$_SESSION['timezone'],'d/m/Y H:i:s');}echo "</td>";}
if (true){echo "<td>";if ($row[4]!=0){$end_d=new DateTime($row[4]); echo timeLocalFormat($end_d,$_SESSION['timezone'],'d/m/Y H:i:s');}echo "</td>";}
if (true){echo "<td>";echo $row[5];echo "</td>";}
if (true){echo "<td>";echo $row[6];echo "</td>";}
if (true){echo "<td>";echo $row[7];echo "</td>";}
if (true){echo "<td>";echo $row[8];echo "</td>";}
if (true){echo "<td>";echo $row[9];echo "</td>";}
if (true){echo "<td>";echo $row[10];echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
