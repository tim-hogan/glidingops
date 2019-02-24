<?php
require './includes/classGlidingDB.php';
$con_params = require('./config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);

$r = $DB->allTracks('order by point_time');
while ($t = $r->fetch_array())
{
    if (! $DB->haveFlight($t['point_time'],$t['glider']) )
    {
        $DB->deleteTrack($t['id']);    
    }
}
?>
