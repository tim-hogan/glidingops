<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 32)) {die("Secruity level too low for this page");}
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
<title>Gliding - Currency</title>
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
?>
<div id="div1">
<p>Last flights for each active member, sortable by any column.</p>
<p>If you can't find a member, ensure they are marked as "Active" (If not, please go to Membership and fix that :)</p>
<p>If you can see members you shouldn't see, then please to to Membership and mark them as "Passive / Retired".</p>
<div id="div2">
<table><tr>
<?php
$colsort = 0;
$descsort = 1;
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    if(isset($_GET['col']))
    if($_GET['col'] != "" && $_GET['col'] != null)
        $colsort = $_GET['col'];
    if(isset($_GET['descsort']))
    if($_GET['descsort'] != "" && $_GET['descsort'] != null)
        $descsort = $_GET['descsort'];
}

$renderHeaderCell = function($sequence, $header, $colsort, $descsort){
    echo '<th ';
    if ($colsort == $sequence) 
        echo "class='colsel' ";
    echo "onclick=\"location.href='last-flights-list.php?col=$sequence&descsort=";

    if ($colsort == $sequence) 
        echo -1*$descsort; 
    else echo 1; 
    echo "'\"";
    echo " style='cursor:pointer;'";
    echo ">";
    echo $header;
    echo "</th>";
};
$renderHeaderCell(0, "MEMBER", $colsort, $descsort);
$renderHeaderCell(1, "LAST FLIGHT", $colsort, $descsort);
$renderHeaderCell(2, "LAST SOLO", $colsort, $descsort);
$renderHeaderCell(3, "LAST AS P2", $colsort, $descsort);
$renderHeaderCell(4, "LAST AS P1 WITH OTHER P2", $colsort, $descsort);
?>
</tr>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$sql=<<<SQL
SELECT 
    m.displayname
    ,MAX(CASE WHEN f.pic = m.id OR f.p2 = m.id THEN localdate ELSE NULL END) as last_flight
    ,MAX(CASE WHEN f.p2 is null THEN localdate ELSE NULL END) as last_solo_flight
    ,MAX(CASE WHEN f.p2 = m.id THEN localdate ELSE NULL END) as last_flight_as_P2
    ,MAX(CASE WHEN f.pic = m.id and f.p2 is not null THEN localdate ELSE NULL END) as last_p1_with_p2_flight
FROM gliding.flights f JOIN gliding.members m ON (f.pic = m.id OR f.p2 = m.id)
WHERE
    f.org = {$_SESSION['org']} AND m.class = 1 AND m.status = 1
GROUP BY m.id
SQL;
$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "displayname";
   break;
 case 1:
    $sql .= "last_flight";
    break;
 case 2:
   $sql .= "last_solo_flight";
   break;
 case 3:
   $sql .= "last_flight_as_P2";
   break;
 case 4:
   $sql .= "last_p1_with_p2_flight";
   break;
}
if ($descsort == 1)
    $sql .= " ASC";
else
    $sql .= " DESC";
$diagtext.= "SQL=".$sql;
$r = mysqli_query($con,$sql);
$rownum = 0;

$renderDateCell = function($column){
    echo "<td>";
    if ($column!=0 && $column!=null){
        $date=new DateTime($column); 
        echo $date->format('D d/m/Y');
    }
    echo "</td>";
};

while ($row = mysqli_fetch_array($r))
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";
    echo "<td class='right'>";echo $row[0];echo "</td>";
    $renderDateCell($row[1]);
    $renderDateCell($row[2]);
    $renderDateCell($row[3]);
    $renderDateCell($row[4]);
  echo "</tr>";
}


?>
</table>
</div>
</div>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
