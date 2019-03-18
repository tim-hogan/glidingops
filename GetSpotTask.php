<?php
/* 
    GetSpotTask.php
    ===============
    
    Task run from CRON that will periodically check locations from spots

    Usegae:
        php GetSpotTask.php -o <org>
*/
require dirname(__FILE__) . '/includes/classGlidingDB.php';

$con_params = require( dirname(__FILE__) .'/config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);

$org = 0;

$options = getopt("o:");
if (isset($options['o'])) 
    $org = $options['o'];
else
{
    echo "Error: No organistaion specified\nUseage: php GetSpotTask.php -o <org>";
    exit(-1);
}


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

//Who is flying today
$r = $DB->allFlightsToday($org);
while ($flight = $r->fetch_array())
{
    $spot = $DB->getSpotByReg($org,$flight['glider']);
    if ($spot)
    {
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
                echo "Error: In loadXML";
                exit(-1);
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
?>