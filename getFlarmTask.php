<?php
/*
    This task is spawned off from cron to look for tracking data of glders with flarms.
    All flarm data should be available from glidernet.org
    This task also checks Gliding New Zealand for any additional flarm data.
*/
require 'GlidingClass.php';
require 'ognClass.php';
require 'GlidingGNZClass.php';
require dirname(__FILE__) . '/includes/classGlidingDB.php';

$con_params = require( dirname(__FILE__) .'/config/database.php');
$con_params = $con_params['gliding'];
$DB = new GlidingDB($con_params);

//Curret time
$dt = new DateTime();

//Find out who is flying today
$myGlide = new Gliding('glidingops.com');
$flyingToday = $myGlide->getFlyingToday(1);

//Build a list of gliders
$gliderlist = array();

$flyNow = $flyingToday['data'] ['flying'];
$flyDone = $flyingToday['data'] ['completed'];
foreach ($flyNow as $f)
{
    if (strlen($f['glider']) > 0)
    {
        $g = strtoupper($f['glider']);
        if (!in_array($g,$gliderlist) )
        {
            array_push($gliderlist,$g);
        }
    }
}

foreach ($flyDone as $f)
{
    if (strlen($f['glider']) > 0)
    {
        $g = strtoupper($f['glider']);
        if (!in_array($g,$gliderlist) )
        {
            array_push($gliderlist,$g);
        }
    }
}

//Build a table of flarm codes
$flarmCode = array();

if (count($gliderlist) > 0)
{
    foreach ($gliderlist as $gld)
    {
        $r = $myGlide->getFlarmCode($gld);
        if ($r['meta'] ['status'] == "OK")
        {
            $ICAO = $r['data'] ['flarmcode'];
            if (strlen($ICAO) > 0)
            {
                $flarmCode[$gld] = $ICAO;
            }
        }
    }

    //Now source data for OGN
    $myOGN = new ogn();
    $rogn =  json_decode($myOGN->getCurrentFlarms(),true);
    $myGNZ = new GNZ('www.gliding.net.nz');

    $dtNZ = new DateTime();
    $dtNZ->setTimezone(new DateTimeZone('Pacific/Auckland'));
    $dayStr = $dtNZ->format('Y-m-d');

    foreach ($gliderlist as $gld)
    {
        $ICAO = '';
        if (array_key_exists($gld,$flarmCode))
            $ICAO = $flarmCode[$gld];
        if (strlen($ICAO) > 0)
        {
            echo "Have flarm code for " . $gld . " of ". $ICAO ." \n";
            if (array_key_exists($ICAO,$rogn) )
            {
                $p = $rogn[$ICAO];
                /*
                    As we can get this data later than its occurence, and we only get a time and no date
                    It is possibe, if we assume that its todays date, that it is in fact captured yeterday UTC.
                    So if the timestanp is more than say 10 minutes ahead of now, we can assume it was the day before.
                    The 10 minute window allows for clock sync issues.
                */
                $dtgps = new DateTime($dt->format('Y-m-d') . " " . $p['time']);
                if ($dtgps->getTimestamp() > ($dt->getTimestamp() + 3600*12))
                    $dtgps->setTimestamp($dtgps->getTimestamp() - 86400);
                $strTime =  $dtgps->format('Y-m-d H:i:s');
                $DB->createTrack(1,$gld,$strTime,0,$p['lat'],$p['lon'],$p['alt'],'FlarmOGN');
            }

            //Now check GNZ for same
            try
            {
                $gnzData = $myGNZ->getFlarmData($dayStr,$ICAO);
                $data = $gnzData['data'];
                foreach ($data as $r)
                {
                    $DB->createTrack(1,$gld,$r['thetime'],0,$r['lat'],$r['lon'],$r['alt'],'FlarmGNZ');
                }

            }
            catch (exception $e)
            {
                echo "Exception getting GNZ Data\n";
            }

        }
    }
}
?>