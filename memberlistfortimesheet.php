<?php
include 'helpers.php';
header('Content-type: text/xml');
if (isset($_GET['org']))
    $org = $_GET['org'];
else
   exit();
echo "<allmembers>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
$shorttermclass = getShortTermClass($con,$org);
$q1 = "SELECT * FROM members WHERE org = ".$org." order by displayname ASC";
$r1 = mysqli_query($con,$q1);
while ($row = mysqli_fetch_array($r1) )
{
   echo "<member><id>".$row['id']."</id><name>".$row['displayname']."</name></member>";
}
echo "</allmembers>";
mysqli_close($con);
?>
