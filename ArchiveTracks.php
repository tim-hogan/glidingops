<?php
/*
    Used to take the tracks dataset of the gliding database and archive tracks that are greater than 3 days old to a tracks archive database.
    This minimises the db size of the mian database.
    
    This routine can be called either from a web page or run using php from the command line.

    From apache as a web page: http://<hostname>/ArchiveTracks.php
    From a CRON Job:  php ArchiveTracks.php <route directory>
      Example: php ArchiveTracks.php /var/www/html
*/
$configDir = '.';
if (null != $argv && count($argv) > 1)
{
    $configDir = rtrim($argv[1],"/");
}
$db_params = require($configDir . '/config/database.php');
$con1_params = $db_params['gliding']; 
$con1=mysqli_connect($con1_params['hostname'],$con1_params['username'],$con1_params['password'],$con1_params['dbname']);
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
