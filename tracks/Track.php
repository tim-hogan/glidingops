<?php session_start();?>
<?php
//Database
require './includes/classtrackDB.php';
$con_params = require('./config/database.php'); $con_params = $con_params['48d5f377']; 
$DB = new trackDB($con_params);
$vehicle = null;
$vid = 0;
$tripid = 0;
$showmap = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['v']) )
    {
        $vid = $_GET['v'];
        if ($vid > 0)
            $vehicle = $DB->getVehilce($vid);
        if (isset($_GET['trip']) && null != $vehicle)
        {
            $tripid = $_GET['trip'];
            $showmap = true;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['vehicle'])) $vid = $_POST['vehicle'];
    if ($vid > 0)
        $vehicle = $DB->getVehilce($vid);
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<script type="text/javascript"
 src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBACKvSv3Dkose3DNn9rmvfCdJnEUwcGaE">
</script>
<script>
var map = null;
var markers = [];
<?php
    //Build locations
    $d1 = false;
    $lastd = null;
    $bend = false;
    echo "var paths = [";
    if ($vehicle && $showmap)
    {
        $r = $DB->allTracksForVehicleTrip($vid,$tripid);
        while ($t = $r->fetch_array())
        {
            if ($d1 && !$bend)
                echo ",";
            $d = new DateTime($t['track_timestamp']);
            $d->setTimezone(new DateTimeZone('Pacific/Auckland')); 
            if ($lastd)
            {
                if ( ($d->getTimestamp() - $lastd->getTimestamp() ) > 1200)
                    $bend = true;
            }
            if (!$bend)
            {
                echo "{lat:" . $t['track_lat'] . ", lng: " . $t['track_lon'] . ", t: \"" .$d->format('H:i:s d/m/Y'). "\", a:".$t['track_alt']."}";
                $d1 = true;
            }
            $lastd = $d;
        }
    }
    echo "];";
?>
function selectVehilce()
{
    document.getElementById('selform').submit();
}
function buildmap()
{
    if (paths.length > 0)
    {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -41.285161, lng: 174.774244}, 
            zoom: 10
        });
        
        path = new google.maps.Polyline({
            path: paths,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 3
        });
    
        path.setMap(map);
    }
}

function mapdisplaymarkers(bshow) {
    if (paths.length > 0 && bshow)
    {
        for (var i = 0; i < paths.length; i++)
        {
            var strTitle = "" + i + "\n" + paths[i] ['t'] + "\nALT: " + parseInt(parseFloat(paths[i] ['a']) * 3.28084);
            var marker = new google.maps.Marker(
                {
                    position: paths[i],
                    title: strTitle,
                    map: map
                }
            );
            markers.push(marker);
        }
    }
    if (!bshow)
    {
        for (var i = 0; i < markers.length; i++)
        {
            markers[i].setMap(null);
        }
        markers = [];
    }
}

function showmks(n) {
    mapdisplaymarkers(n.checked);
}
</script>
<style>
body {font-family: Arial, Helvetica, sans-serif;font-size: 9pt;}
#container {}
#main {}
#map {width: 600px; height: 500px;}
#details {float: left; padding: 10px;}
#maploc {float: left;}
</style>
</head>
<body onload='buildmap()'>
    <div id='container'>
        <div id='heading'>
        </div>
        <div id='main'>
            <div id='vehilces'>
                <form id='selform' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <p>SELECT VEHICLE</p>
                    <select name='vehicle' onchange='selectVehilce()'>
                        <option></option>
                        <?php
                            $r = $DB->allVehilces('order by vehicle_name');
                            while ($veh = $r->fetch_array())
                            {
                                echo "<option value='".$veh['idvehicle']."'>".$veh['vehicle_name']."</option>";
                            }
                        ?>
                    </select><br/>
                    <input id='showmarkers' type='checkbox' name='showmarkers' onchange='showmks(this)'/><span>SHOW MARKERS</span>
                </form>
            </div>
            <div id='details'>
                <?php
                if ($vehicle)
                {
                    
                    //Find last position
                    $track = $DB->lastTrackForVehicle($vehicle['idvehicle']);
                
                    echo "<table>";
                    echo "<tr><td>NAME:</td><td>".$vehicle['vehicle_name']."</td></tr>";
                    echo "<tr><td>PARTICLE ID:</td><td>".$vehicle['vehicle_particle_id']."</td></tr>";
                    $version = '';
                    if (!is_null($vehicle['vehicle_particle_version']))
                        $version = floatVal($vehicle['vehicle_particle_version']);
                    echo "<tr><td>PARTICLE VERSION:</td><td>{$version}</td></tr>";
                    $strLastHello = '';
                    $strBatt = '';
                    $strLastSeen = '';
                    if ($vehicle['vehicle_last_seen'])
                    {
                        $d2 = new DateTime($vehicle['vehicle_last_seen']);
                        $d2->setTimezone(new DateTimeZone('Pacific/Auckland')); 
                        $strLastSeen = $d2->format('D jS M Y H:i');
                    }
                    if ($vehicle['vehicle_last_hello'])
                    {
                        $d = new DateTime($vehicle['vehicle_last_hello']);
                        $d->setTimezone(new DateTimeZone('Pacific/Auckland'));
                        $strLastHello = $d->format('D jS M Y H:i');
                    }
                    if ($vehicle['vehicle_battery_timestamp'])
                    {
                        $dBatt = new DateTime($vehicle['vehicle_battery_timestamp']);
                        $dBatt->setTimezone(new DateTimeZone('Pacific/Auckland'));
                        $strBatt = $dBatt->format('D jS M Y H:i');
                        echo "<tr><td>BATTERY:</td><td>".sprintf("%3.1f%%",$vehicle['vehicle_battery_level'])." at {$strBatt}</td></tr>";
                    }
                    else
                        echo "<tr><td>BATTERY:</td><td></td></tr>";
                    echo "<tr><td>LAST HELLO:</td><td>{$strLastHello}</td></tr>";
                    echo "<tr><td>LAST SEEN:</td><td>{$strLastSeen}</td></tr>";
                    echo "<tr><td>LAST STATUS:</td><td>".$vehicle['vehicle_last_status']."</td></tr>";
                    if ($track)
                    {
                        $d1 = new DateTime($track['track_timestamp']);
                        $d1->setTimezone(new DateTimeZone('Pacific/Auckland')); 
                        echo "<tr><td>LAST KNOWN POSITION:</td><td>".$d1->format('D jS M Y H:i')."</td><td>".$track['track_lat'].",".$track['track_lon']."</td></tr>";
                    }
                    echo "<tr><td>LAST SEQUENCE COMPLETE:</td><td>{$vehicle['vehicle_seq_complete']}</td></tr>";
                    echo "</table>";
                    
                    //Select trips
                    echo "<table>";
                    if ($vehicle)
                    {
                        $starttrip = false;
                        $d = null;
                        $cnt = 0;
                        $r = $DB->allTracksForVehicleNoTrip($vehicle['idvehicle']);
                        while ($t = $r->fetch_array())
                        {
                            $tripid = 0;
                            //We need to find previous
                            $tprev = $DB->prevTrackForVehilce($t);
                            if ($tprev)
                            {
                                $dt1 = new DateTime($tprev['track_timestamp']);
                                $dt2 = new DateTime($t['track_timestamp']);
                                if (($dt2->getTimestamp() - $dt1->getTimestamp()) < (20 * 60))
                                    $tripid = $tprev['track_trip'];
                            }
                            
                            if ($tripid == 0)
                            {
                                $trip = $DB->createTrip($vehicle['idvehicle'],$t['track_timestamp']);
                                $tripid = $trip['idtrip'];
                            }
                            
                            if ($tripid != 0)
                                $DB->updateTrackTrip($t,$tripid);
                            
                        }
                        
                        $r = $DB->allTripsForVehilce($vid);
                        while ($trip = $r->fetch_array() )
                        {
                            $dt = new DateTime($trip['trip_start']);
                            $dt->setTimezone(new DateTimeZone('Pacific/Auckland'));
                            echo "<tr><td>{$trip['idtrip']}</td><td><a href='Track.php?v={$vid}&trip={$trip['idtrip']}'>{$dt->format('d/m/Y H:i')}</a></td></tr>";
                        }
                    }
                    echo "</table>";
                }
                ?>
            </div>
            <div id='maploc'>
                <div id='map'></div>
            </div>
        </div>
    </div>
</body>
</html>
