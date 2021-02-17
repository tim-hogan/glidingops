<?php
session_start();
require "./includes/classSecure.php";
require "./includes/classTime.php";
require "./includes/classGlidingDB.php";
$DB = new GlidingDB($devt_environment->getDatabaseParameters());

if (! Secure::isSignedIn())
{
    header("Location: Login.php");
    exit();
}

$user = $DB->getUser($_SESSION['userid']);
$org = $DB->getOrganisation($user['org']);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<title>glidingOps</title>
<link rel='stylesheet' type='text/css' href='css/base.css' />
<link rel='stylesheet' type='text/css' href='css/heading.css' />
<link rel='stylesheet' type='text/css' href='css/message.css' />
<link rel='stylesheet' type='text/css' href='css/main.css' />
<link rel='stylesheet' type='text/css' href='css/home.css' />
</head>
<body>
    <div id="container">
        <div id="heading">

        </div>
        <div id="messages">
            <h1>LAST CLUB MESSAGE</h1>
            <?php
            $msg = $DB->getLastOrgMessage($org);
            if ($msg)
            {
                $strMsgTime = classTimeHelpers::timeFormat12Hr($msg['create_time'],$org['timezone']);
                echo "<p>{$strMsgTime} " . htmlspecialchars($msg) . "</p>";
            }
            ?>
        </div>
        <div id="main">
            <div id="tiles">

            </div>
        </div>
    </div>
</body>
</html>