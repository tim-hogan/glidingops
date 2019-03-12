<?php
include 'timehelpers.php';
header('Content-type: text/xml');
require dirname(__FILE__) . '/includes/classGlidingDB.php';

$con_params = require( dirname(__FILE__) .'/config/database.php'); 
$con_params = $con_params['gliding'];
$DB = new GlidingDB($con_params);

$diagMsg = '';

function GetAllSpots($key)
{
  $ch = curl_init();
  $url = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/" . $key . "/message.xml";
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
function GetLastSpot($key)
{
  $ch = curl_init();
  $url = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/" . $key . "/latest.xml";
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    $org=$_GET['org'];
    if ($org < 1)
    {
        die("<getspotdata><error>No organisation specified</error></getspotdata>");
    }
 
    echo "<getspotdata>";
    $diagMsg .= "<org>{$org}</org>";
 
    $dtNow = new DateTime('now');

    //Who is flying today
    $r4 = $DB->allFlightsToday($org);
    while ($flight = $r4->fetch_array())
    {
        $diagMsg .= "<flying>{$flight['glider']}</flying>";
        $spot = $DB->getSpotByReg($org,$flight['glider']);
        if ($spot)
        {
            $diagMsg .= "<spotkey>{$spot['spotkey']}</spotkey>";
            $rxml = '';
            $doc = new DOMDocument();
            $dNow = new DateTime("now");
            $dLast = new DateTime($spot['lastreq']);
            $dLastFull = new DateTime($spot['lastlistreq']);
            if (($dNow->getTimestamp() - $dLastFull->getTimestamp()) >  $spot['polltimeall'])
            {
                //Get full list
                $rxml = GetAllSpots($spot['spotkey']);
                $DB->updateSpotLastListReq($org,$spot['rego_short']);
            }
            else
            if (($dNow->getTimestamp() - $dLast->getTimestamp()) >  $spot['polltimelast'])
            {
                //Get last
                $rxml = GetLastSpot($spot['spotkey']);
                $DB->updateSpotLastReq($org,$spot['rego_short']);
            }
            if (strlen($rxml) > 0)
            {
                if (!$doc->loadXML($rxml))
                {
                echo "<error>XML Parse Error</error></getspotdata>";
                exit();
                }
                $list = $doc->getElementsByTagName('message');
                foreach ($list as $message) 
                {
                    $id = $message->getElementsByTagName ('id')->item(0)->nodeValue;
                    $type = $message->getElementsByTagName ('messageType')->item(0)->nodeValue;
                    $timenum = $message->getElementsByTagName ('unixTime')->item(0)->nodeValue;
                    $lat = $message->getElementsByTagName ('latitude')->item(0)->nodeValue;
                    $lon = $message->getElementsByTagName ('longitude')->item(0)->nodeValue;
                    if ($type == "TRACK" || $type == "OK")
                    {
                        $dt = new DateTime();
                        $dt->setTimestamp($timenum);
                        $DB->createTrack($org,$flight['glider'],$dt->format('Y-m-d H:i:s'),0.0,$lat,$lon,0.0,'SPOT');
                    }
                } 
            }   
        }
    } 
}
echo "<diag>{$diagMsg}</diag>";
echo "</getspotdata>";
?>