<?php
header('Content-Type: application/json');
include 'timehelpers.php';
include 'helpers.php';

require './includes/classGlidingDB.php';
$con_params = require('./config/database.php'); 
$con_params = $con_params['gliding']; 
$DB = new GlidingDB($con_params);

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
                    
                    $DB->createTrack($aircraft['org'],$aircraft['rego_short'],$dgps->format('Y-m-d H:i:s'),sprintf("%03d",$tm % 1000),$lat,$lon,$alt);    
              
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
                
                $DB->createTrack($aircraft['org'],$aircraft['rego_short'],$dgps->format('Y-m-d H:i:s'),sprintf("%03d",intval($p['t'])%1000),$p['a'],$p['b'],$p['c']);                
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

//Start
if (!isset($_GET['r'])) 
    returnError(null,1000,"Invalid parameter");

$r = $_GET['r'];
$tok = strtok($r,"/");
if (strlen($tok) == 16) $key = $tok;
$req = strtok("/");
$reqValue1 =strtok("/"); 
$reqValue2 =strtok("/"); 
$reqValue3 =strtok("/"); 

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $result = array();
    switch (strtolower($req))
    {
    case 'flyingnow':
        getFlyingNow($reqValue1);
        break;
    case 'flarmcode':
        getFlarmCode($reqValue1);
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
    default:
        returnError($req,1000,"Invalid parameter");
        break;
    }     
}
?>