<?php
require 'GlidingClass.php';
require 'ognClass.php';
require 'GlidingGNZClass.php';


$dt = new DateTime();
echo "Start " . $dt->format('H:i:s d/m/Y') . "\n";
echo "Get ready\n";

//This task runs every minute
$con_params = require('/var/www/html/config/database.php'); 
$con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);

echo "Database Open\n";

//Find out who is flying today
$myGlide = new Gliding('glidingops.com');
$flyingToday = $myGlide->getFlyingToday(1);


echo "Build list of gliders flying today\n";

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
                
                $q = "insert into tracks (org,glider,point_time,point_time_milli,lattitude,longitude,altitude) values (1,'".$gld."','".$dt->format('Y-m-d') . " " . $p['time']."',0,".$p['lat'].",".$p['lon'].",".$p['alt'].")";
                $r = mysqli_query($con,$q); 
                if (!$r)
                {
                    echo $dt->format('Y-m-d H:i:s') . " SQL Error: " . mysqli_error($con) . " Q: " . $q . "\n";
                }
                else
                    echo $dt->format('Y-m-d H:i:s') . " Insert into database \n";
            }
            
            //Now check GNZ for same
            try 
            {
                $gnzData = $myGNZ->getFlarmData($dayStr,$ICAO);
                $data = $gnzData['data'];
                foreach ($data as $r)
                {
                    $q = "insert into tracks (org,glider,point_time,point_time_milli,lattitude,longitude,altitude) values (1,'".$gld."','".$r['thetime'] ."',0,".$r['lat'].",".$r['lng'].",".$r['alt'].")";
                    $r = mysqli_query($con,$q); 
                    if (!$r)
                    {
                        if (mysqli_errno($con) != 1062)
                            echo $dt->format('Y-m-d H:i:s') . " SQL Error: " . mysqli_error($con) . " Q: " . $q . "\n";
                    }
                    else 
                        echo "GNZ Entry inserted into database\n";
                       
                }

            }
            catch (exception $e)
            {
                echo "Exception getting GNZ Data\n";
            }
            
        }  
    }  
}
else
    echo "Nobody Flying Today\n";
?>  
