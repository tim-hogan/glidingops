<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 64)) {die("Secruity level too low for this page");}
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
<title>Gliding-Users</title>
<link rel="stylesheet" type="text/css" href="styletable1.css">
<script>function goBack() {window.history.back()}</script>
</head>
<body>
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
$pageid=4;
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
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "USERNAME";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "USERCODE";echo "</th>";}
if ($_SESSION['security'] & 128){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ORGANISATION";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "SECURITY LEVEL";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "MEMEBR";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='users-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "FORCE PW RESET";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT users.id,users.name,users.usercode,a.name,users.securitylevel,b.displayname,users.force_pw_reset FROM users LEFT JOIN organisations a ON a.id = users.org LEFT JOIN members b ON b.id = users.member"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE users.org=".$_SESSION['org'];}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "id";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "name";
   break;
 case 3:
   $sql .= "usercode";
   break;
 case 4:
   $sql .= "a.name";
   break;
 case 5:
   $sql .= "securitylevel";
   break;
 case 6:
   $sql .= "b.displayname";
   break;
 case 7:
   $sql .= "force_pw_reset";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='users.php?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if ($_SESSION['security'] & 128){echo "<td>";echo $row[3];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[4];echo "</td>";}
if (true){echo "<td>";echo $row[5];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[6];echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
