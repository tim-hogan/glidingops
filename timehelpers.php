<?php
function orgTimezone($db,$org)
{
 $tz='UTC';
 $q="SELECT timezone from organisations where id = ".$org;
 if($r = mysqli_query($db,$q))
 {
  if ($row = mysqli_fetch_array($r))
   $tz=$row[0];
 }
 return $tz;
}
function timeLocalFormat($d,$strTimeZone,$strFormat)
{
 if ($strTimeZone==NULL || strlen($strTimeZone)==0)
   $strTimeZone = 'UTC';  
 $date = new DateTime();
 $date = $d;
 $date->setTimezone(new DateTimeZone($strTimeZone)); 
 return $date->format($strFormat);
}
function timeLocalSQL($sql,$strTimeZone,$strFormat)
{
 if ($strTimeZone==NULL || strlen($strTimeZone)==0)
   $strTimeZone = 'UTC';  
 $date = new DateTime($sql);
 $date->setTimezone(new DateTimeZone($strTimeZone)); 
 return $date->format($strFormat);
}
function timestampSQL($sql)
{
 $date = new DateTime($sql);
 return $date->getTimestamp();
}
?>