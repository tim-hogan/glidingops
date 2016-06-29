<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 72)) {die("Secruity level too low for this page");}
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
$pageid=34;
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
if (true){echo '<th ';if ($colsort == 1) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=1'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "ID";echo "</th>";}
if (true){echo '<th ';if ($colsort == 2) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=2'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "DATE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 3) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=3'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "LOCATION";echo "</th>";}
if (true){echo '<th ';if ($colsort == 4) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=4'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "SEQ";echo "</th>";}
if (true){echo '<th ';if ($colsort == 5) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=5'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TYPE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 6) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=6'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "LAUNCH TYPE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 7) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=7'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TOW PLANE";echo "</th>";}
if (true){echo '<th ';if ($colsort == 8) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=8'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "GLIDER";echo "</th>";}
if (true){echo '<th ';if ($colsort == 9) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=9'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TOW PILOT";echo "</th>";}
if (true){echo '<th ';if ($colsort == 10) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=10'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "PIC";echo "</th>";}
if (true){echo '<th ';if ($colsort == 11) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=11'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "P2";echo "</th>";}
if (true){echo '<th ';if ($colsort == 12) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=12'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TAKEOFF";echo "</th>";}
if (true){echo '<th ';if ($colsort == 13) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=13'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "TOW LAND";echo "</th>";}
if (true){echo '<th ';if ($colsort == 14) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=14'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "LAND";echo "</th>";}
if (true){echo '<th ';if ($colsort == 15) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=15'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "HEIGHT";echo "</th>";}
if (true){echo '<th ';if ($colsort == 16) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=16'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "BILLING OPTION";echo "</th>";}
if (true){echo '<th ';if ($colsort == 17) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=17'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "BILLING 1";echo "</th>";}
if (true){echo '<th ';if ($colsort == 18) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=18'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "BILLING 2";echo "</th>";}
if (true){echo '<th ';if ($colsort == 19) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=19'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "COMMENTS";echo "</th>";}
if (true){echo '<th ';if ($colsort == 20) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=20'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "FINALISED";echo "</th>";}
if (true){echo '<th ';if ($colsort == 21) echo "class='colsel'";echo " onclick=";echo "\"";echo "location.href='flights-list.php?col=21'";echo "\"";echo " style='cursor:pointer;'";echo ">";echo "DELETED";echo "</th>";}
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql= "SELECT flights.id,flights.localdate,flights.location,flights.seq,b.name,c.name,d.rego_short,flights.glider,e.displayname,f.displayname,g.displayname,flights.start,flights.towland,flights.land,flights.height,h.name,i.displayname,j.displayname,flights.comments,flights.finalised,flights.deleted FROM flights LEFT JOIN flighttypes b ON b.id = flights.type LEFT JOIN launchtypes c ON c.id = flights.launchtype LEFT JOIN aircraft d ON d.id = flights.towplane LEFT JOIN members e ON e.id = flights.towpilot LEFT JOIN members f ON f.id = flights.pic LEFT JOIN members g ON g.id = flights.p2 LEFT JOIN billingoptions h ON h.id = flights.billing_option LEFT JOIN members i ON i.id = flights.billing_member1 LEFT JOIN members j ON j.id = flights.billing_member2"; 
if ($_SESSION['org'] > 0){$sql .= " WHERE flights.org=".$_SESSION['org'];}
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "localdate";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "localdate";
   break;
 case 3:
   $sql .= "location";
   break;
 case 4:
   $sql .= "seq";
   break;
 case 5:
   $sql .= "b.name";
   break;
 case 6:
   $sql .= "c.name";
   break;
 case 7:
   $sql .= "d.rego_short";
   break;
 case 8:
   $sql .= "glider";
   break;
 case 9:
   $sql .= "e.displayname";
   break;
 case 10:
   $sql .= "f.displayname";
   break;
 case 11:
   $sql .= "g.displayname";
   break;
 case 12:
   $sql .= "start";
   break;
 case 13:
   $sql .= "towland";
   break;
 case 14:
   $sql .= "land";
   break;
 case 15:
   $sql .= "height";
   break;
 case 16:
   $sql .= "h.name";
   break;
 case 17:
   $sql .= "i.displayname";
   break;
 case 18:
   $sql .= "j.displayname";
   break;
 case 19:
   $sql .= "comments";
   break;
 case 20:
   $sql .= "finalised";
   break;
 case 21:
   $sql .= "deleted";
   break;
}
$sql .= " ASC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='flights.php?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td class='right'>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[3];echo "</td>";}
if (true){echo "<td>";echo $row[4];echo "</td>";}
if (true){echo "<td>";echo $row[5];echo "</td>";}
if (true){echo "<td>";echo $row[6];echo "</td>";}
if (true){echo "<td>";echo $row[7];echo "</td>";}
if (true){echo "<td>";echo $row[8];echo "</td>";}
if (true){echo "<td>";echo $row[9];echo "</td>";}
if (true){echo "<td>";echo $row[10];echo "</td>";}
if (true){echo "<td>";echo $row[11];echo "</td>";}
if (true){echo "<td>";echo $row[12];echo "</td>";}
if (true){echo "<td>";echo $row[13];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[14];echo "</td>";}
if (true){echo "<td>";echo $row[15];echo "</td>";}
if (true){echo "<td>";echo $row[16];echo "</td>";}
if (true){echo "<td>";echo $row[17];echo "</td>";}
if (true){echo "<td>";echo $row[18];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[19];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[20];echo "</td>";}
  echo "</tr>";
}
?>
</table>
</div>
</div>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
