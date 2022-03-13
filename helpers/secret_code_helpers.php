<?php

function checkSecretCode($org, $key) {
    $secretCode = md5($key);

    $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
    $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
    if (mysqli_connect_errno())
    {
        return false;
    }

    $sql="SELECT secret_code FROM organisations WHERE id='$org'";
    $r = mysqli_query($con,$sql);
    $row = mysqli_fetch_array($r);
    return $row[0] == $secretCode;
}

function initiateServiceUserSession($securityLevel, $org){
    $_SESSION['userid']= -1;
    $_SESSION['who']= "service-user";
    $_SESSION['memberid']= -1;
    $_SESSION['org']= $org;
    $_SESSION['security']=$securityLevel;
    //$_SESSION['pagesortdata']=$pagesortdata ;
    $_SESSION['dispname']="";
    $q="SELECT timezone from organisations where id = " . $org;
    $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
    $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
    $r2 = mysqli_query($con,$q);
    $row2 = mysqli_fetch_array($r2);
    $_SESSION['timezone'] = $row2[0];
}

function generateRandomString($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>