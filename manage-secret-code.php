<?php
include './helpers/session_helpers.php';
include './helpers/secret_code_helpers.php';
session_start();
require_security_level(64);

$org = isset($_SESSION['org']) ? $org=$_SESSION['org'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $secret_code = generateRandomString(16);

    $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
    $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
    if (mysqli_connect_errno())
    {
        return false;
    }

    $sql= "UPDATE organisations SET secret_code='".md5($secret_code)."' WHERE id=$org ";
    $result = mysqli_query($con,$sql);
    if (!$result)
    {
        $error= "SQL ERROR : " . mysqli_error($con);
        error_log($error);
    }
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
    <style>
        <?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?>
    </style>
    <style>
    <?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
    <link rel="stylesheet" type="text/css" href="styletable1.css">
    <script>function goBack() {window.history.back()}</script>
</head>
<body>
    <?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
    <?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
    <div style="padding:5px">
        <form action="/manage-secret-code.php" method="POST">
            <input type="submit" value="Regenerate Secret Code"></input>
        </form>
        <?php if(isset($error)) {?>
            <p>Could not set new secret code.</p>
            <p>Error: <?=$error?></p>
        <?php }else if(isset($secret_code)) {?>
            <p>Secret Code: <?=$secret_code?></p>
            <p>Daily Ops Url: www.glidingops.com/DailySheet.php?org=<?=$org?>&key=<?=$secret_code?></p>
        <?php }?>
    </div>
</body>
</html>
