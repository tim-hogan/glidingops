<?php
session_start();
require "./includes/classSecure.php";
require "./includes/classGlidingDB.php";
$DB = new GlidingDB($devt_environment->getDatabaseParameters());

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['user']))
        $myusername = trim($_POST['user']);
    if (isset($_POST['pcode']))
        $mypassword = trim($_POST['pcode']);
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $mypassword = md5($mypassword);

    $user = $DB->getUserByUsername($myusername);
    if ($user['password'] != $mypassword)
    {
        echo "Invalid Username or Password";
        exit();
    }

    //Check if qwe need to force a password reset
    if ( $user['force_pw_reset'] )
    {
        header('Location: PasswordChange');
        exit();
    }

    //Set up session variables
    $_SESSION['userid'] = $user['id'];
    $_SESSION['who'] = $myusername;
    $_SESSION['memberid'] = $user['member'];
    $_SESSION['org'] = $user['org'];
    $_SESSION['session_key']=base64_encode(openssl_random_pseudo_bytes(32));
    $_SESSION['csrf_key'] = base64_encode(openssl_random_pseudo_bytes(32));
    if ($_SESSION['org'] === NULL)
        $_SESSION['org'] = 0;
    $_SESSION['security'] = $user['securitylevel'];

    $pagesortdata = array();
    for ($i = 0; $i < 65;$i++)
        $pagesortdata[$i] = 0;

    $_SESSION['pagesortdata'] = $pagesortdata ;
    $_SESSION['dispname'] = $user['name'];

    if ($_SESSION['org'] != 0 )
        $_SESSION['timezone']  = $DB->getOrgTimezone($_SESSION['org']);
    else
        $_SESSION['timezone'] = "UTC";

    $DB->createAudit("Signin",$user['id'],$user['member']);

    header('Location: home');
    
    exit();
}

?>
