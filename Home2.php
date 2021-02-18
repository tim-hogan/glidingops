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

$org = 0;
$organistaion=null;
$user = $DB->getUserWithMember($_SESSION['userid']);
if ($user && isset($user['org']))
{
    if ($organistaion = $DB->getOrganisation($user['org']) )
        $org = $user['org'];
}

if ($org == 0)
{
    header("Location: Login.php");
    exit();
}

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
            <?php include "./orgs/{$org}/heading.php" ?>
        </div>
        <div id="messages">
            <h1>LAST CLUB MESSAGE</h1>
            <?php
            $msg = $DB->getLastOrgMessage($org);
            if ($msg)
            {
                $strMsgTime = classTimeHelpers::timeFormatnthDateTime1($msg['create_time'],$organistaion['timezone']);
                echo "<p>{$strMsgTime} " . htmlspecialchars($msg['msg']) . "</p>";
            }
            ?>
        </div>
        <div id="main">
            <div id="tiles">
                <div class="tile">
                    <h1>My Flights</h1>
                </div>
            </div>
        </div>
    </div>
</body>
</html>