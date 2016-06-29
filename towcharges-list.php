<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 8)) {die("Secruity level too low for this page");}
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
<title>Gliding Tow Charges</title>
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
$pageid=44;
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
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TOW PLANE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TYPE (0/1)";echo "</th>";}
if (true){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "HEIGHT";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "CLUB GLIDER";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "MEMBER CLASS";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "EFFECTIVE DATE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 8) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='towcharges-list.php?col=8'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "COST";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT towcharges.id,b.rego_short,towcharges.type,towcharges.height,towcharges.club_glider,c.class,towcharges.effective_from,towcharges.cost FROM towcharges LEFT JOIN aircraft b ON b.id = towcharges.plane LEFT JOIN membership_class c ON c.id = towcharges.member_class"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE towcharges.org=".$_SESSION['org'];}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "id";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "b.rego_short";
   break;
 case 3:
   $sql .= "type";
   break;
 case 4:
   $sql .= "height";
   break;
 case 5:
   $sql .= "club_glider";
   break;
 case 6:
   $sql .= "c.class";
   break;
 case 7:
   $sql .= "effective_from";
   break;
 case 8:
   $sql .= "cost";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='TowCharge?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td>";echo $row[1];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[2];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[3];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[4];echo "</td>";}
if (true){echo "<td>";echo $row[5];echo "</td>";}
if (true){echo "<td>";if ($row[6]!=0){$effective_from_d=new DateTime($row[6]); echo $effective_from_d->format('d/m/Y');}echo "</td>";}
if (true){echo "<td class='right'>";echo $row[7];echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<form id="form1" action='TowCharge' method='GET'><input type='submit' value = 'Create New'>
</form>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
