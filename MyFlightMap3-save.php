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

function Bearing($lat1,$lon1,$lat2,$lon2)
{
  $d2r = 3.141592653585 / 180.0;
  $lat1 = $lat1*$d2r;
  $lon1 = $lon1*$d2r;
  $lat2 = $lat2*$d2r;
  $lon2 = $lon2*$d2r;

  $angle = - atan2( sin( $lon1 - $lon2 ) * cos( $lat2 ), cos( $lat1 ) * sin( $lat2 ) - sin( $lat1 ) * cos( $lat2 ) * cos( $lon1 - $lon2 ) );
  if ( $angle < 0.0 ) $angle  += 3.141592653585 * 2.0;
  if ( $angle > 3.141592653585 ) $angle -= 3.141592653585 * 2.0; 
  return $angle;
}

function DestLat($lat,$lon,$b,$r)
{
  $RR = 6378137.0;// earth's mean radius in meters
  return asin( sin($lat)*cos($r/$RR) + cos($lat)*sin($r/$RR)*cos($b) );
}

function DestLon($lat,$lon,$b,$r)
{
  $RR = 6378137.0;// earth's mean radius in meters
  return $lon + atan2(sin($b)*sin($r/$RR)*cos($lat), 
                             cos($r/$RR)-sin($lat)*sin($lat));
}

function drawArc($cenLat,$cenLon,$startLat,$startLon,$endLat,$endLon,$dist,$dir)
{
    $dlat=0;
    $dlon=0;
    $deltaBearing=0;
    $numPoints = 32;
    $r2d = 180.0 / 3.141592653585;
    $d2r = 3.141592653585 / 180.0;
    $be1 = Bearing($cenLat,$cenLon,$startLat,$startLon);
    $be2 = Bearing($cenLat,$cenLon,$endLat,$endLon);
    $dist = $dist * 1852;
    if ($dir==0)  //Clockwise
    {
     if ($be1 > $be2) $be2 += (2.0*3.141592653585);
     $deltaBearing = $be2 - $be1;
     $deltaBearing = $deltaBearing/$numPoints;
    }
    else
    {
     if ($be2 > $be1) $be1 += (2.0*3.141592653585);
     $deltaBearing = $be1 - $be2;
     $deltaBearing = $deltaBearing/$numPoints;
    }
    $done1 = 0;
    for ($i=0; ($i < $numPoints+1); $i++) 
    { 
      if ($done1 == 1)
         echo ",";
    if ($dir==0)  //Clockwise
    {
      $dlat = DestLat($cenLat*$d2r,$cenLon*$d2r,$be1 + $i*$deltaBearing,$dist) * $r2d;
      $dlon = DestLon($cenLat*$d2r,$cenLon*$d2r,$be1 + $i*$deltaBearing,$dist) * $r2d;
    }
    else
    {
      $dlat = DestLat($cenLat*$d2r,$cenLon*$d2r,$be1 - $i*$deltaBearing,$dist) * $r2d;
      $dlon = DestLon($cenLat*$d2r,$cenLon*$d2r,$be1 - $i*$deltaBearing,$dist) * $r2d;
    }
      echo "new google.maps.LatLng(".$dlat.", ".$dlon.")";
      $done1 = 1;
    } 

}


$diag="";
$org=1;
$count_airspace = 0;
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
var piePoly = [];



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

$bDone=0;
$bDoneSpace=0;
$idx = 0;
$asheights=array();
echo "var airspace = [";
$q="SELECT id, Lower_height from airspace where type = 'cta'";
$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
  $asheights[$idx] = $row[1];
  $idx += 1;
  $count_airspace += 1;
  $firstlat = 0;
  $firstlon = 0;
  $nextCCA = 0;
  $nextCWA = 0;
  $bDone=0;
  $arc1 = 0;
  $arc2 = 0;
  $arc3 = 0;
  $arc4 = 0;
  $arc5 = 0;
  if ($bDoneSpace==1)
     echo ",";
  echo "[";
  $q1="SELECT type,lattitude,longitude,arclat,arclon,arcdist from airspacecoords where airspace = " .$row[0]. " order by seq";
  $r1 = mysqli_query($con,$q1);
  while ($row1 = mysqli_fetch_array($r1))
  {
        

    if ($firstlat == 0 && $firstlon == 0)
    {
        $firstlat = $row1[1];
	$firstlon = $row1[2];
    }
    if ($bDone==1)
        echo ",";
    if ($nextCCA == 1 || $nextCWA == 1)
    {
        if ($nextCCA == 1)
             drawArc($arc1,$arc2,$arc3,$arc4,$row1[1],$row1[2],$arc5,1);
        else
             drawArc($arc1,$arc2,$arc3,$arc4,$row1[1],$row1[2],$arc5,0);

        $nextCCA=0;
        $nextCWA=0;
    }
    else
    {
       if (strtoupper($row1[0]) == 'GRC')
           echo "new google.maps.LatLng(".$row1[1].", ".$row1[2].")";
       if (strtoupper($row1[0]) == 'CCA')
       {
         $nextCCA = 1;
         $arc1 =  $row1[3];
         $arc2 =  $row1[4];
         $arc3 =  $row1[1];
         $arc4 =  $row1[2];
         $arc5 =  $row1[5];
       }
       if (strtoupper($row1[0]) == 'CWA')
       {
         $nextCWA = 1;
         $arc1 =  $row1[3];
         $arc2 =  $row1[4];
         $arc3 =  $row1[1];
         $arc4 =  $row1[2];
         $arc5 =  $row1[5];
       }
    }
    if ($nextCCA == 1 || $nextCWA == 1)
         $bDone=0;
    else
         $bDone=1;
  }
  if ($nextCCA == 1 || $nextCWA == 1)
  {
     if ($bDone == 1)
        echo ",";
     if ($nextCCA == 1)
             drawArc($arc1,$arc2,$arc3,$arc4,$firstlat,$firstlon,$arc5,1);
        else
             drawArc($arc1,$arc2,$arc3,$arc4,$firstlat,$firstlon,$arc5,0);
  }
  else
  {
   if ($firstlat != 0 || $firstlon != 0)
   {
     if ($bDone == 1)
        echo ",";
     echo "new google.maps.LatLng(".$firstlat.", ".$firstlon.")";
   }
  }
 echo "]";
 $bDoneSpace=1;
}
echo "];";

$bDone = 0;
echo "var asheights = [";

for ($idx=0;$idx<count($asheights);$idx++)
{
 if ($bDone>0)
 	echo ",";
 echo $asheights[$idx];
 $bDone=1;
 
}
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
echo "var cnt_as = " . $count_airspace . ";";
?>
function colourPick(height)
{
 switch (height)
 {
 case 1500:
    return "#FF0000";
    break;
 case 2500:
    return "#FF8000";
    break;
 case 3500:
    return "#808000";
    break;
 case 4500:
    return "#00FF00";
    break;
 case 5500:
    return "#0000FF";
    break;
 case 7500:
    return "#0080FF";
    break;
 }
  
}

function AsOpt(node)
{
 if (node.value == 0)
 {
  node.value = 1;
  node.innerHTML = "Hide Airspace";
  for (var cnt=0;cnt<cnt_as;cnt++)
        {
                 piePoly[cnt] = new google.maps.Polygon({
                 paths: [airspace[cnt]],
                 strokeColor: "#000000",
                 strokeOpacity: 0.75,
                 strokeWeight: 1,
                 fillColor: colourPick(asheights[cnt]),
                 fillOpacity: 0.35,
                 map: map
            });
        }
 }
 else
 if (node.value == 1)
 {
  //Remove the poly lines
  for (i=0;i<piePoly.length;i++)
     piePoly[i].setMap(null);
  while (piePoly.length > 0)
    piePoly.pop();  

  node.value = 0;
  node.innerHTML = "Show Airspace";
 } 
}

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
<button id="BtAsk" onclick="AsOpt(this)" value='0'>Show Airspace</button>
<p id='debugtext'></p>
</div>
<?php echo $diag; ?>
</body>
</html>
