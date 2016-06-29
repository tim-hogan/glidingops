<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 1)) {die("Secruity level too low for this page");}
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
<title>Gliding-All Members</title>
<style>
<?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?>
</style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styletable1.css">
<script>function goBack() {window.history.back()}</script>
<script>function SelectionChange() {document.getElementById("selform").submit();}</script>
<script><?php if(isset($_GET['sel'])) echo "var thissel=" . $_GET['sel'] .";"; else echo "thissel=0;";?>
function optSel(s){var y=document.getElementById('selopt').childNodes;for(x=0;x<y.length;x++){if (y[x].value == s)y[x].selected=true;}}</script>
</head>
<body onload='optSel(thissel)'>
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
$pageid=12;
$pkcol=3;
$pagesortdata = $_SESSION['pagesortdata'];
$colsort = $pagesortdata[$pageid];
$selopt = 0;
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
 if(isset($_GET['sel']))
 {
   $selopt = $_GET['sel'];
 }
}
if ($colsort == 0)
 	$colsort = $pkcol;
?>
<div id="sel">
<form id = "selform" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<select id='selopt' name="sel" onchange='SelectionChange()'>
<option value = '0'>All Except Short Term</option>
<option value = '1'>All</option>
<select></form>
</div>
<div id="div1">
<div id="div2">
<table><tr>
<?php
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "FIRSTNAME";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "SURNAME";echo "</th>";}
if (true){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "DISPLAY NAME";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "GNZ NUMBER";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "QGP NUMBER";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "CLASS";echo "</th>";}
if (true){echo '<th ';if ($colsort == 8) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=8'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "MOBILE PHONE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 9) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=9'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "EMAIL";echo "</th>";}
if (true){echo '<th ';if ($colsort == 10) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=10'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "SOLO";echo "</th>";}
if (true){echo '<th ';if ($colsort == 11) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=11'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TEXT";echo "</th>";}
if (true){echo '<th ';if ($colsort == 12) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='members-list.php?col=12'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "EMIAL";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT members.id,members.firstname,members.surname,members.displayname,members.gnz_number,members.qgp_number,b.class,members.phone_mobile,members.email,members.gone_solo,members.enable_text,members.enable_email FROM members LEFT JOIN membership_class b ON b.id = members.class"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE members.org=".$_SESSION['org'];}
switch ($selopt) { 
case 0:  $sql .= " and b.class <> 'Short Term' "; break;
case 1:  break;
}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "surname";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "firstname";
   break;
 case 3:
   $sql .= "surname";
   break;
 case 4:
   $sql .= "displayname";
   break;
 case 5:
   $sql .= "gnz_number";
   break;
 case 6:
   $sql .= "qgp_number";
   break;
 case 7:
   $sql .= "b.class";
   break;
 case 8:
   $sql .= "phone_mobile";
   break;
 case 9:
   $sql .= "email";
   break;
 case 10:
   $sql .= "gone_solo";
   break;
 case 11:
   $sql .= "enable_text";
   break;
 case 12:
   $sql .= "enable_email";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='Member?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if (true){echo "<td>";echo $row[3];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[4];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[5];echo "</td>";}
if (true){echo "<td>";echo $row[6];echo "</td>";}
if (true){echo "<td>";echo $row[7];echo "</td>";}
if (true){echo "<td>";echo $row[8];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[9];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[10];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[11];echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<form id="form1" action='Member' method='GET'><input type='submit' value = 'Create New'>
</form>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
