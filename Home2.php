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
                echo "<p class='msgtime'>{$strMsgTime}</p>";
                echo "<p class='msg'>" . htmlspecialchars($msg['msg']) . "</p>";
            }
            ?>
        </div>
        <div id="main">
            <div id="tiles">
                <div class="tile" onclick="window.location='MyFlights'">
                    <h1>My Flights</h1>
                    <p>Lists details of your flights.</p>
                </div>
                <div class="tile">
                    <h1>Flying Now</h1>
                    <p>Choose between text based list of who is flying today or choose a map view to see tracked flights.</p>
                    <button>LIST</button>
                    <button>MAP</button>
                </div>
                <?php
                if ( Secure::CheckSecurity(SECUIRTY_CFO) || Secure::CheckSecurity(SECURITY_CFI) || Secure::CheckSecurity(SECURITY_ADMIN) || Secure::CheckSecurity(SECURITY_GOD) )
                {
                    echo "<div class='tile' onclick='window.location=\"Parameters.php\"'>";
                    echo "<h1>PARAMETERS</h1>";
                    echo "<p>View, create and change the system parameters</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>