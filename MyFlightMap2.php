<!DOCTYPE html>
<html>
<head>
<style type="text/css">
html {height: 100%; margin: 0; padding: 0;} 
body {height: 100%; margin: 0; padding: 0;font-family: Arial, Helvetica, sans-serif; }
#maparea {float: right;height:75%; width: 70%;}
#map-canvas{ height: 100%; margin: 20px; padding: 0;box-shadow: 10px 10px 5px #888888;}
#head {background-color:#000040;}
p.headp1 {font-family: Calibri, Arial, Helvetica, sans-serif; margin:0;color:white;font-size:50px}
p.headp2 {font-family: Calibri, Arial, Helvetica, sans-serif; margin:0;color:white;font-size:20px}
td.tdhead1 {padding-left:0px;}
td.head3 {color:white;border-style: none;font-size:14px;font-family: Arial, Helvetica, sans-serif;text-align: right;vertical-align: top;}
td.head4 {width: 30px;border-style: none;}
h1 {font-size:20px;}
#textarea td {font-size: 14px;}
</style>
<script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBACKvSv3Dkose3DNn9rmvfCdJnEUwcGaE">
</script>
<script type="text/javascript">
<?php
include 'timehelpers.php';
include 'geohelpers.php';
include 'helpers.php';
$diag="";
$org=1;
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






$q="SELECT def_launch_lat,def_launch_lon, timezone from organisations where id = ".$org;
$r = mysqli_query($con,$q);
$row = mysqli_fetch_array($r);
$orgLat=$row[0];
$orgLon=$row[1];
$strTimeZone=$row[2];
?>
var map;
var centreLat=<?php echo $orgLat;?>;
var centreLon=<?php echo $orgLon;?>;
var markers = [];
var airspace = [
 new google.maps.LatLng(-40.68333333,175.1769444),
 new google.maps.LatLng(-40.76411111,175.1174167),
 new google.maps.LatLng(-40.94094444,175.0118333),
 new google.maps.LatLng(-40.9395,174.9934444),
 new google.maps.LatLng(-40.92652778,174.9141944),
 new google.maps.LatLng(-41.34319444,174.2635278),
 new google.maps.LatLng(-41.34447222,174.1282778),
 new google.maps.LatLng(-41.39188889,174.0468333),
 new google.maps.LatLng(-41.43955556,173.965),
 new google.maps.LatLng(-41.44441667,173.8818056),
 new google.maps.LatLng(-41.37172222,173.9409167),
 new google.maps.LatLng(-41.29611111,174.0021667),
 new google.maps.LatLng(-41.15411111,174.2003611),
 new google.maps.LatLng(-40.995,174.3136389),
 new google.maps.LatLng(-40.90483333,174.3058056),
 new google.maps.LatLng(-40.63161111,174.8754444),
 new google.maps.LatLng(-40.63161111,174.9966667)
];

<?php
$bDone=0;
$totdist = 0.0;
$lastlat = 0;
$lastlon = 0;
$lastts = 0;
$maxalt = 0;
$maxspeed = 0;
$maxspeedtm = 0;
$speeddist = 0;
$glider=$_GET['glider'];
$from=$_GET['from'];
$to=$_GET['to'];
$flightid=0;
$pic = '';
$p2 = '';
if (isset($_GET['flightid']))
{
  $flightid=$_GET['flightid'];
}
if ($flightid > 0)
{

 $q="SELECT a.displayname, b.displayname from flights LEFT JOIN members a ON a.id = flights.pic LEFT JOIN members b ON b.id = flights.p2 where flights.id = " . $flightid;
 $r = mysqli_query($con,$q);
 if (mysqli_num_rows($r) > 0)
 {
  $row = mysqli_fetch_array($r);
  $pic = $row[0];
  $p2 = $row[1];
 }
}

//Determine whcih source
if (tracksforFlight($con,null,$glider,$from,$to) )
{
 $q="SELECT lattitude,longitude,altitude, point_time from tracks where glider = '".$glider."' and point_time > '".$from."' and point_time < '".$to."' order by point_time ASC";
 $r = mysqli_query($con,$q);
}
else
if (tracksforFlight(null,$con2,$glider,$from,$to) )
{
 $q="SELECT lattitude,longitude,altitude, point_time from tracksarchive where glider = '".$glider."' and point_time > '".$from."' and point_time < '".$to."' order by point_time ASC";
 $r = mysqli_query($con2,$q);
}
$alts=array();
$pttimes=array();
$idx=0;


echo "var flightCoordinates = [";
while ($row = mysqli_fetch_array($r))
{
 $altft = 3.281*$row[2];
 if ($maxalt < $altft)
    $maxalt = $altft;
 if ($bDone>0)
 {
      $dist = DistKM($row[0],$row[1],$lastlat,$lastlon);
      $totdist = $totdist + $dist;
      $ts = timestampSQL($row[3]);
      $speeddist = $speeddist + $dist;
      if (($ts - $lastts) > 5)
      {
      	$speed = ($speeddist*1000) / ($ts - $lastts);
      	$lastts = $ts;
        $speeddist = 0.0;
      	if ($maxspeed < $speed)
      	{
	   $maxspeed = $speed;
           $maxspeedtm = $row[3];
      	}
      }
      echo ",";
 }
 else
 {
     $totdist = $totdist + DistKM($row[0],$row[1],$orgLat,$orgLon);	   
 }
 echo "new google.maps.LatLng(".$row[0].", ".$row[1].")";
 $alts[$idx]=$row[2];
 $pttimes[$idx]=timeLocalSQL($row[3],$strTimeZone,"H:i:s");
 $idx++;
 $lastlat = $row[0];
 $lastlon = $row[1];
 $bDone=1;
}
$totdist = $totdist + DistKM($lastlat,$lastlon,$orgLat,$orgLon);	  
echo "];";

$bDone = 0;
echo "var altitudes = [";
for ($idx=0;$idx<count($alts);$idx++)
{
 if ($bDone>0)
 	echo ",";
 echo $alts[$idx];
 $bDone=1;
 
}
echo "];";

$bDone = 0;
echo "var pttimes = [";
for ($idx=0;$idx<count($alts);$idx++)
{
 if ($bDone>0)
 	echo ",";
 echo "'" . $pttimes[$idx] . "'";
 $bDone=1;
 
}
echo "];";

?>
function MarkerOpt(node)
{
 console.log("Marker Option Value" + node.value);
 if (node.value == 0)
 {
   for (i=0;i<flightCoordinates.length;i++)
   {
     var strText =  "Pt: " + i.toString() + " " + pttimes[i];
     if (altitudes[i] > 0)
     {
       var ft=  3.281*altitudes[i];
       strText = strText + " Alt: " + ft.toString();
     }
     var marker = new google.maps.Marker({
        position: flightCoordinates[i],
        title: strText
        });
      markers.push(marker);
      marker.setMap(map);
    }

  node.value = 1;
  node.innerHTML = "Hide Markers";
 }
 else
 if (node.value == 1)
 {
  //Remove the markers
  for (i=0;i<markers.length;i++)
     markers[i].setMap(null);
  while (markers.length > 0)
    markers.pop();  


  node.value = 0;
  node.innerHTML = "Show Markers";
 }
}

function AirSpace(node)
{
 console.log("Marker Option Value" + node.value);
 if (node.value == 0)
 {
 	var flightPath = new google.maps.Polyline({
    		path: airspace,
    		geodesic: true,
    		strokeColor: '#00FF00',
    		strokeOpacity: 1.0,
    		strokeWeight: 2
  		});

  	flightPath.setMap(map);
}

function Start() 
{

       var mapOptions = {
          center: { lat: centreLat, lng: centreLon},
          zoom: 10
        };
        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
 	var flightPath = new google.maps.Polyline({
    		path: flightCoordinates,
    		geodesic: true,
    		strokeColor: '#FF0000',
    		strokeOpacity: 1.0,
    		strokeWeight: 2
  		});

  	flightPath.setMap(map);

}
google.maps.event.addDomListener(window, 'load', Start);
</script>
</head>
<body>
<div id='head'>
<table class='t1'>
<tr><td class='tdhead1'><p class='headp1'>wellington</p></td></tr>
<tr><td class='tdhead1'><p class='headp2'>gliding club operations</p></td></tr>
</table>
</div>
<div id='maparea'>
<div id="map-canvas"></div>
</div>
<div id='textarea'>
<h1>My Flight Track</h1>
<table>
<tr><td>GLIDER</td><td><?php echo $glider;?></td></tr>
<tr><td>START</td><td><?php echo timeLocalSQL($from,$strTimeZone,"D, j M Y H:i:s");?></td></tr>
<tr><td>END</td><td><?php echo timeLocalSQL($to,$strTimeZone,"D, j M Y H:i:s");?></td></tr>
<?php 
if ($flightid > 0)
{
 if (strlen($pic) > 0)
     echo "<tr><td>PILOT IN COMMAND</td><td>" . $pic . "</td></tr>";
 if (strlen($p2) > 0)
     echo "<tr><td>PILOT 2</td><td>" . $p2 . "</td></tr>";
}
?>
<tr><td>TOTAL DISTANCE</td><td><?php echo sprintf("%4.0f",$totdist);?> km</td></tr>
<tr><td>MAX HEIGHT</td><td><?php echo sprintf("%5.0f",$maxalt);?> ft</td></tr>
<tr><td>MAX GROUND SPEED</td><td><?php echo sprintf("%3.0f",$maxspeed*3.6);?> km/hr</td></tr>
</table>
<button id="BtMk" onclick="MarkerOpt(this)" value='0'>Show Markers</button>
<button id="BtAirSpace" onclick="AirSpace(this)" value='0'>Show Airspace</button>
</div>
<?php echo $diag; ?>
</body>
</html>
