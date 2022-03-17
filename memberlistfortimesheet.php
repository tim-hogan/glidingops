<?php
include './helpers/session_helpers.php';
session_start();
require_security_level(4);

include 'timehelpers.php';
include 'helpers.php';
header('Content-type: text/xml');

if (isset($_GET['org'])) {
  $org = $_GET['org'];
} else {
  exit();
}

$specific_date='';
if (isset($_GET['ds']) ) {
  $specific_date = $_GET['ds'];
}
$whatdt = 'now';
if (strlen($specific_date) > 0) {
  $whatdt=$specific_date;
}

$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);

$dateTimeZone = new DateTimeZone(orgTimezone($con,$org));
$dateTime = new DateTime($whatdt, $dateTimeZone);


echo "<allmembers>";
echo getMemmbersXmlRows($con, $org, $dateTime);
echo "</allmembers>";

mysqli_close($con);
?>
