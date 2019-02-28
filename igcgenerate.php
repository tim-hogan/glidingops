<?php
include 'timehelpers.php';
include 'helpers.php';

$db_params = require('./config/database.php');
$con_params = $db_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
$con2_params = $db_params['tracks'];
$con2=mysqli_connect($con2_params['hostname'],$con2_params['username'],$con2_params['password'],$con2_params['dbname']);
if (mysqli_connect_errno())
{
  error_log("Cannot open tracksarchive database"); 
  $con2 = null;
}

if (isset($_GET['flightid']))
{
 
 $flightid=$_GET['flightid'];
 $q = "SELECT flights.glider , a.displayname , b.displayname , (flights.start/1000) , (flights.land/1000), flights.org from flights LEFT JOIN members a ON a.id = flights.pic LEFT JOIN members b ON b.id = flights.p2 WHERE flights.id = " . $flightid;
 $r = mysqli_query($con,$q);
 if (mysqli_num_rows($r) > 0)
 {
   $row = mysqli_fetch_array($r);
   $glider = $row[0];
   $org = $row[5];
   
   $launchlat = getOrgDefautLaunchLat($con,$org);
   $launchlon = getOrgDefautLaunchLon($con,$org);


   $pic = $row[1];
   $p2 = $row[2];
   $strACPrefix = getOrgAircraftPrefix($con,$org);
   $trDateStart = new DateTime();
   $trDateLand = new DateTime();
   $trDateStart->setTimestamp(intval(floor($row[3])));
   $trDateLand->setTimestamp(intval(floor($row[4])));

//Determine what database
if (tracksforFlight($con,null,$glider,$trDateStart->format('Y-m-d H:i:s'),$trDateLand->format('Y-m-d H:i:s')) )
{
   $q1="SELECT lattitude,longitude,altitude, point_time from tracks where glider = '".$glider."' and point_time > '".$trDateStart->format('Y-m-d H:i:s')."' and point_time < '".$trDateLand->format('Y-m-d H:i:s')."' order by point_time ASC";
   $r1 = mysqli_query($con,$q1);
}
else
if (tracksforFlight(null,$con2,$glider,$trDateStart->format('Y-m-d H:i:s'),$trDateLand->format('Y-m-d H:i:s')) )
{
   $q1="SELECT lattitude,longitude,altitude, point_time from tracksarchive where glider = '".$glider."' and point_time > '".$trDateStart->format('Y-m-d H:i:s')."' and point_time < '".$trDateLand->format('Y-m-d H:i:s')."' order by point_time ASC";
   $r1 = mysqli_query($con2,$q1);
}
   if (mysqli_num_rows($r1) > 0)
   {


     header('Content-type: application/igc'); 
     echo "AXXXABCFLIGHT\r\n";
     echo "HFFXA035\r\n";
     $trT = new DateTime();
     $trT->setTimestamp(intval(floor($row[3])));
     echo "HFDTE" . $trT->format('dmy'). "\r\n";
     echo "HFPLTPILOTINCHARGE: " . $row[1] . "\r\n";
     if (strlen($row[2]) > 0)
        echo "HFCM2CREW2: " . $row[2] . "\r\n";
     else
        echo "HFCM2CREW2:\r\n";
     echo "HFGIDGLIDERID: " .$strACPrefix. "-" .$glider . "\r\n";
     echo "HFDTM100GPSDATUM: WGS-1984\r\n";
     echo "HFRFWFIRMWAREVERSION:1.0\r\n";
     echo "HFRHWHARDWAREVERSION:1.0\r\n";
     
     echo "HFFTYFRTYPE:bTraced,Phone\r\n";
     echo "HFGPS:PHONE,VAR,12,10000m\r\n";
     echo "HFPRSPRESSALTSENSOR:NONE,NONE,0m\r\n";

     //echo the start
     echo "B";
     echo $trDateStart->format('His');
     $plat = $launchlat;
     if ($plat < 0)
          $plat = -($plat);
     echo sprintf("%02d",floor($plat));
     $min = ($plat - floor($plat)) * 60;
     $sec = ($min - floor($min)) * 1000;
     echo sprintf("%02d",floor($min));
     echo sprintf("%03d",floor($sec));
     if ($launchlat >= 0)
        echo "N";
     else
        echo "S";
     $plon = $launchlon;
     echo sprintf("%03d",floor($plon));
     $min = ($plon - floor($plon)) * 60;
     $sec = ($min - floor($min)) * 1000;
     echo sprintf("%02d",floor($min));
     echo sprintf("%03d",floor($sec));
     echo "E";
     echo "A";
     echo "00000";
     echo sprintf("%05d",0);
     echo "\r\n";
 

     while ($row1 = mysqli_fetch_array($r1) )
     {
      echo "B";
      echo timeLocalSQL($row1[3],null,'His');
      $plat = $row1[0];
      if ($plat < 0)
          $plat = -($plat);
      echo sprintf("%02d",floor($plat));
      $min = ($plat - floor($plat)) * 60;
      $sec = ($min - floor($min)) * 1000;
      echo sprintf("%02d",floor($min));
      echo sprintf("%03d",floor($sec));
      if ($row1[0] >= 0)
        echo "N";
      else
        echo "S";
      $plon = $row1[1];
      echo sprintf("%03d",floor($plon));
      $min = ($plon - floor($plon)) * 60;
      $sec = ($min - floor($min)) * 1000;
      echo sprintf("%02d",floor($min));
      echo sprintf("%03d",floor($sec));
      echo "E";

      echo "A";
      echo "00000";
      echo sprintf("%05d",floor($row1[2]));
      echo "\r\n";
     }

     //echo the end
     echo "B";
     echo $trDateLand->format('His');
     $plat = $launchlat;
     if ($plat < 0)
          $plat = -($plat);
     echo sprintf("%02d",floor($plat));
     $min = ($plat - floor($plat)) * 60;
     $sec = ($min - floor($min)) * 1000;
     echo sprintf("%02d",floor($min));
     echo sprintf("%03d",floor($sec));
     if ($launchlat >= 0)
        echo "N";
     else
        echo "S";
     $plon = $launchlon;
     echo sprintf("%03d",floor($plon));
     $min = ($plon - floor($plon)) * 60;
     $sec = ($min - floor($min)) * 1000;
     echo sprintf("%02d",floor($min));
     echo sprintf("%03d",floor($sec));
     echo "E";
     echo "A";
     echo "00000";
     echo sprintf("%05d",0);
     echo "\r\n";

     echo "GDUMMYINFORMATIONFORSECURITYREASONS\r\n";
   }
   else
     echo "No track points for flight";
 }
 else
 {
   echo "Flight record does not exist";
 }
}
else
{
 echo "No Flight Specified";
}
?>
