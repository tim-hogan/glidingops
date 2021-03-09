<?php session_start(); ?>
<?php
header('Content-Type: application/json');
include 'timehelpers.php';
include 'helpers.php';

require './includes/classGlidingDB.php';
require './includes/classTracksDB.php';
$con_params = require('./config/database.php');
$DB = new GlidingDB($con_params['gliding']);

//Diagnostic
function var_error_log( $object=null )
{
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log( $contents );        // log contents of the result of var_dump( $object )
}


/*
Repsonse format
meta:
status: OK/ERROR
request: <request made>
time:   <timestamp>
errorcode:  errorcode if error
errormsg:   error message if error
data:
<response data>
 */



//Globals
$key = '';
$req = '';
$reqValue1 = '';
$reqValue2 = '';
$reqValue3 = '';

//Functions
function newMetaResponseHdr($status,$req,$errorcode = null,$errormsg = null)
{
    $dt = new DateTime('now');
    $meta = array();
    $meta['status'] = $status;
    $meta['req'] = $req;
    $meta['time'] = $dt->format('Y-m-d') . "T" . $dt->format('H:i:s') . "Z";
    $meta['errorcode'] = $errorcode;
    $meta['errormsg'] = $errormsg;
    return $meta;
}

function newErrorMetaHdr($req,$errorcode,$errormsg)
{
    return newMetaResponseHdr('ERROR',$req,$errorcode,$errormsg);
}

function newOKMetaHdr($req)
{
    return newMetaResponseHdr('OK',$req);
}

function returnError($req,$code,$desc)
{
    $rslt = array();
    $meta = newErrorMetaHdr($req,$code,$desc);
    $data = array();
    $rslt['meta'] = $meta;
    $rslt['data'] = array();
    echo json_encode($rslt);
    exit();
}

/*
 ***********************************************************************
GET FUNCTIONS
 ***********************************************************************
 */
function getAllTableRecs($req,$tablename,$where,$order,$from,$to)
{
    global $DB;
    if (!isset($_SESSION['security']))
        returnError($req,1008,'Security Error');
    if ($_SESSION['security'] < 255)
        returnError($req,1008,'Security Error');
    $ed = 0;
    if ($from)
        $fm = intval($from);
    if ($to)
        $ed = intval($to);

    $where = trim($where);
    $order = trim($order);

    if (strlen($where) > 0 && strpos(strtoupper($where), 'WHERE') === false)
        $where = "where " . $where;
    if (strlen($order) > 0 && strpos(strtoupper($order), 'ORDER') === false)
        $order = "order by " . $order;

    $r = $DB->allFromTable($tablename,$where,$order);
    if ($r)
    {
        $fields = $r->fetch_fields();
        //We can unset some columns to reduce payload
        for ($idx = 0;  $idx < count($fields);$idx++)
        {
            unset($fields[$idx]->orgname);
            unset($fields[$idx]->table);
            unset($fields[$idx]->orgtable);
            unset($fields[$idx]->def);
            unset($fields[$idx]->db);
            unset($fields[$idx]->catalog);
        }

        $data = array();
        $data['table'] = $tablename;
        $data['fields'] = $fields;
        $rows = array();

        if ($fm > 0 && $fm < $r->num_rows)
        {
            $r->data_seek($fm);
        }
        if ($to == 0)
            $to = $r->num_rows -1;
        for ($idx = $fm; $idx <= $to;$idx++)
        {
            $row = $r->fetch_row();
            array_push($rows,$row);
        }
        $data['rows'] = $rows;

        $ret['meta'] = newOKMetaHdr($req);
        $ret['data'] = $data;

        echo json_encode($ret);
        exit();
    }
    else
        returnError($req,1007,"SQL Error");
}

function getFlyingNow($org)
{
    global $DB;
    global $req;

    $ret = array();
    $dateTimeNow = new DateTime("now");

    $flying = array();

    $r = $DB->flyingNow($org);
    while ($flight = $r->fetch_array())
    {
        $f = array();
        $f['seq'] = $flight[0];
        $f['glider'] = $flight[1];
        $f['pic'] = $flight[2];
        $f['p2'] = $flight[3];

        $elapsed = $dateTimeNow->getTimestamp() - $flight[4];
        $hours = intval($elapsed / 3600);
        $mins = intval(($elapsed % 3600) / 60);
        $timeval = sprintf("%02d:%02d",$hours,$mins);

        $f['flighttime'] = $timeval;
        array_push($flying,$f);
    }

    $completed = array();

    $r = $DB->completedToday($org);
    while ($flight = $r->fetch_array())
    {
        $f = array();
        $f['seq'] = $flight[0];
        $f['glider'] = $flight[1];
        $f['pic'] = $flight[2];
        $f['p2'] = $flight[3];

        $elapsed = $flight[4]/1000;
        $hours = intval($elapsed / 3600);
        $mins = intval(($elapsed % 3600) / 60);
        $timeval = sprintf("%02d:%02d",$hours,$mins);

        $f['flighttime'] = $timeval;

        array_push($completed,$f);
    }

    $ret['meta'] = newOKMetaHdr($req);
    $ret['data'] ['flying'] = $flying;
    $ret['data'] ['completed'] = $completed;

    echo json_encode($ret);
    exit();

}

function getFlarmCode($glider)
{
    global $DB;
    global $req;

    $ret = array();
    $gl = $glider;
    $gl = trim($gl);

    if (strlen($gl) == 0)
        returnError($req,1004,"Invalid Glider Name Specified");

    if (strlen($gl) > 3)
        $gl = substr($gl,-3);

    if ($aircraft = $DB->getAircraftByRegShort($gl) )
    {

        $ret['meta'] = newOKMetaHdr($req);
        $ret['data'] ['glider'] = $gl;
        $ret['data'] ['flarmcode'] = $aircraft['flarm_ICAO'];
    }
    else
        returnError($req,1005,"Invalid Aircraft");

    echo json_encode($ret);
    exit();
}

function getFlightData($flightid)
{
    global $DB;
    global $req;
    global $con_params;

    $ret = array();
    $data = array();

    if ($flight = $DB->getFlight($flightid) )
    {
        $ret['meta'] = newOKMetaHdr($req);
        //Get flight start and end times
        $st = new DateTime();
        $ed = new DateTime();

        $st->setTimestamp($flight['start'] / 1000);
        $ed->setTimestamp($flight['land'] / 1000);

        $data['aircraft'] = $flight['glider'];
        $data['start'] = $st->format('Y-m-d H:i:s');
        $data['land'] = $ed->format('Y-m-d H:i:s');
        //Look for map data
        $tracks = array();



        //Glider tracks
        $r = null;
        $data['track_count'] = 0;
        $numtracks = $DB->numTracksForFlight($st,$ed,$flight['glider']);
        if ($numtracks > 0)
        {
            $data['source'] = "gliding";
            $data['track_count'] = $numtracks;
            $r = $DB->getTracksForFlight($st,$ed,$flight['glider']);
        }
        else
        {
            $DBArchive = new TracksDB($con_params['tracks']);
            $r = $DBArchive->getTracksForFlight($st,$ed,$flight['glider']);
            if ($r->num_rows > 0)
            {
                $data['source'] = "archive";
                $data['track_count'] = $r->num_rows;
            }
        }

        while ($track = $r->fetch_array())
        {
            $ts = new DateTime($track['point_time']);
            $pos = array();
            $pos['lat'] = $track['lattitude'];
            $pos['lng'] = $track['longitude'];
            $pos['alt'] = $track['altitude'];
            $pos['time'] = $ts->getTimestamp();
            array_push($tracks,$pos);
        }
        $data['tracks'] = $tracks;
        $ret['data'] = $data;

        echo json_encode($ret);
        exit();

    }
    else
        returnError($req,1006,"No Flight");
}

/**
 * Summary of getTracks
 * @param mixed $req The text "tracks"
 * @param mixed $p1  Optional the organistaion number
 * @param mixed $p2  Optional the key word "TODAY"
 */
function getTracks($req,$p1,$p2)
{
    global $DB;

    $ret = array();
    $data = array();
    $tz = "UTC";
    $r = null;
    if (strlen($p1) > 0)
    {
        $tz = $DB->getOrgTimezone(intval($p1));
    }

    if (strtoupper($p2) == "TODAY")
    {
        $t1 = new DateTime('now');
        $t1->setTimezone(new DateTimeZone('PACIFIC/AUCKLAND'));
        $t1 = new DateTime($t1->format('Y-M-d 00:00:00'), new DateTimeZone('PACIFIC/AUCKLAND'));
        $t1->setTimezone(new DateTimeZone('UTC'));
        $strFrom = $t1->format('Y-m-d H:i:s');
        $r = $DB->allTracksForOrg($p1,'order by glider,point_time',$strFrom,null);
    }
    else
        $r = $DB->allTracksForOrg($p1,'order by glider,point_time');

    $ret['meta'] = newOKMetaHdr($req);
    $lastglider = '';
    while ($d = $r->fetch_array(MYSQLI_ASSOC))
    {
        if ($lastglider != $d['glider'])
        {
            $data[$d['glider']] = array();
            $lastglider = $d['glider'];
        }
        $track = array();
        $track['t'] = $d['point_time'];
        $track['lat'] = floatval($d['lattitude']);
        $track['lon'] = floatval($d['longitude']);
        $track['h'] = floatval($d['altitude']);
        array_push($data[$d['glider']],$track);
    }

    $ret['data'] = $data;
    echo json_encode($ret);
    exit();

}

function getTrackHeights($req,$p1,$p2)
{
    global $DB;

    $ret = array();
    $data = array();
    $tz = "UTC";
    $r = null;
    $t1 = null;

    if (strlen($p1) > 0)
    {
        $tz = $DB->getOrgTimezone(intval($p1));
    }
    $datetz = new DateTimeZone($tz);
    if (strtoupper($p2) == "TODAY")
    {
        $t1 = new DateTime('now');
        $t2 = new DateTime('now');
        $t1->setTimezone($datetz);
        $t2->setTimezone($datetz);
        $upto = intval($t1->format('H')) * 60 + intval($t1->format('i'));
        $t1 = new DateTime($t1->format('Y-M-d 08:00:00'), $datetz);
        $t2 = new DateTime($t2->format('Y-M-d 21:00:00'), $datetz);
    }
    else
    {
        $t1 = new DateTime($p2 .' 08:00:00', $datetz);
        $t2 = new DateTime($p2 .' 21:00:00', $datetz);
        $upto = intval($t2->format('H')) * 60 + intval($t2->format('i'));
    }

    $offset = floor(floatval($datetz->getOffset($t1)) / 3600.0);
    $t1->setTimezone(new DateTimeZone('UTC'));
    $t2->setTimezone(new DateTimeZone('UTC'));
    $strFrom = $t1->format('Y-m-d H:i:s');
    $strTo = $t2->format('Y-m-d H:i:s');
    $r = $DB->allTrackHeightsByMinForOrg($p1,$offset,$strFrom,$strTo);

    if ($r->num_rows == 0)
        returnError($req,1010,"No Data");

    $ret['meta'] = newOKMetaHdr($req);
    $lastglider = '';
    $lastforglider = 0;
    while ($d = $r->fetch_array(MYSQLI_ASSOC))
    {
        if ($lastglider != $d['glider'])
        {
            if (strlen($lastglider) > 0)
            {
                $glider = array();
                $glider['name'] = $lastglider;
                $glider['points'] = $points;
                $glider['last'] = $lastforglider;
                //$cntpts = count($points);
                //error_log("Push new glider {$lastglider} number of points {$cntpts}" );
                array_push($data,$glider);
            }
            $points = array();
            $lastglider = $d['glider'];
        }

        $points[intval($d['H']) * 60 + intval($d['M'])] = floatval($d['A']) * 3.28084;
        $lastforglider = intval($d['H']) * 60 + intval($d['M']);
    }


    if (strlen($lastglider) > 0)
    {
        $glider = array();
        $glider['name'] = $lastglider;
        $glider['points'] = $points;
        $glider['last'] = $lastforglider;
        //$cntpts = count($points);
        //error_log("Push new glider {$lastglider} number of points {$cntpts}" );
        array_push($data,$glider);
    }

    //Now create the big list
    $last = array();
    $lastcnt = array();
    $retdata = array();
    $hd = array();

    array_push($hd,['label'=>'Time','type' =>'timeofday']);

    for ($glidix = 0; $glidix < count($data);$glidix++)
    {
        array_push($hd,$data[$glidix] ['name']);
        $lastcnt[$data[$glidix] ['name']] = 0;
    }
    array_push($retdata,$hd);

    for ($hour = 8; $hour < 21;$hour++)
    {
        for ($min = 0; $min < 60; $min++)
        {

            if (($hour * 60) + $min < $upto)
            {
                $entry = array();
                $tod = array();
                array_push($tod,intval($hour),intval($min),0);
                array_push($entry,$tod);
                //Now for each glider
                for ($glidix = 0; $glidix < count($data);$glidix++)
                {
                    $points = $data[$glidix] ['points'];
                    if (isset($points[$hour*60+$min]))
                    {
                        array_push($entry,floatval($points[$hour*60+$min]));
                        $last[$data[$glidix] ['name']] = floatval($points[$hour*60+$min]);
                        $lastcnt[$data[$glidix] ['name']] = 0;
                    }
                    else
                    {
                        if (isset($last[$data[$glidix] ['name']]) && ($hour * 60) + $min < $data[$glidix] ['last'])
                        {
                            array_push($entry,floatval($last[$data[$glidix] ['name']]));
                            $lastcnt[$data[$glidix] ['name']] += 1;
                        }
                        else
                        {
                            array_push($entry,null);
                        }

                    }
                }
                array_push($retdata,$entry);
            }
        }
    }


    $ret['data'] = $retdata;
    echo json_encode($ret);
    exit();

}

/*
 ***********************************************************************
PUT AND POST FUNCTIONS
 ***********************************************************************
 */

function parseUDP($params)
{
    global $DB;
    error_log("Dump of params from parseUDP:");
    var_error_log($params);

    if (isset($params['b']))
    {
        $b = $params['b'];
        if (isset($b['l']))
        {
            $v = intval($b['v']);
            $l = floatval($b['l']);
            $DB->updateAircraftTrackBattery($v,$l);
        }
        exit();
    }


    if (isset($params['n']))
    {
        $n = $params['n'];
        if (isset($p['v']))
        {
            $v = intval($p['v']);
            $DB->updateAircraftTrackStatus($v,'nofix');
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
            if ($aircraft = $DB->getAircraft($v) )
            {
                $locs = array();
                $locs = $p['p'];
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

                    $DB->updateAircraftTrackStatus($v,'pos');

                    $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                    $dgps->setTimestamp($dgps->getTimestamp() + ($tm/1000));
                    if ($dgps->getTimestamp() > ($d->getTimestamp() + 300))  // We recevied this udp packet from capture to receive over UTC day change so back it up a day, allow 5 minutes for click sync issues.
                        $dgps->setTimestamp($dgps->getTimestamp() - (3600*24)  );

                    $DB->createTrack($aircraft['org'],$aircraft['rego_short'],$dgps->format('Y-m-d H:i:s'),sprintf("%03d",$tm % 1000),$lat,$lon,$alt,'Particle');

                }
            }
        }
    }
}

function parseLoc($params)
{
    global $DB;
    error_log("Dump of params from parseLoc:");
    var_error_log($params);

    $devid = '';
    $input = array();

    if (isset($params['coreid'])) $devid = $params['coreid'];
    if (isset($params['data'])) $input = json_decode($params['data'],true);

    error_log("Dump of params from parseLoc:");
    //Look up database for glider
    if (strlen($devid) > 0)
    {
        if ($aircraft = $DB->getAircraftByParticleId($devid) )
        {
            //Update the aircraft status
            $d = new DateTime('now');
            if (isset($input['n']))  //No Fix
            {
                $DB->updateAircraftTrackStatus($aircraft['id'],'nofix');
            }
            if (isset($input['e']))  //Error from device
            {
                $e = $input['e'];
                error_log("Particle Device error for ".$aircraft['registration']." type = " . $e['t']);
            }
            if (isset($input['p']))
            {
                $p = array();
                $p =$input['p'];
                $q = 0.0;
                if (isset($p['q']))
                    $q = $p['q'];
                $DB->updateAircraftTrackStatus($aircraft['id'],'pos');

                $dgps = new DateTime($d->format('Y-m-d 00:00:00'));
                $dgps->setTimestamp($dgps->getTimestamp() + (intval($p['t'])/1000));
                if ($dgps->getTimestamp() > ($d->getTimestamp() + 300))  // We recevied this udp packet from capture to receive over UTC day change so back it up a day, allow 5 minutes for click sync issues.
                    $dgps->setTimestamp($dgps->getTimestamp() - (3600*24)  );

                $DB->createTrack($aircraft['org'],$aircraft['rego_short'],$dgps->format('Y-m-d H:i:s'),sprintf("%03d",intval($p['t'])%1000),$p['a'],$p['b'],$p['c'],'Particle');
            }
            if (isset($input['h']))
            {
                $h = array();
                $h = $input['h'];
                $DB->updateAircraftTrackStatus($aircraft['id'],'hello');
                echo "hello," . $aircraft['id'] . "," . $aircraft['aircraft_track_timer_1'] . "," . $aircraft['aircraft_track_timer_2']  . "," . $aircraft['aircraft_track_timer_3']  . "," . $aircraft['aircraft_track_timer_4']  . "," . $aircraft['aircraft_track_timer_5']  . "," . $aircraft['aircraft_track_udp_server']. "," . $aircraft['aircraft_track_debug'];
                exit();
            }
            echo "1";
            exit();
        }
        else
            error_log("No aircraft for: " . $devid);
    }
    error_log("DeviceId = " . $devid);
    echo "0";
    exit();

}

function createTrack($params)
{
    global $DB;
    $org = 1;
    $glider = '';
    $gpstime = '1970-01-01 00:00:01';
    $lat = 0.0;
    $lon = 0.0;
    $alt = 0.0;

    if (isset($params['org'])) $glider = $params['org'];
    if (isset($params['glider'])) $glider = $params['glider'];
    if (isset($params['gpstime'])) $gpstime = $params['gpstime'];
    if (isset($params['lat'])) $lat = floatval($params['lat']);
    if (isset($params['lon'])) $lon = floatval($params['lon']);
    if (isset($params['alt'])) $alt = floatval($params['alt']);

    $DB->createTrack($org,$glider,$gpstime,0.0,$lat,$lon,$alt,'Particle');

    echo "0";
    exit();
}

function trackme($params)
{
    global $DB;

    error_log("Api from trackme follows");
    var_error_log($params);
    $org = 1;

    //Error checks
    if ($params && isset($params['deviceId']))
    {
        if ($aircraft = $DB->getAircraftBySpotId($params['deviceId']) )
        {
            if (isset($params['deviceSendDateTime']))
                $t = DateTime::createFromFormat(DateTime::ISO8601,$params['deviceSendDateTime']);
            else
                $t = new DateTime();   //Use now
            $alt = 0.0;
            if (isset($params['altitude']) && $params['altitude'])
                $alt = $params['altitude'];
            //Finaly check that we have a lat and lon;
            if (isset($params['latitude']) && isset($params['longitude']))
                $DB->createTrack($org,$aircraft['rego_short'],$t->format('Y-m-d H:i:s'),0.0,$params['latitude'],$params['longitude'],$alt,'NZSPOT');
            else
                error_log("trackme: No lat or lon supplied");
        }
        else
            error_log("trackme: Aircraft not found");
    }
    else
        error_log("trackme: invalid parameters");

    //Return all ok even if we did not use the data.
    $ret = array();
    $ret['messageId'] = $params['gatewayMessageId'];
    $ret['response'] = "OK";
    $ret['error'] = null;
    echo json_encode($ret);
    exit();
}

//Start
if (!isset($_GET['r']))
    returnError(null,1000,"Invalid parameter");

$r = $_GET['r'];
$tok = strtok($r,"/");
if (strlen($tok) == 16)
{
    $key = $tok;
    $req = strtok("/");
}
else
    $req = $tok;
$reqValue1 =strtok("/");
$reqValue2 =strtok("/");
$reqValue3 =strtok("/");
$reqValue4 =strtok("/");
$reqValue5 =strtok("/");

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $result = array();
    switch (strtolower($req))
    {
        case 'alltable':
            getAllTableRecs($req,$reqValue1,$reqValue2,$reqValue3,$reqValue4,$reqValue5);
            break;
        case 'flyingnow':
            getFlyingNow($reqValue1);
            break;
        case 'flarmcode':
            getFlarmCode($reqValue1);
            break;
        case 'flightdata':
            getFlightData($reqValue1);
            break;
        case 'tracks':
            getTracks($req,$reqValue1,$reqValue2);
            break;
        case 'trackheights':
            getTrackHeights($req,$reqValue1,$reqValue2);
            break;
        default:
            returnError($req,1002,"Invalid parameter");
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT'  || $_SERVER['REQUEST_METHOD'] == 'POST')
{

    $contents = file_get_contents('php://input');
    $params = array();
    $params = json_decode($contents,true);

    switch (strtolower($req))
    {
        case 'loc':
            parseLoc($params);
            break;
        case 'udploc':
            parseUDP($params);
            break;
        case 'createtrack':
            createTrack($params);
            break;
        case 'trackme':
            trackme($params);
            break;
        default:
            returnError($req,1000,"Invalid parameter");
            break;
    }
}
?>