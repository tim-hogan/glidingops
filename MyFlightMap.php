<?php
include 'timehelpers.php';
include 'geohelpers.php';
include 'helpers.php';

require dirname(__FILE__) . '/includes/classGlidingDB.php';
require dirname(__FILE__) . '/includes/classTracksDB.php';
$con_params = require( dirname(__FILE__) .'/config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);
$DBArchive = new TracksDB($con_params['tracks']);

$global_settings = require(dirname(__FILE__) . '/config/site.php'); 
$global_settings = $global_settings['globalSettings']; 	
?>
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
      src="https://maps.googleapis.com/maps/api/js?key=<?php echo $global_settings['mapKey'];?>">
</script>
<script type="text/javascript">
<?php

$flightid=0;
$diag="";
$org=1;


if (isset($_GET['flightid']))
{
    $flightid=$_GET['flightid'];
}

$flight = $DB->getFlightWithNames($flightid);
if (!$flight)
{
    echo "Invalid flight id";
    exit();
}

$organisation = $DB->getOrganisation($DB->getFlightOrg($flightid));
if (!$organisation)
{
    echo "Internal error, invalid organisation";
    exit();
}

$orgLat=$organisation['def_launch_lat'];
$orgLon=$organisation['def_launch_lon'];
$strTimeZone=$organisation['timezone'];

?>
var map;
var centreLat=<?php echo $orgLat;?>;
var centreLon=<?php echo $orgLon;?>;
var markers = [];

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

$pic = $flight['namePIC'];
$p2 = $flight['nameP2'];


$trDateStart = new DateTime($from);
$trDateLand = new DateTime($to);

$r = null;
if ($DB->numTracksForFlight($trDateStart,$trDateLand,$glider) > 0)
    $r = $DB->getTracksForFlight($trDateStart,$trDateLand,$glider);
else
if ($DBArchive->numTracksForFlight($trDateStart,$trDateLand,$glider) > 0)
    $r = $DBArchive->getTracksForFlight($trDateStart,$trDateLand,$glider);

$alts=array();
$pttimes=array();
$idx=0;

echo "var flightCoordinates = [";
while ($track = $r->fetch_array())
{
 $altft = 3.281 * $track['altitude'];
 if ($maxalt < $altft)
    $maxalt = $altft;
 if ($bDone>0)
 {
      $dist = DistKM($track['lattitude'],$track['longitude'],$lastlat,$lastlon);
      $totdist = $totdist + $dist;
      $ts = timestampSQL($track['point_time']);
      $speeddist = $speeddist + $dist;
      if (($ts - $lastts) > 5)
      {
      	$speed = ($speeddist*1000) / ($ts - $lastts);
      	$lastts = $ts;
        $speeddist = 0.0;
      	if ($maxspeed < $speed)
      	{
	   $maxspeed = $speed;
           $maxspeedtm = $track['point_time'];
      	}
      }
      echo ",";
 }
 else
 {
     $totdist = $totdist + DistKM($track['lattitude'],$track['longitude'],$orgLat,$orgLon);	   
 }
 echo "new google.maps.LatLng(".$track['lattitude'].", ".$track['longitude'].")";
 $alts[$idx]=$track['altitude'];
 $pttimes[$idx]=timeLocalSQL($track['point_time'],$strTimeZone,"H:i:s");
 $idx++;
 $lastlat = $track['lattitude'];
 $lastlon = $track['longitude'];
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
</div>
<?php echo $diag; ?>
</body>
</html>
