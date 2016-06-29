<?php
//Rules for Canterbury
function removecomma($str)
{
   $str2 = str_replace(","," ",$str);
   return $str2;
}

function CalcTowRetrieve($org,$towplane,$duration)
{
   $chrg=-1.0;
   $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
   $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (!mysqli_connect_errno())
   {
    $q="SELECT cost from towcharges where org = ".$org." and type = 1 and plane = ".$towplane." order by effective_from DESC";  
    $r = mysqli_query($con,$q);
    if (mysqli_num_rows($r) > 0)
    {
       $row = mysqli_fetch_array($r);
       $d = round($duration / 60);
       $chrg = $d * $row[0];
    }   
   }
   return $chrg;
}

function CalcTowCharge($org,$launchtype,$towtype,$towplane,$duration,$height,$memberclass,$juniorclass,$clubglider,$is5050)
{
   $chrg=-1.0;
   if ($launchtype != $towtype)
   {
     $chrg = 0.0;
     return $chrg;
   }
   $q="";
   $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (!mysqli_connect_errno())
   {
      $q="SELECT cost from towcharges where org = ".$org." and type = 1 and plane = ".$towplane." order by effective_from DESC"; 
      $r = mysqli_query($con,$q);
      if (mysqli_num_rows($r) > 0)
      {
       $row = mysqli_fetch_array($r);
       $towcost=    $row[0] * round($duration / 60000.0);
       if ($is5050 > 0) 
         $towcost = round(($towcost / 2.0), 2);
       $chrg = $towcost; 
      }
   }
   mysqli_close($con);
   return $chrg;
}

function CalcTowCharge2($org,$launchtype,$towplane,$duration,$height,$strmemberclass,$clubglider,$is5050)
{
   $chrg=-1.0;
   if ($launchtype != 1)
   {
     $chrg = 0.0;
     return $chrg;
   }
   $q="";
   $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (!mysqli_connect_errno())
   {
      $q="SELECT cost from towcharges where org = ".$org." and type = 1 and plane = ".$towplane." order by effective_from DESC"; 
      $r = mysqli_query($con,$q);
      if (mysqli_num_rows($r) > 0)
      {
       $row = mysqli_fetch_array($r);
       $towcost=    $row[0] * round($duration / 60000.0);
       if ($is5050 > 0) 
         $towcost = round(($towcost / 2.0), 2);
       $chrg = $towcost; 
      }
   }
   mysqli_close($con);
   return $chrg;
}

function CalcWinchCharge($db,$org,$location,$flightDate)
{
   $ret = 0.00;
   $q ="SELECT amount,validfrom from charges where org = ".$org." and name ='Winch' and location = ".$location." and validfrom <= '" . $flightDate->format('Y-m-d')  ."' order by validfrom DESC";
   $r = mysqli_query($db,$q);
   if (mysqli_num_rows($r) > 0)
   {
       $row = mysqli_fetch_array($r);
       $ret = $row[0];
   }
   else
   {
     //Ignore the location and see if we find one.
     $q ="SELECT amount,validfrom from charges where org = ".$org." and name ='Winch' and validfrom <= '" . $flightDate->format('Y-m-d')  ."' order by validfrom DESC";
     $r = mysqli_query($db,$q);
     if (mysqli_num_rows($r) > 0)
     {
         $row = mysqli_fetch_array($r);
        $ret = $row[0];
     }
   }
   return $ret;
}

function CalcGliderCharge($org,$clubGlider,$regoshort,$SchemeCharge,$iRateGlider,$is5050,$totMins,$strmemberclass)
{
 //Parameters $clubGlider if it is a club glider
 //           $regoshort the three letter glider rego
 //           $SchemeCHarge = 1 if member on scheme
 //           $iRateGlider the scheme rate for the glider
 //	      $totMins total number of airborn minutes

 $chrg=-1.0;
 if ($clubGlider>0)
 {
  
  $maxglidtime=0;
  $glidrate=0;
  $q="";
  $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);        		
  $q="SELECT charge_per_minute,max_perflight_charge FROM aircraft where aircraft.org = ".$org." and rego_short = '" .$regoshort."'";
  $r = mysqli_query($con,$q);
  if (mysqli_num_rows($r) > 0)
  {
   $row = mysqli_fetch_array($r);
   $maxglidtime=$row[1];
   $glidrate=$row[0];
  }
  mysqli_close($con);
  if ($SchemeCharge==1)
    $glidrate=$iRateGlider;
  if ($maxglidtime > 0)
  {
    if ($totMins > $maxglidtime)
      $totMins = $maxglidtime;
  }
  $glidcost=$totMins*$glidrate;
  if ($is5050 > 0) 
    $glidcost = round(($glidcost / 2.0), 2); 
  $chrg=$glidcost;
}
else
  $chrg=0.0;
  return $chrg;
}

function MemberScheme($org,$memberid,$flightDate,$glider,&$rate,&$chargetow,&$chargeairways,&$name)
{
 $ret=-1;
 $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);    
 //First look for a scheme that includes specific gliders.
 $q = "SELECT a.id, a.rate_glider , a.charge_tow, a.name, a.specific_glider_list, a.charge_airways from scheme_subs LEFT JOIN incentive_schemes a ON a.id = scheme_subs.scheme WHERE scheme_subs.org = ".$org." and member =" .$memberid.  " and start <= '" .  $flightDate->format('Y-m-d') .  "' and end >= '".  $flightDate->format('Y-m-d') . "' and a.specific_glider_list LIKE '%" . $glider . "%'";
 $r = mysqli_query($con,$q);
 if (mysqli_num_rows($r) > 0)
 {
    $row = mysqli_fetch_array($r);
    $ret = $row[0];
    $rate =$row[1];
    $chargetow=$row[2];
    $name=$row[3];
    $chargeairways=$row[5];
 }
 else
 {
  $q = "SELECT a.id, a.rate_glider , a.charge_tow, a.name, a.specific_glider_list, a.charge_airways from scheme_subs LEFT JOIN incentive_schemes a ON a.id = scheme_subs.scheme WHERE scheme_subs.org = ".$org." and member =" .$memberid.  " and start <= '" .  $flightDate->format('Y-m-d') .  "' and end >= '".  $flightDate->format('Y-m-d') . "'";   
  $r = mysqli_query($con,$q);
  if (mysqli_num_rows($r) > 0)
  {
    while($row = mysqli_fetch_array($r) )
    {
      $glidlist = $row[4];   
      if (strlen($glidlist) == 0)
      {
         $ret=$row[0];
         $rate =$row[1];
         $chargetow=$row[2];
         $name=$row[3];
         $chargeairways=$row[5];
      }
    }
  }

 }
 mysqli_close($con);
 return $ret;
}

function CalcAirwaysCharge($org,$location,$clubGlider,$memberClass,$juniorclass,$flightDate,$is5050)
{
   $ret = 0.0;
   if($clubGlider > 0 && !($memberClass == $juniorclass))
   {        
     $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
     $q ="SELECT amount,validfrom from charges where org = ".$org." and location = '".$location."' and name ='Airways' and validfrom <= '" . $flightDate->format('Y-m-d')  ."' order by validfrom DESC";
     $r = mysqli_query($con,$q);
     if (mysqli_num_rows($r) > 0)
     {
       $row= mysqli_fetch_array($r);
       $ret=$row[0];
       if ($is5050)        
         $ret = $ret / 2.0;            
      }
     mysqli_close($con);
    }
    return $ret;	
}

function CalcOtherCharges($org,$location,$clubGlider,$memberClass,$juniorclass,$flightDate,$is5050,$Memberid,$flightseq=0)
{
   


   $ret = 0.0;
   $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   $q ="SELECT * from charges where org = ".$org." and location = '".$location."' and name ='Landing Fee' and validfrom <= '" . $flightDate->format('Y-m-d')  ."' order by validfrom DESC";
   $r = mysqli_query($con,$q);
   if (mysqli_num_rows($r) > 0)
   {
      $row= mysqli_fetch_array($r);
      $ret = $row['amount'];
      if ($row['max_once_per_day'] > 0 && $Memberid > 0 && $flightseq > 0)
      {
          $q2 = "SELECT * FROM flights where org = ".$org." and location = '".$location."' and localdate = '".$flightDate->format('Ymd')  ."' and (billing_member1 = ".$Memberid." or billing_member2 = ".$Memberid.") and seq < ".$flightseq;
          
          $r2 = mysqli_query($con,$q2);
          if (mysqli_num_rows($r2) > 0)
             $ret = 0.0;
      }
     

      
      if ($is5050)        
         $ret = $ret / 2.0;            
   }     

   return $ret;	
}
?>