<?php
require dirname(__FILE__) . '/includes/classGlidingDB.php';
$con_params = require( dirname(__FILE__) .'/config/database.php');
$DB = new GlidingDB($con_params['gliding']);

$org = 1;
$options = getopt("m:o:");

if (isset($options['o']))
{
    $org = $options['o'];
}

$dateTimeZone = new DateTimeZone($DB->getOrgTimezone(1));
$tz = $DB->getOrgTimezone(1);
$r = $DB->allTracksForOrgToday($org,' order by glider, point_time');
$glider = '';
$flights = array();
$flight = null;
$bstarted = false;
while ($track = $r->fetch_array())
{
    if ($glider != $track['glider'])
    {
        if (strlen($glider) > 0 && $flight)
        {
            array_push($flights,$flight);
            $flight = null;
        }
        $bstarted = false;
        $glider = $track['glider'];
    }


    if (!$bstarted && $track['altitude'] > 150)
    {
        $bstarted = true;
        $flight = array();
        $flight['glider'] = $track['glider'];
        $flight['start'] = $track['point_time'];
    }

    if ($bstarted && $track['altitude'] < 150)
    {
        $flight['end'] = $track['point_time'];
        array_push($flights,$flight);
        $flight = null;
        $bstarted = false;
    }
}

if ($bstarted && is_array($flight))
{
    array_push($flights,$flight);
}

function buildHTML($flights)
{
    global $dateTimeZone;

    $dtNow = new DateTime('now');
    $dtNow->setTimezone($dateTimeZone);
    
    $strRet = '';
    $strRet .= "<h1>LAUNCH AND LANDING BASED ON TRACKING FOR {$dtNow->format('D jS M Y')}</h1>";
    if (count($flights) != 0)
    {
        $strRet .=  "<p>ORDERED BY GLIDER THEN TIME</p>";
        $strRet .=  "<table>";
        $strRet .=  "<tr><th class='l'>GLIDER</th><th class='r'>LAUNCH</th><th class='r'>LAND</th></tr>";
        foreach($flights as $flight)
        {
            $dtStart = new DateTime($flight['start']);
            $dtStart->setTimezone($dateTimeZone);
            $strStart = $dtStart->format('H:i');
            $strEnd = '';
            if (isset($flight['end']))
            {
                $dtEnd = new DateTime($flight['end']);
                $dtEnd->setTimezone($dateTimeZone);
                $strEnd = $dtEnd->format('H:i');
            }
            $strRet .= "<tr><td>{$flight['glider']}</td><td class='r'>{$strStart}</td><td class='r'>{$strEnd}</td></tr>";
        }
        $strRet .=  "</table>";
    }
    else
    {
        $strRet .=  "<p>NO FLIGHTS DETECTED TODAY</p>";    
    }
    return $strRet;
}


function buildNonFinalised()
{
    global $DB;
    $txt = "";
    $r = $DB->query('select localdate from flights where finalised is null and org = 1 group by localdate');
    if ($r->num_rows > 0)
    {
        $txt = "<p>FOLLOWING ARE GOPS DATES WHERE THE DAY WAS NOT FINALISED.</p>";
        $txt .= "<table>";
        while ($flight = $r->fetch_array(MYSQLI_ASSOC))
        {
            $dt = substr($flight['localdate'],6,2) . "/" . substr($flight['localdate'],4,2) ."/" . substr($flight['localdate'],0,4);
            $txt .= "<tr><td>{$dt}</td></tr>";
        }
        $txt .= "</table>";
    }
    return $txt;
}

if (isset($options['m']))
{
    $v = buildHTML($flights);
    $v .= buildNonFinalised();

    $msg = "<html><head><style>h1 {font-size: 12pt;} .r {text-align: right;} .l {text-align: left;}</style></head><body>{$v}</body></html>";
    
    
    
    $headers = 'From: Gliding Operations <operations@glidingops.com>' . "\r\n" .
               'Reply-To: wgcoperations@gmail.com' . "\r\n" .
               'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

    mail($options['m'],"Daily Flight Times from Tracks",$msg,$headers,'-r operations@glidingops.com');
    exit();
}

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<title>GLIDER DAY TIMES</title>
<style>
body {font-family: Arial, Helvetica, sans-serif;font-size: 10pt;margin: 0;}
#main {margin: 20px;}
h1{color: #666; font-size: 12pt;}
p {color: #666;}
th, td {padding-right: 8px;}
.r {text-align: right;}
.l {text-align: left;}
</style>
</head>
<body>
    <div id="main">
        <?php
            $v = buildHTML($flights);
            $v .= buildNonFinalised();
            echo $v;
        ?>
    </div>
</body>

