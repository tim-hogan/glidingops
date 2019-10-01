<?php
require dirname(__FILE__) . '/includes/classGlidingDB.php';
require dirname(__FILE__) . '/includes/classTracksDB.php';
$con_params = require( dirname(__FILE__) .'/config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);
$DBArchive = new TracksDB($con_params['tracks']);

$f = $DB->getFlightWithNames(13655);
$start = $f['start'] / 1000;
$dt = new DateTime();
$dt->setTimestamp($start);
echo $dt->format('Y-m-d H:i:s');
echo "\n";
$cords = $DB->getOrgLaunchCoords(1);
var_dump($cords);

?>