<?php session_start(); ?>
<?php
require './includes/classGlidingDB.php';
$con_params = require('./config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);

if(!isset($_SESSION['security']))
{
  header('Location: Login.php');
  die("Please logon");
}


$org = 0;
$tz = 'UTC';
if(isset($_SESSION['org'])) $tz=$_SESSION['org'];
if(isset($_SESSION['timezone'])) $tz=$_SESSION['timezone'];
$datetz = new DateTimeZone($tz);
$datetziutc = new DateTimeZone('UTC');
$heigh_threshold = 70;
$liftoff = 0;
$dtnow = new DateTime('now',$datetz);
$offset = $datetz->getOffset($dtnow);
error_log("Offset = {$offset}");

//$dt = new DateTime('2019-06-09 07:00:00');
$dt = new DateTime('now');
$dt->setTimestamp($dt->getTimestamp() + $offset);
$dtStartl = new DateTime($dt->format('Y-m-d 00:00:00'));
$dtEndl = new DateTime($dt->format('Y-m-d 23:59:59'));

$dtStart = new DateTime;
$dtEnd = new DateTime;
$dtStart->setTimestamp($dtStartl->getTimestamp() - $offset);
$dtEnd->setTimestamp($dtEndl->getTimestamp() - $offset);

$dtDay = $dtStart;
$dtDay->setTimestamp($dtDay->getTimestamp() + $offset);

$strGlider = '';

function buildrow($glider,$strt,$end)
{
    echo "<tr><td class='l'>{$glider}</td><td class='r'>{$strt->format('H:i')}</td><td class='r'>{$end->format('H:i')}</td></tr>";
}

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container {padding: 10px;}
#heading h1 {font-size: 14pt; color: #555}
#main {font-size: 10pt;}
#main h2 {font-size: 11pt; color: #666;}
#main th,td {padding-right: 40px;}
#main .r {text-align: right;}
#main .l {text-align: left;}
</style>
<script>
</script>
</head>
<body >
    <div id = 'container'>
        <div id='heading'>
            <h1>DETECTED FLIGHTS BASED ON GPS DATA TIMEZONE [<?php echo $tz?>]</h1>
        </div>
        <div id = 'main'>
            <h2><?php echo $dtDay->format('D jS M Y')?></h2>
            <table>
            <tr><th class='l'>GLIDER</th><th class='r'>TAKEOFF</th><th class='r'>LAND</th></tr>
            <?php
                error_log("All tracks from {$dtStart->format('Y-m-d H:i:s')}");
                error_log("All tracks to {$dtEnd->format('Y-m-d H:i:s')}");
                $r = $DB->allTracksForPeriod($dtStart->format('Y-m-d H:i:s'), $dtEnd->format('Y-m-d H:i:s'));
                $dtlift = null;
                $dtland = null;
                $track = null;
            
                while ($track = $r->fetch_array())
                {
                    if ($strGlider != $track['glider'])
                    {
                        $strGlider = $track['glider'];
                        if ($liftoff)
                        {
                            $dtland = new DateTime($track['point_time']);
                            $dtlift2 = $dtlift;
                            $dtlift2->setTimeZone($datetz);
                            $dtland2 = $dtland;
                            $dtland2->setTimeZone($datetz);
                            buildrow($strGlider,$dtlift2,$dtland2);
                        }
                        $liftoff = 0;
                        $land = 0;
                    
                    }
                    if ($liftoff == 0 && $track['altitude'] > $heigh_threshold)
                    {
                        $liftoff = 1;
                        $dtlift = new DateTime($track['point_time']);
                    }
                    else
                    if ($liftoff == 1 && $track['altitude'] < $heigh_threshold)
                    {
                        $liftoff = 0;
                        $dtland = new DateTime($track['point_time']);
                        $dtlift2 = $dtlift;
                        $dtlift2->setTimeZone($datetz);
                        $dtland2 = $dtland;
                        $dtland2->setTimeZone($datetz);
                        buildrow($strGlider,$dtlift2,$dtland2);
                    }
                }
                if ($liftoff && $track)
                {
                    $dtland = new DateTime($track['point_time']);
                    $dtlift2 = $dtlift;
                    $dtlift2->setTimeZone($datetz);
                    $dtland2 = $dtland;
                    $dtland2->setTimeZone($datetz);
                    buildrow($strGlider,$dtlift2);
                }

            ?>
            </table>
        </div>
    </div>
</body>
</html>