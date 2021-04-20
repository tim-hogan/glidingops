<?php
require_once  dirname(__FILE__) . "/classProdEnvironment.php";
require_once  dirname(__FILE__) . "/classDevEnvironment.php";
$devt_environment = getenv('APP_ENV') == 'PRODUCTION' ? new ProdEnvironment() : new DevEnvironment();
?>