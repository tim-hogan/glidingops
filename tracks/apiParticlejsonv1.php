<?php session_start();?>
<?php
header('Content-Type: application/json');
require './includes/geoHelpers.php';
//Database
require './includes/classtrackDB.php';
$con_params = require('./config/database.php'); $con_params = $con_params['48d5f377'];
$DB = new trackDB($con_params);

//Globals
$key = '';
$req = '';
$reqValue1 = '';
$reqValue2 = '';
$reqValue3 = '';
$apiversion = 1.0;

$dtNow = new DateTime('now');


//Diagnostic
function var_error_log( $object=null )
{
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log( $contents );        // log contents of the result of var_dump( $object )
}

function returnError($code)
{
   global $req;

   $res = createDefRet(0,$req);
   $error = array();
   $error['code'] = intval($code);
   $res['meta'] ['error'] =  $error;
   echo json_encode($res);
   exit();
}

function createDefRet($status,$request)
{
    global $apiversion;
    global $dtNow;

    $res = array();
    $meta = array();
    $meta['status'] = $status;
    $meta['request'] = $request;
    $meta['version'] = $apiversion;
    $meta['timestamp'] = $dtNow->format('Y-m-d') . "T" . $dtNow->format('H:i:s') . "Z";
    $res['meta'] = $meta;
    return $res;
}

function getSomething()
{

    $result = createDefRet(true,'something');
    $data = array();

    $result['data'] = $data;
    echo json_encode($result);
    exit();
}

function parseUDP($params)
{
    global $DB;

    if (isset($params['s']))
    {
        $s = $params['s'];
        $v = intval($s['v']);
        $seq = intval($s['seq']);
        $DB->updateVehilceSeq($v,$seq);
    }

    if (isset($params['b']))
    {
        $b = $params['b'];
        if (isset($b['l']))
        {
            $v = intval($b['v']);
            $l = floatval($b['l']);
            $seq = intval($b['u']);
            $DB->updateVehilceBattery($v,$l);
            $DB->updateVehilceSeq($v,$seq);
        }
        exit();
    }


    if (isset($params['n']))
    {
        $n = $params['n'];
        if (isset($n['v']))
        {
            $v = intval($n['v']);
            $seq = intval($n['u']);
            $DB->updateVehilceStatus($v,'nofix');
            $DB->updateVehilceSeq($v,$seq);
        }
        exit();
    }

    if (isset($params['p']))
    {
        $p = array();
        $p = $params['p'];
        if (isset($p['v']))
        {
            $v = $p['v'];
            $seq = intval($p['u']);
            if ($veh = $DB->getVehilce($v) )
            {
                $DB->updateVehilceSeq($v,$seq);
                $locs = array();
                $locs = $p['p'];

                //Create and array for point for GNZ
                $points = array();



                for($i = 0; $i < count($locs);$i++)
                {
                    $pos = $locs[$i];
                    $tm = 0;
                    $lat = 0;
                    $lon = 0;
                    $alt = 0;
                    $d = new DateTime('now');
                    if (isset($pos['t'])) $tm = intval($pos['t']);
                    if (isset($pos['lat'])) $lat = floatval($pos['lat']);
                    if (isset($pos['lon'])) $lon = floatval($pos['lon']);
                    if (isset($pos['alt'])) $alt = floatval($pos['alt']);
                    $DB->updateVehilceStatus($veh['idvehicle'],'recvpos');
                    $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                    $dgps->setTimestamp($dgps->getTimestamp() + ($tm/1000));


                    if ($dgps->getTimestamp() > ($d->getTimestamp() + 3600*12))  // We recevied this udp packet from capture to receive over UTC day change allow for 5 minutes clock unsync
                        $dgps->setTimestamp($dgps->getTimestamp() - (3600*24)  );

                    //We need to check that the this isnt an errored coordiante
                    $pointvalid = true;

                    //Are we in the geofence of NZ
                    if ($lat > -34.122426 || $lat < -47.524316)
                    {
                        $pointvalid = false;
                        error_log("Invalid GPS point not in geofence: {$veh['vehicle_rego_full']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon}");
                    }
                    if ($lon > 178.795690 || $lon < 166.219352)
                    {
                        $pointvalid = false;
                        error_log("Invalid GPS point not in geofence: {$veh['vehicle_rego_full']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon}");
                    }

                    if ($pointvalid)
                    {
                        //$tr = $DB->lastTrackForVehicle($veh['idvehicle']);
                        $tr = $DB->previousTrackForVehicle($veh['idvehicle'],$dgps->format('Y-m-d H:i:s'));
                        if ($tr)
                        {
                            if ($lat == $tr['track_lat'] && $lon == $tr['track_lon'])
                            {
                                $pointvalid = false;
                            }
                            else
                            {
                                $dist = DistKM($lat,$lon,$tr['track_lat'],$tr['track_lon']);
                                $t1 = $dgps->getTimestamp();
                                $t2 = new DateTime($tr['track_timestamp']);
                                $t2 = $t2->getTimestamp();
                                $diff = abs($t1-$t2);
                                if ($diff == 0)
                                    $pointvalid = false;
                                else
                                {
                                    $speed = ($dist*1000)/$diff;

                                    if ($speed > 69.0)
                                    {
                                        $pointvalid = false;
                                        error_log("Invalid GPS point detected Glider: {$veh['vehicle_rego_full']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon} Speed in m/s: {$speed} Threshhold: 69.0");
                                    }
                                }
                                //Now check height difference
                                $altdiff = abs($alt - $tr['track_alt']);
                                $speed = $altdiff/$diff;
                                if ($speed > 50.0)  //50 m/s
                                {
                                    $pointvalid = false;
                                    error_log("Invalid GPS point detected Glider: {$veh['vehicle_rego_full']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon} Altitude diff in m/s: {$speed} Threshhold: 50.0");
                                }
                            }
                        }
                    }

                    if ($pointvalid)
                    {
                        $ok = $DB->createTrack($veh['idvehicle'],$lat,$lon,$alt,$dgps->format('Y-m-d H:i:s') . '.' . sprintf("%03d",$tm%1000),true);

                        $point = array();
                        $point['utctime'] = $dgps->getTimestamp();
                        $point['lat'] = $lat;
                        $point['lon'] = $lon;
                        $point['alt'] = $alt;

                        array_push($points,$point);

                        //Now create a url to gliding ops
                        $json_data = array();
                        $json_data['org'] = 1;
                        $json_data['glider'] = $veh['vehicle_rego'];
                        $json_data['gpstime'] = $dgps->format('Y-m-d H:i:s');
                        $json_data['lat'] = $lat;
                        $json_data['lon'] = $lon;
                        $json_data['alt'] = $alt;
                        $data_string = json_encode($json_data);
                        $ch = curl_init('http://glidingops.com/api/v1/json/1234567890123456/createtrack');
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($data_string))  );

                        $result = curl_exec($ch);
                        curl_close($ch);
                    }


               }

                //Now create a url to gliding gnz
                if (count($points) > 0)
                {
                    $json_data = array();
                    $json_data['aircraft'] = $veh['vehicle_rego_full'];
                    $json_data['points'] = $points;
                    $data_string = json_encode($json_data);
                    $ch = curl_init('https://gliding.net.nz/api/v1/tracking/insert');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($data_string))  );
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
            }
        }
    }
}

function parseLoc($params)
{
    global $DB;
    $devid = '';
    $input = array();

    if (isset($params['coreid'])) $devid = $params['coreid'];
    if (isset($params['data'])) $input = json_decode($params['data'],true);

    //Look up database for glider
    if (strlen($devid) > 0)
    {
        if ($veh = $DB->getVehilceByDevId($devid) )
        {
            error_log(__FILE__ . " We have a vehilce {$veh['vehicle_name']}");
            //Update the vehicle status
            $d = new DateTime('now');

            if (isset($input['t']))  //Test echo
            {
                echo $input['t'];
                exit();
            }

            if (isset($input['b']))  //Battery
            {
                $status = $input['b'];
                $strDebug = "Vehilce Status [{$veh['idvehicle']}] ";

                if (isset($status['s']))
                    $strDebug .= "Last udp seq {$status['s']} ";
                if (isset($status['t']))
                    $strDebug .= "Last thread time {$status['t']} ";
                if (isset($status['u']))
                    $strDebug .= "Last udp recv device milli: {$status['u']} ";
                if (isset($status['usa']))
                    $strDebug .= "udp send count: {$status['usa']} ";
                if (isset($status['ucc']))
                    $strDebug .= "udp connect count: {$status['ucc']} ";
                if (isset($status['ucnr']))
                    $strDebug .= "udp cell not ready count: {$status['ucnr']} ";
                if (isset($status['use']))
                    $strDebug .= "udp send error count: {$status['use']} ";
                //error_log($strDebug);


                echo "seq," . $veh['vehicle_seq_complete'] . "," . $veh['idvehicle'];
                exit();
            }

            if (isset($input['n']))  //No Fix
            {
                $DB->updateVehilceStatus($veh['idvehicle'],'nofix');
                echo "seq," . $veh['vehicle_seq_complete'];
                exit();
            }
            if (isset($input['e']))  //Error from device
            {
                $e = $input['e'];
                error_log("Particle Device error for ".$veh['vehicle_name']." type = " . $e['t']);
            }
            if (isset($input['z']))
            {
                $p = array();
                $p = $input['z'];
                //var_error_log($p);

                //array for gnz points
                $gnzpoints = array();

                $seq = $p['s'];

                $points = $p['p'];
                //error_log("Received ". count($points) . " points");
                //error_log("Seq {$seq}");

                for ($i = 0;$i < count($points); $i++)
                {
                    $point = $points[$i];
                    $tm = $point['t'];
                    $lat = $point['l'];
                    $lon = $point['k'];
                    $alt = $point['j'];

                    $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                    $dgps->setTimestamp($dgps->getTimestamp() + ($tm/1000));

                    //error_log("Point decode = {$tm}/{$lat}/{$lon}/{$alt}");

                    //Check that the point is in the geofence
                    //We need to check that the this isnt an errored coordiante
                    $pointvalid = true;

                    //Are we in the geofence of NZ
                    if ($lat > -34.122426 || $lat < -47.524316)
                    {
                        $pointvalid = false;
                        error_log("Invalid GPS point not in geofence: {$veh['idvehicle']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon}");
                    }
                    if ($lon > 178.795690 || $lon < 166.219352)
                    {
                        $pointvalid = false;
                        error_log("Invalid GPS point not in geofence: {$veh['idvehicle']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon}");
                    }

                    if ($pointvalid)
                    {
                        $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                        $dgps->setTimestamp($dgps->getTimestamp() + ($tm/1000));

                        $tr = $DB->lastTrackForVehicle($veh['idvehicle']);
                        if ($tr)
                        {
                            if ($lat == $tr['track_lat'] && $lon == $tr['track_lon'])
                            {
                                $pointvalid = false;
                            }
                            else
                            {
                                $dist = DistKM($lat,$lon,$tr['track_lat'],$tr['track_lon']);
                                $t1 = $dgps->getTimestamp();
                                $t2 = new DateTime($tr['track_timestamp']);
                                $t2 = $t2->getTimestamp();
                                $diff = abs($t1-$t2);
                                if ($diff == 0)
                                    $pointvalid = false;
                                else
                                {
                                    $speed = ($dist*1000)/$diff;

                                    if ($speed > 69.0)
                                    {
                                        $pointvalid = false;
                                        error_log("Invalid GPS point detected Glider: {$veh['idvehicle']} Timestamp: {$dgps->format('Y-m-d H:i:s')} lat: {$lat} lon: {$lon} Speed in m/s: {$speed} Threshhold: 69.0");
                                    }
                                }
                            }
                        }
                    }


                    if ($pointvalid)
                    {
                        if ($i > 0)
                        {
                            //create track
                            $DB->updateVehilceStatus($veh['idvehicle'],'recvpos');
                            $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                            $dgps->setTimestamp($dgps->getTimestamp() + ($tm/1000));


                            if ($dgps->getTimestamp() > ($d->getTimestamp() + 3600*12))  // We recevied this udp packet from capture to receive over UTC day change allow for 5 minutes clock unsync
                                $dgps->setTimestamp($dgps->getTimestamp() - (3600*24)  );
                            $ok = $DB->createTrack($veh['idvehicle'],$lat,$lon,$alt,$dgps->format('Y-m-d H:i:s') . '.' . sprintf("%03d",$tm%1000),true);

                            $gnzpoint = array();
                            $gnzpoint['utctime'] = $dgps->getTimestamp();
                            $gnzpoint['lat'] = $lat;
                            $gnzpoint['lon'] = $lon;
                            $gnzpoint['alt'] = $alt;

                            array_push($gnzpoints,$gnzpoint);

                            //Now create a url to gliding ops
                            $json_data = array();
                            $json_data['org'] = 1;
                            $json_data['glider'] = $veh['vehicle_rego'];
                            $json_data['gpstime'] = $dgps->format('Y-m-d H:i:s');
                            $json_data['lat'] = $lat;
                            $json_data['lon'] = $lon;
                            $json_data['alt'] = $alt;
                            $data_string = json_encode($json_data);
                            $ch = curl_init('http://glidingops.com/api/v1/json/1234567890123456/createtrack');
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json',
                                    'Content-Length: ' . strlen($data_string))  );

                            $result = curl_exec($ch);
                            curl_close($ch);
                        }
                    }
                }

                //Now create a url to gliding gnz
                if (count($gnzpoints) > 0)
                {
                    $json_data = array();
                    $json_data['aircraft'] = $veh['vehicle_rego_full'];
                    $json_data['points'] = $gnzpoints;
                    $data_string = json_encode($json_data);
                    $ch = curl_init('http://gliding.net.nz/api/v1/tracking/insert');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($data_string))  );
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
                echo "z,{$seq},{$veh['idvehicle']},";
            }

            if (isset($input['p']))
            {
                $p = array();
                $p =$input['p'];
                $q = 0.0;
                if (isset($p['q']))
                    $q = $p['q'];
                $DB->updateVehilceStatus($veh['idvehicle'],'recvpos');
                $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                $dgps->setTimestamp($dgps->getTimestamp() + (intval($p['t'])/1000));
                if ($dgps->getTimestamp() > ($d->getTimestamp() + (3600*12)))  // We recevied this udp packet from capture to receive over UTC day change allow for 5 minutes clock unsync
                     $dgps->setTimestamp($dgps->getTimestamp() - (3600*24)  );
                $ok = $DB->createTrack($veh['idvehicle'],$p['a'],$p['b'],$p['c'],$dgps->format('Y-m-d H:i:s') . '.' . sprintf("%03d",intval($p['t'])%1000),false);
                if (!$ok)
                    error_log("UDP Update of Database Failed");

                //Now create a url to
                $json_data = array();
                $json_data['org'] = 1;
                $json_data['glider'] = $veh['vehicle_rego'];
                $json_data['gpstime'] = $dgps->format('Y-m-d H:i:s');
                $json_data['lat'] = $lat;
                $json_data['lon'] = $lon;
                $json_data['alt'] = $alt;
                $data_string = json_encode($json_data);
                $ch = curl_init('http://glidingops.com/api/v1/json/1234567890123456/createtrack');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                );

                $result = curl_exec($ch);
                curl_close($ch);

                echo "seq," . $veh['vehicle_seq_complete'] . "," . $veh['idvehicle'];
                exit();

            }
            if (isset($input['h']))
            {
                $version = floatval($input['h']);
                $DB->updateVehicleLastHello($veh['idvehicle'],$version);
                $DB->updateVehilceSeq($veh['idvehicle'],0);

                error_log("Hello from vechicle " . $veh['idvehicle'] . " Version " . $version . " Glider " . $veh['vehicle_rego']);

                if ($version >= 4.0)
                {
                    if ($version >= 5.7)
                    {
                        echo "hello," . $veh['idvehicle'] . "," . $veh['vehicle_delay_seconds'] . "," . $veh['vehicle_delay_seconds_no_movt'] . "," . $veh['vehicle_delay_seconds_no_fix'] . "," . $veh['vehicle_delay_seconds_low_bat'] . "," . $veh['vehicle_delay_seconds_publish'] . ","  . $veh['vehicle_delay_seconds_status'] . ",". $veh['vehilce_udp_address']  . "," . $devid . ",1";
                    }
                    else
                    {
                        echo "hello," . $veh['idvehicle'] . "," . $veh['vehicle_delay_seconds'] . "," . $veh['vehicle_delay_seconds_no_movt'] . "," . $veh['vehicle_delay_seconds_no_fix'] . "," . $veh['vehicle_delay_seconds_low_bat'] . "," . $veh['vehicle_delay_seconds_publish'] . ","  . $veh['vehicle_delay_seconds_status'] . ",". $veh['vehilce_udp_address']  . ",0";
                    }
                }
                else
                    echo "hello," . $veh['idvehicle'] . "," . $veh['vehicle_delay_seconds'] . "," . $veh['vehicle_delay_seconds_no_movt'] . "," . $veh['vehicle_delay_seconds_no_fix'] . "," . $veh['vehicle_delay_seconds_low_bat'] . "," . $veh['vehicle_delay_seconds_publish'] . "," . $veh['vehilce_udp_address']  . ",0";

                exit();
            }
            echo "1";
            exit();
        }
        else
        {
            error_log(__FILE__ . " ERROR no vehilce for device id  {$devid}");

        }

    }
    echo "0";
    exit();
}

//Start
//Check the key first
$result = array();


if (!isset($_GET['r']))
    returnError(1000);

$r = $_GET['r'];
$req = strtok($r,"/");
$reqValue1 =strtok("/");
$reqValue2 =strtok("/");
$reqValue3 =strtok("/");


if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $result = array();
    switch ($req)
    {
    case 'something':
        getSomething();
        break;
    default:
        returnError(1000);
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT'  || $_SERVER['REQUEST_METHOD'] == 'POST')
{

    $contents = file_get_contents('php://input');
    $params = array();
    //error_log("Dump or Raw Params");
    //var_error_log($contents);
    $params = json_decode($contents,true);

    switch ($req)
    {
    case 'loc':
        //error_log("TCP: {$contents}");
        parseLoc($params);
        break;
    case 'udploc':
        //error_log("UDP: {$contents}");
        parseUDP($params);
        break;
    default:
        returnError(1000);
        break;
    }
}
?>