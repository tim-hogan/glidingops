<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  header('Content-type: text/csv');
  $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
  if (mysqli_connect_errno())
  {
   echo "<p>Unable to connect to database</p>";
   exit();
  }

  $strDateFrom = $_POST["fromdate"];
  $strDateTo = $_POST["todate"];
  $dateStart2 = substr($strDateFrom,0,4) . substr($strDateFrom,5,2) . substr($strDateFrom,8,2);
  $dateEnd2 = substr($strDateTo,0,4) . substr($strDateTo,5,2) . substr($strDateTo,8,2);

  $org=$_POST["org"];
  echo "Glider " .$_POST['glider'] . ",,\r\n";
  
  echo "DATE,FLIGHT DURATION\r\n";
  $totalcnt = 0;
  $totaltime = 0;
  $q="SELECT flights.localdate, (flights.land-flights.start) from flights where flights.org = ".$org." and flights.glider = '".$_POST['glider']."' and localdate >= " . $dateStart2 . " and localdate <= " . $dateEnd2 . " order by localdate,seq";
  $r = mysqli_query($con,$q);
  while ($row = mysqli_fetch_array($r) )
  {
  	  $totalcnt = $totalcnt+1;
          $strdate=$row[0];
          echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);
	  echo ",";
          $duration = intval($row[1] / 1000);
          $hours = intval($duration / 3600);
          $mins = intval(($duration % 3600) / 60);
          $totMins = (($hours*60) + $mins);
          $totaltime = $totaltime + $totMins;

  	  $timeval = sprintf("%02d:%02d",$hours,$mins);

	  echo $timeval;
          echo "\r\n";
  }

  mysqli_close($con);

}
?>	
