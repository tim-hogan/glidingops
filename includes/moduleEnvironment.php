<?php
require_once  dirname(__FILE__) . "/classEnvironment.php";
require_once  dirname(__FILE__) . "/classDevEnvironment.php";
$devt_environment = getenv('APP_ENV') == 'PRODUCTION' ? new Environment() : new DevEnvironment();
?>