<?php
function SendMail($to,$subject,$message)
{
  $headers = 
   'From: Judy Hogan <judy.hogan@glidingops.com>' . "\r\n" .
   'X-Mailer: PHP/' . phpversion();   
  return mail($to, $subject, $message, $headers);
}
SendMail("tim.hogan@clear.net.nz","Can you please....","Sort out the leaves for Sunday's Open Home");
echo "Sent";
?>