<?php
require_once 'load_model.php';

//TODO: remove $db once we update all pages to use eloquent
function orgTimezone($db,$org_id)
{
 $org = App\Organisation::find($org_id);
 if($org) {
  return $org->timezone;
 }
 return 'UTC';
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