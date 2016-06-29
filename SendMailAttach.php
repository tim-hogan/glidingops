<?php
//Example php SendMailAttach.php -tjoe.blogg@aplace.com -fwebmaster@fred.com -s"Subject Matter" -m"This is a nail message" -aAttachFile.txt
$to = '';
$from = '';
$replyto = '';
$subject = '';
$attach = '';
$msg ='';
for ($i=1;$i<$argc;$i++)
{
  $str = $argv[$i];
  if (substr($str,0,2) == "-t")
     $to = substr($str,2,strlen($str)-2);
  if (substr($str,0,2) == "-f")
     $from = substr($str,2,strlen($str)-2);
  if (substr($str,0,2) == "-s")
     $subject = substr($str,2,strlen($str)-2);
  if (substr($str,0,2) == "-a")
     $attach = substr($str,2,strlen($str)-2);
  if (substr($str,0,2) == "-m")
     $msg = substr($str,2,strlen($str)-2);
}
if (strlen($replyto) == 0)
   $replyto = $from;

//create a boundary string. It must be unique 
//so we use the MD5 algorithm to generate a random hash 
$random_hash = md5(date('r', time())); 
//define the headers we want passed. Note that they are separated with \r\n 
$headers = "From: ".$from."\r\nReply-To: ".$replyto; 
//add boundary string and mime type specification 
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
$attachment = chunk_split(base64_encode(file_get_contents($attach))); 
//define the body of the message. 
ob_start(); //Turn on output buffering 
?> 
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<?php echo $msg; ?>

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<h2><?php echo $subject; ?></h2>
<p><?php echo $msg; ?></p> 

--PHP-alt-<?php echo $random_hash; ?>-- 

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/zip; name="attach.zip"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?> 
--PHP-mixed-<?php echo $random_hash; ?>-- 

<?php 
//copy current buffer contents into $message variable and delete current output buffer 
$message = ob_get_clean(); 
//send the email 
$mail_sent = @mail( $to, $subject, $message, $headers ); 
//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
echo $mail_sent ? "Mail sent" : "Mail failed"; 
?>