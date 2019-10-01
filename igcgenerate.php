<?php
include 'timehelpers.php';

require dirname(__FILE__) . '/includes/classGlidingDB.php';
require dirname(__FILE__) . '/includes/classTracksDB.php';
$con_params = require( dirname(__FILE__) .'/config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);
$DBArchive = new TracksDB($con_params['tracks']);

if (isset($_GET['flightid']))
{
    $flightid=$_GET['flightid'];
    $f = $DB->getFlightWithNames($flightid);
    if ($f)
    {
        $glider = $f['glider'];
        $org = $f['org'];
        $cords = $DB->getOrgLaunchCoords($org);
        $pic = $f['namePIC'];
        $p2 = $f['nameP2'];
        $strACPrefix = $DB->getOrgAircraftPrefix($org);
        $trDateStart = new DateTime();
        $trDateLand = new DateTime();
        $trDateStart->setTimestamp(intval(floor($f['start'] / 1000)));
        $trDateLand->setTimestamp(intval(floor($f['land'] / 1000)));

        $r1 = null;
        if ($DB->numTracksForFlight($trDateStart,$trDateLand,$glider) > 0)
            $r1 = $DB->getTracksForFlight($trDateStart,$trDateLand,$glider);
        else
        if ($DBArchive->numTracksForFlight($trDateStart,$trDateLand,$glider) > 0)
            $r1 = $DBArchive->getTracksForFlight($trDateStart,$trDateLand,$glider);

        if ($r1 && $r1->num_rows > 0)
        {
            header('Content-type: application/igc'); 
            echo "AXXXABCFLIGHT\r\n";
            echo "HFFXA035\r\n";
            $trT = new DateTime();
            $trT->setTimestamp(intval(floor($f['start'] / 1000)));
            echo "HFDTE" . $trT->format('dmy'). "\r\n";
            echo "HFPLTPILOTINCHARGE: " . $f['namePIC'] . "\r\n";
            if (strlen($f['nameP2']) > 0)
            echo "HFCM2CREW2: " . $f['nameP2'] . "\r\n";
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
            $plat = $cords['lat'];
            if ($plat < 0)
            $plat = -($plat);
            echo sprintf("%02d",floor($plat));
            $min = ($plat - floor($plat)) * 60;
            $sec = ($min - floor($min)) * 1000;
            echo sprintf("%02d",floor($min));
            echo sprintf("%03d",floor($sec));
            if ($cords['lat'] >= 0)
            echo "N";
            else
            echo "S";
            $plon = $cords['lon'];
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
 

            while ($track = $r1->fetch_array() )
            {
                echo "B";
                echo timeLocalSQL($track['point_time'],null,'His');
                $plat = floatval($track['lattitude']);
                if ($plat < 0)
                    $plat = -($plat);
                echo sprintf("%02d",floor($plat));
                $min = ($plat - floor($plat)) * 60;
                $sec = ($min - floor($min)) * 1000;
                echo sprintf("%02d",floor($min));
                echo sprintf("%03d",floor($sec));
                if (floatval($track['lattitude']) >= 0)
                echo "N";
                else
                echo "S";
                $plon = floatval($track['longitude']);
                echo sprintf("%03d",floor($plon));
                $min = ($plon - floor($plon)) * 60;
                $sec = ($min - floor($min)) * 1000;
                echo sprintf("%02d",floor($min));
                echo sprintf("%03d",floor($sec));
                echo "E";

                echo "A";
                echo "00000";
                echo sprintf("%05d",floor(floatval($track['altitude'])));
                echo "\r\n";
            }

            //echo the end
            echo "B";
            echo $trDateLand->format('His');
            $plat = $cords['lat'];
            if ($plat < 0)
                $plat = -($plat);
            echo sprintf("%02d",floor($plat));
            $min = ($plat - floor($plat)) * 60;
            $sec = ($min - floor($min)) * 1000;
            echo sprintf("%02d",floor($min));
            echo sprintf("%03d",floor($sec));
            if ($cords['lat'] >= 0)
            echo "N";
            else
            echo "S";
            $plon = $cords['lon'];
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
