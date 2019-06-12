<?php
require './includes/classGlidingDB.php';
$con_params = require('./config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);
$org = 0;
$tz = 'UTC';
if(isset($_SESSION['org'])) $tz=$_SESSION['org'];
if(isset($_SESSION['tz'])) $tz=$_SESSION['tz'];
$datetz = new DateTimeZone($tz);
$datetziutc = new DateTimeZone('UTC');
$heigh_threshold = 70;

function buildrow($glider,$strt,$end)
{
    echo "<tr><td>{$glider}</td><td>{$strt->format('H:i')}</td><td>{$end->format('H:i')}</td></tr>";
}

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<script>
</script>
</head>
<body >
    <div id = 'container'>
        <h1>DETECTED FLIGHTS BASED ON GPS DATA FOR TIMEZONE <?php echo $tz;?> and ORG <?php echo $org;?></h1>
        <table>
        <?php
            $dt = new DateTime('now',$datetz);
            $dtStart = new DateTime($dt->format('Y-m-d 00:00:00'),$datetz);
            $dtEnd = new DateTime($dt->format('Y-m-d 23:59:59'),$datetz);
            $dtStart->setTimezone($datetziutc);
            $dtEnd->setTimezone($datetziutc);
            $r = $DB->allTracksForPeriod($dtStart->format('Y-m-d H:i:s'), $dtStart->format('Y-m-d H:i:s'));
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
                        $dtlift2->setTimeZone($tz);
                        $dtland2 = $dtland;
                        $dtland2->setTimeZone($tz);
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
                    $dtlift2->setTimeZone($tz);
                    $dtland2 = $dtland;
                    $dtland2->setTimeZone($tz);
                    buildrow($strGlider,$dtlift2,$dtland2);
                }
            }
            if ($liftoff && $track)
            {
                $dtland = new DateTime($track['point_time']);
                $dtlift2 = $dtlift;
                $dtlift2->setTimeZone($tz);
                $dtland2 = $dtland;
                $dtland2->setTimeZone($tz);
                buildrow($strGlider,$dtlift2);
            }

        ?>
        </table>
    </div>
</body>
</html>