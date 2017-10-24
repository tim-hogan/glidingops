<?php
header('Content-Type: application/json');
include 'timehelpers.php';
include 'helpers.php';

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

$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
    returnError(null,1001,"Unable to open database");

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

function getFlyingNow($org)
{
    global $con;
    global $req;
    
    $ret = array();
    
    $dateTimeZone = new DateTimeZone(orgTimezone($con,$org));
    $dateTime = new DateTime("now", $dateTimeZone);
    $dateStr = $dateTime->format('Ymd');
    $dateTimeNow = new DateTime("now");
    $flightTypeGlider = getGlidingFlightType($con);
    
    $flying = array();
    
    $q= "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.start/1000) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".$org." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 and flights.land=0 order by flights.seq ASC";
    $r = mysqli_query($con,$q);
    if (!$r) 
    {
        error_log("SQL Error: " . mysqli_error($con) . " Q: " . $q);
        returnError($req,1003,"SQL Error");
    }
    while ($flight = mysqli_fetch_array($r))
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
    
    $q= "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.land-flights.start) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".$org." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 and flights.land>0 order by flights.seq ASC";
    $r = mysqli_query($con,$q);
    if (!$r) 
    {
        error_log("SQL Error: " . mysqli_error($con) . " Q: " . $q);
        returnError($req,1003,"SQL Error");
    }
    while ($flight = mysqli_fetch_array($r))
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
    global $con;
    global $req;
    
    $ret = array();
    $gl = $glider;
    $gl = trim($gl);
    
    if (strlen($gl) == 0)
        returnError($req,1004,"Invalid Glider Name Specified");
    
    if (strlen($gl) > 3)
       $gl = substr($gl,-3);
       
    $q = "select * from aircraft where rego_short = '" . $gl . "'";
    $r = mysqli_query($con,$q);
    if (!$r) 
    {
        error_log("SQL Error: " . mysqli_error($con) . " Q: " . $q);
        returnError($req,1003,"SQL Error");
    }   
    
    if ($aircraft = mysqli_fetch_array($r) )
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

?>