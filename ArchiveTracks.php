<?php
$db_params = require('./config/database.php');
$con_params = $db_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 error_log("Unable to connect to database gliding");
 exit();
}

$con2_params = $db_params['tracks'];
$con2=mysqli_connect($con2_params['hostname'],$con2_params['username'],$con2_params['password'],$con2_params['dbname']);
if (mysqli_connect_errno())
{
 mysqli_close($con1);
 error_log("Unable to connect to database glidingtracks");
 exit();
}

$dtNow = new DateTime('now');
// Go back 3 days
$dtPrev = new DateTime();
$dtPrev->setTimestamp($dtNow->getTimestamp() - (3600*24*3));


$q = "SELECT org,glider,point_time,point_time_milli,lattitude,longitude,altitude,accuracy from tracks where point_time < '" . $dtPrev->format('Y-m-d') . "'";
$r = mysqli_query($con1,$q);  
while ($row = mysqli_fetch_array($r))
{
   $q1 = "INSERT INTO tracksarchive (org,glider,point_time,point_time_milli,lattitude,longitude,altitude,accuracy) VALUES (".$row[0].",'".$row[1]."','".$row[2]."',".$row[3].",".$row[4].",".$row[5].",".$row[6].",".$row[7].")";
   $r1 = mysqli_query($con2,$q1);  
}
$q = "DELETE from tracks where point_time < '" . $dtPrev->format('Y-m-d') . "'";
$r = mysqli_query($con1,$q); 
mysqli_close($con1);
mysqli_close($con2);
?>