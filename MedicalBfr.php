<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<link rel="stylesheet" type="text/css" href="heading2.css">
<style>
@media print {
    th {font-size: 10px;padding-left: 1em;}
    td {font-size: 10px;padding-left: 1em;}
    th.thname {font-size: 12px;padding-left: 0em;text-align: left;}
    h1 {font-size: 18px;}
    h2 {font-size: 16px;}
    h3 {font-size: 14px;}
    .indent1 {padding-left: 2em;}
    #print-button {display: none;}
    #divhdr {display: none;}
    #head {display: none;}
    @page {size: landscape;}
}
@media screen {
     th {font-size: 12px;padding-left: 20px;}
     td {font-size: 12px;padding-left: 20px;}
     th.thname {font-size: 14px;padding-left: 0px;text-align: left;}
     h1 {font-size: 20px;}
     h2 {font-size: 18px;}
     h3 {font-size: 16px;}
     .indent1 {padding-left: 20px;}
}
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
table {border-collapse: collapse;}
.right {text-align: right;}
.left {text-align: left;}
.bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
</style>
<script>
function printit(){window.print();}
</script>
<?php
include 'helpers.php';
?>
</head>
<body>
<?php
if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 32))
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
<div id='head'>
<table class='t1'>
<tr><td class='tdhead1'><p class='headp1'>wellington</p></td><td class='head3' rowspan="2"><?php echo $_SESSION['dispname'];?> Signed in as: <?php echo $_SESSION['who'];?><br><a href='SignOut.php' class='wt'>Sign Out</a><br><a href='PasswordChange' class='wt'>Change Password</a></td><td class='head4'></td></tr>
<tr><td class='tdhead1'><p class='headp2'>gliding club operations</p></td></tr>
</table>
</div>
<?php
$DEBUG=0;
$diagtext="";
$con=mysqli_connect("127.0.0.1","admin","Checkers305","gliding");
if (mysqli_connect_errno())
{
   echo "<p>Unable to connect to database</p>";
   exit();
}
$org=0;
if (isset($_SESSION['org']))
 $org = $_SESSION['org'];
$classignore1 = getShortTermClass($con,$org);
$classignore2 = getNonFlyingClass($con,$org);
$dtNow = new DateTime();

$class = '';
$mstatus = '';
$done1 = false;
echo "<h1>Members with valid Medical</h1>";
echo "<table>";
$q= "SELECT members.displayname , a.class , b.status_name , members.id FROM members LEFT JOIN  membership_class a on a.id = members.class LEFT JOIN membership_status b on b.id = members.status where members.org = " . $org . " and b.status_name = 'Active' and medical_expire > '".$dtNow->format('Y-m-d 00:00:00') ."' order by members.surname";
$r = mysqli_query($con,$q);
while($row = mysqli_fetch_array($r) )
{
    if ($row[1] != 5 && $row[1] != 6 && $row[1] != 31 && $row[1] != 32)
    {
         echo "<tr><td><a href='Member?id=".$row['id']."'>" .$row['displayname']. "</a></td></tr>";
    }
}   
echo "</table>";

echo "<h1>Members with valid BFR</h1>";
echo "<table>";
$q= "SELECT members.displayname , a.class , b.status_name , members.id FROM members LEFT JOIN  membership_class a on a.id = members.class LEFT JOIN membership_status b on b.id = members.status where members.org = " . $org . " and b.status_name = 'Active' and bfr_expire > '".$dtNow->format('Y-m-d 00:00:00') ."' order by members.surname";
$r = mysqli_query($con,$q);
while($row = mysqli_fetch_array($r) )
{
    if ($row[1] != 5 && $row[1] != 6 && $row[1] != 31 && $row[1] != 32)
    {
         echo "<tr><td><a href='Member?id=".$row['id']."'>" .$row['displayname']. "</a></td></tr>";
    }
}   
echo "</table>";


echo "<h1>Members gone solo with no record of medical</h1>";
echo "<table>";
$q= "SELECT members.displayname , a.class , b.status_name , members.id FROM members LEFT JOIN  membership_class a on a.id = members.class LEFT JOIN membership_status b on b.id = members.status where members.org = " . $org . " and gone_solo > 0 and medical_expire = 0 order by b.status_name , a.class , members.surname";
$r = mysqli_query($con,$q);
while($row = mysqli_fetch_array($r) )
{
     if ($mstatus != $row['status_name'])
     {
        echo "<tr><td colspan='3'>STATUS ".$row['status_name']."</td></tr>";
        $mstatus = $row['status_name'];

     }
     
     if ($class != $row['class'])
     {
        echo "<tr><td></td><td colspan='2'>CLASS ".$row['class']."</td></tr>";
        $class = $row['class'];
     }
     
     echo "<tr><td></td><td></td><td><a href='Member?id=".$row['id']."'>" .$row['displayname']. "</a></td></tr>";
}
echo "</table>";





$class = '';
$mstatus = '';

$dt = new DateTime('now');
echo "<h1>Members with expired medical</h1>";
echo "<table>";
$q= "SELECT members.displayname, medical_expire, a.class , b.status_name, members.id FROM members LEFT JOIN  membership_class a on a.id = members.class LEFT JOIN membership_status b on b.id = members.status where members.org = " . $org . " and medical_expire <> 0 and medical_expire < '".$dt->format('Y-m-d H:i:s')."' order by b.status_name , a.class , members.surname";
$r = mysqli_query($con,$q);
while($row = mysqli_fetch_array($r) )
{
   $dte = new DateTime($row[1]);
   $days = floor(($dt->getTimestamp() - $dte->getTimestamp()) / (3600*24));
   
     if ($mstatus != $row['status_name'])
     {
        echo "<tr><td colspan='3'>STATUS ".$row['status_name']."</td></tr>";
        $mstatus = $row['status_name'];

     }
     
     if ($class != $row['class'])
     {
        echo "<tr><td></td><td colspan='2'>CLASS ".$row['class']."</td></tr>";
        $class = $row['class'];
     }
   
   
     echo "<tr><td></td><td></td><td><a href='Member?id=".$row['id']."'>" .$row['displayname']. "</a></td><td>" .$dte->format('d/m/Y'). "</td><td>" .$days. " Days</td></tr>";
}
echo "</table>";

$class = '';
$mstatus = '';

echo "<h1>Members with QGP and no record of BFR</h1>";
echo "<table>";
$q= "SELECT members.displayname, a.class , b.status_name, members.id FROM members LEFT JOIN  membership_class a on a.id = members.class LEFT JOIN membership_status b on b.id = members.status where members.org = " . $org . " and qgp_number > 0 and bfr_expire = 0 order by b.status_name , a.class , members.surname";
$r = mysqli_query($con,$q);
while($row = mysqli_fetch_array($r) )
{
     if ($mstatus != $row['status_name'])
     {
        echo "<tr><td colspan='3'>STATUS ".$row['status_name']."</td></tr>";
        $mstatus = $row['status_name'];

     }
     
     if ($class != $row['class'])
     {
        echo "<tr><td></td><td colspan='2'>CLASS ".$row['class']."</td></tr>";
        $class = $row['class'];
     }
     
     echo "<tr><td></td><td></td><td><a href='Member?id=".$row['id']."'>" .$row['displayname']. "</a></td></tr>";
}
echo "</table>";

$class = '';
$mstatus = '';

echo "<h1>Members with expired BFR</h1>";
echo "<table>";
$q= "SELECT members.displayname, bfr_expire, a.class , b.status_name, members.id FROM members LEFT JOIN  membership_class a on a.id = members.class LEFT JOIN membership_status b on b.id = members.status where members.org = " . $org . " and bfr_expire <> 0 and bfr_expire < '".$dt->format('Y-m-d H:i:s')."' order by b.status_name , a.class , members.surname";
$r = mysqli_query($con,$q);
while($row = mysqli_fetch_array($r) )
{
   $dte = new DateTime($row[1]);
   $days = floor(($dt->getTimestamp() - $dte->getTimestamp()) / (3600*24));
   
     if ($mstatus != $row['status_name'])
     {
        echo "<tr><td colspan='3'>STATUS ".$row['status_name']."</td></tr>";
        $mstatus = $row['status_name'];

     }
     
     if ($class != $row['class'])
     {
        echo "<tr><td></td><td colspan='2'>CLASS ".$row['class']."</td></tr>";
        $class = $row['class'];
     }
     
   
   
   echo "<tr><td></td><td></td><td><a href='Member?id=".$row['id']."'>" .$row['displayname']. "</a></td><td>" .$dte->format('d/m/Y'). "</td><td>" .$days. " Days</td></tr>";
}
echo "</table>";	

echo "<button onclick='printit()' id='print-button'>Print Report</button>";
mysqli_close($con);

?>

<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
