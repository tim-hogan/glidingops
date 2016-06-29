<?php session_start(); ?>
<?php
include 'helpers.php';
if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 1))
 {
  die("Secruity level too low for this page");
 }
}
else
{
 header('Location: Login.php');
 die("Please logon");
}
$status="";
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 if (SendMail("bwsharpe@xtra.co.nz",$_POST['SB'],$_POST['MSG']) )
    $status = "Mail Sent";
 else
    $status = "Error Sending Mail";
}
?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<tr><td>To:</td><td>bwsharpe@xtra.co.nz</td></tr>
<tr><td>Subject:</td><td><input type="text" name = 'SB'></td></tr>
<tr><td>Message:</td><td><textarea rows="4" cols="50" name ='MSG'></textarea></td></tr>
<tr><td><input type="submit" name = 'send' value = 'Send'></td><td></td></tr>
</table>
</form>
<p><?php echo $status; ?></p>
</body>
</html>
