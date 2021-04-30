<?php
/**
 * @abstract Code for the a Allflights report
 * @author Tim Hogan
 * @version 1.0
 * @requires classSecure (Which includes clasEnvironment, clasVault and secruityParams )
 *
 */
session_start();
require_once "./includes/classSecure.php";
require_once "./includes/classRolling.php";

require "./includes/classGlidingDB.php";
$DB = new GlidingDB($devt_environment->getDatabaseParameters());

function var_error_log( $object=null,$text='')
{
    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( "{$text} {$contents}" );
}

function getLocalDate($f)
{
    if (isset($_POST[$f]))
        return (new DateTime($_POST[$f]))->format('Ymd');
    return null;
}

//Start
$selff = trim($_SERVER["PHP_SELF"],"/");
$user = null;
$org = 0;
$organistaion=null;
$localDateStart = null;
$localDateEnd = null;

if (isset($_SESSION['userid']))
    $user = $DB->getUser($_SESSION['userid']);
if ($user && isset($user['org']))
{
    if ($organistaion = $DB->getOrganisation($user['org']) )
        $org = $user['org'];
}

Secure::CheckPage2($user, intval(SECUIRTY_CFO) | intval(SECURITY_CFI) | intval(SECURITY_ADMIN));

$strNow = (new DateTime())->setTimezone(new DateTimeZone($organistaion['timezone']))->format("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!Secure::checkCSRF())
    {
        header("Location: SecurityError.php");
        exit();
    }

    $localDateStart = getLocalDate('fromdate');
    $localDateEnd = getLocalDate('todate');

}


?>
<!DOCTYPE HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>Allflights report</title>
    <link rel='stylesheet' type='text/css' href='css/base.css' />
    <link rel='stylesheet' type='text/css' href='css/heading.css' />
    <link rel='stylesheet' type='text/css' href='css/menu.css' />
    <link rel='stylesheet' type='text/css' href='css/main.css' />
    <link rel='stylesheet' type='text/css' href='css/AllFlights.css' />
</head>
<body>
    <div id="container">
        <div id="heading">
            <?php include "./orgs/{$org}/heading.php" ?>
        </div>
        <div id="menu">
            <div class="menuitem" onclick="window.location='home'">HOME</div>
        </div>
        <div id="main">
            <h1>All flights report</h1>
            <div id="selection">
                <h1>REPORT PARAMETERS</h1>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div id="divfromdate">
                        <label for="fromdate">FROM</label>
                        <input id="fromdate" type="date" name="fromdate" />
                    </div>
                    <div id="divtodate">
                        <label for="todate">TO</label>
                        <input id="todate" type="date" name="todate" value="<?php echo $strNow;?>"/>
                    </div>
                    <?php
                    echo "<input type='hidden' name='formtoken' value='{$_SESSION['csrf_key']}'>";
                    ?>
                    <button>CREATE REPORT</button>
                </form>
            </div>
            <div id="report">
                <?php
                if ($localDateStart && $localDateEnd)
                {
                    echo "<div id='report2'>";
                        $r = $DB->allGliderFlightsForOrg($org,$localDateStart,$localDateEnd);
                        if (!$r || $r->num_rows == 0)
                        {
                            echo "<p>NO FLIGHT RECORDS</p>";
                        }
                        else
                        {
                            echo "<h1>FLIGHTS ({$r->num_rows})</h1>";
                            echo "<table>";
                            echo "<tr><th>DATE</th><th>SEQ</th><th>LOCATION</th><th>LAUNCH TYPE</th><th>TOW</th><th>GLIDER</th><th>TOWY/WINCHY</th><th>PIC</th><th>P2</th><th>LAUNCH</th><th>LAND</th><th>DURATION</th><th>CHARGE</th><th>COMMENT</th></tr>";

                            while ($flight = $r->fetch_assoc())
                            {
                                $strDate = substr($flight['localdate'],6,2) . "/" . substr($flight['localdate'],4,2) ."/" .substr($flight['localdate'],0,4);
                                $strLocation = htmlspecialchars($flight['location']);
                                $strTOWY = htmlspecialchars($flight['TOWY']);
                                $strPIC = htmlspecialchars($flight['PICNAME']);
                                $strP2 = htmlspecialchars($flight['P2NAME']);
                                $strLaunch = (new DateTime())->setTimestamp($flight['start'] / 1000)->setTimezone(new DateTimeZone($organistaion['timezone']))->format('H:i');
                                $strLand = (new DateTime())->setTimestamp($flight['land'] / 1000)->setTimezone(new DateTimeZone($organistaion['timezone']))->format('H:i');
                                $duration = (($flight['land'] / 1000) - ($flight['start'] / 1000)) / 60;
                                $strDuration = sprintf("%02d",$duration / 60) . ":" . sprintf("%02d",$duration % 60);
                                $billingname = htmlspecialchars($flight['BILLINGNAME']);
                                $comment = htmlspecialchars($flight['comments']);
                                $numtracks = $DB->numTracksForFlight((new DateTime())->setTimestamp($flight['start'] / 1000),(new DateTime())->setTimestamp($flight['land'] / 1000),$flight['glider']);

                                echo "<tr>";
                                echo "<td>{$strDate}</td>";
                                echo "<td class='c'>{$flight['seq']}</td>";
                                echo "<td class='c'>{$strLocation}</td>";
                                echo" <td class='c'>{$flight['LT']}</td>";
                                echo "<td class='c'>{$flight['TOWREGO']}</td>";
                                echo "<td class='c'>{$flight['glider']}</td>";
                                echo "<td>{$strTOWY}</td>";
                                echo "<td>{$strPIC}</td>";
                                echo "<td>{$strP2}</td>";
                                echo "<td class='r'>{$strLaunch}</td>";
                                echo "<td class='r'>{$strLand}</td>";
                                echo "<td class='r'>{$strDuration}</td>";
                                echo "<td class='c'>{$billingname}</td>";
                                echo "<td>{$comment}</td>";
                                echo "<td>" . $numtracks > 0 ? "MAP" : "" . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
