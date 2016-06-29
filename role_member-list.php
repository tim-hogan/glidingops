<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 4)) {die("Secruity level too low for this page");}
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
<title>Gliding-Role Assignment</title>
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
$pageid=50;
$pkcol=2;
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
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='role_member-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='role_member-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ROLE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='role_member-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "MEMBER";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT role_member.id,b.name,c.displayname FROM role_member LEFT JOIN roles b ON b.id = role_member.role_id LEFT JOIN members c ON c.id = role_member.member_id"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE role_member.org=".$_SESSION['org'];}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "role_id";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "b.name";
   break;
 case 3:
   $sql .= "c.displayname";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='AssignRole?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<form id="form1" action='AssignRole' method='GET'><input type='submit' value = 'Create New'>
</form>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
