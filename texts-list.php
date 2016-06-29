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
$pageid=18;
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
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Unique ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Message Text";echo "</th>";}
if (true){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Member Name";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "To";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Status";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Creation Time";echo "</th>";}
if (true){echo '<th ';if ($colsort == 8) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=8'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Sent Time";echo "</th>";}
if (true){echo '<th ';if ($colsort == 9) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='texts-list.php?col=9'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "Received Time";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT texts.txt_id,texts.txt_unique,a.msg,b.displayname,texts.txt_to,texts.txt_status,texts.txt_timestamp_create,texts.txt_timestamp_sent,texts.txt_timestamp_recv FROM texts LEFT JOIN messages a ON a.id = texts.txt_msg_id LEFT JOIN members b ON b.id = texts.txt_member_id"; 
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "txt_id";
break;
 case 1:
   $sql .= "txt_id";
   break;
 case 2:
   $sql .= "txt_unique";
   break;
 case 3:
   $sql .= "a.msg";
   break;
 case 4:
   $sql .= "b.displayname";
   break;
 case 5:
   $sql .= "txt_to";
   break;
 case 6:
   $sql .= "txt_status";
   break;
 case 7:
   $sql .= "txt_timestamp_create";
   break;
 case 8:
   $sql .= "txt_timestamp_sent";
   break;
 case 9:
   $sql .= "txt_timestamp_recv";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='texts.php?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td class='right'>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if (true){echo "<td>";echo $row[3];echo "</td>";}
if (true){echo "<td>";echo $row[4];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[5];echo "</td>";}
if (true){echo "<td>";if ($row[6]!=0){$txt_timestamp_create_d=new DateTime($row[6]); echo timeLocalFormat($txt_timestamp_create_d,$_SESSION['timezone'],'d/m/Y H:i:s');}echo "</td>";}
if (true){echo "<td>";if ($row[7]!=0){$txt_timestamp_sent_d=new DateTime($row[7]); echo timeLocalFormat($txt_timestamp_sent_d,$_SESSION['timezone'],'d/m/Y H:i:s');}echo "</td>";}
if (true){echo "<td>";if ($row[8]!=0){$txt_timestamp_recv_d=new DateTime($row[8]); echo timeLocalFormat($txt_timestamp_recv_d,$_SESSION['timezone'],'d/m/Y H:i:s');}echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
