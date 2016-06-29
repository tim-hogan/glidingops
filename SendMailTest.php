<?php
include 'helpers.php';
$to = 'bwsharpe@xtra.co.nz';
$subject = "And another test email";
$message = "Brian, an automated email from the new Gliding Operations Site, hope this beats the SPAM";
if (SendMail($to,$subject,$message))
echo "Good";
else
echo "Bad";
?>