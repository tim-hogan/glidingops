<?php
include 'helpers.php';
header('Content-type: text/xml');
echo "<upd>";
if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST" )
{
 if ( $_SERVER["REQUEST_METHOD"] == "GET")
 {
   $strxml = $_GET["upd"];
   $org=$_GET['org'];
 }
 else
 {
   $strxml = $_POST["upd"];
   $org=$_POST['org'];
 }
$inc = "./orgs/" . $org . "/orgHelpers.php"; include $inc;
 

 $strStatus = "OK";
 $translate=array();
 $doc = new DOMDocument();
 $strdiag="";
 
 
 function tranlateid($val)
 {
   global $translate;
   $num = intval($val); 
   if ($num == 0)
      $num="null";
   else
   if (array_key_exists ( $num , $translate))
      $num=intval($translate[$num]);       
   return $num;
 }
 $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
 
 $launchTypeTow=getTowLaunchType($con);
 $flightTypeGlider = getGlidingFlightType($con); 
 $flightTypeCheck = getCheckFlightType($con); 
 $flightTypeRetrieve = getRetrieveFlightType($con); 
 $flightTypeLandingFee = getLandingFeeFlightType($con);

 $noChargeOpt = getNoChargeOpt($con);
 
 if (!$doc->loadXML("<?xml version=\"1.0\"?>" . $strxml))
 {
    echo "<status>ERROR: XML Parse</status></upd>";
    error_log("XML Parse error: " . $strxml);
    exit();
 }
 
 $dateStr = $doc->getElementsByTagName('date')->item(0)->nodeValue;
 $updseq = $doc->getElementsByTagName('updseq')->item(0)->nodeValue;
 $asslist = $doc->getElementsByTagName ('newassocs')->item(0);

 
//Find class for Associates
$mem_class_assoc = getClassId($con,$org,_NAME_TRIAL_CLASS);
$role_member = getRoleId($con,'Member');
$role_tow = getRoleId($con,'Tow Pilot');

 //Find billing option for Other Member 
 $r = mysqli_query($con,"SELECT * FROM billingoptions WHERE bill_other = 1");
 $row = mysqli_fetch_array($r);
 $billing_other_member = $row['id'];

 $role = $role_member;
 
 //Any new members
 $list = $asslist->getElementsByTagName ('member'); 
 foreach ($list as $member) 
 {
   $tempid=$member->getElementsByTagName('tempid')->item(0)->nodeValue;
   $cell=$member->getElementsByTagName('cell')->item(0)->nodeValue;
   $colid=$member->getElementsByTagName('colid')->item(0)->nodeValue;
   $org=$member->getElementsByTagName('org')->item(0)->nodeValue;
   $sur = $member->getElementsByTagName('surname')->item(0)->nodeValue;
   $fir = $member->getElementsByTagName('firstname')->item(0)->nodeValue;
   $disp = $member->getElementsByTagName('displayname')->item(0)->nodeValue;
   $sur=trim($sur);
   $fir=trim($fir);
   $disp=trim($disp);
   if (strlen($sur) == 0 || strlen($fir) == 0)
   {
       echo "<status>NEW MEMBER DATA ERROR Surname and/or Firstname zero length</status></upd>";
       exit(0);
   }
   if (strlen($disp) == 0)
      $disp = $sur . " " . $fir;

   $sur = mysqli_real_escape_string($con, $sur);
   $fir = mysqli_real_escape_string($con, $fir);
   $disp = mysqli_real_escape_string($con, $disp);


   $mob = $member->getElementsByTagName('mobile')->item(0)->nodeValue;
   $mob = trim($mob);
   $mob = "+" . ltrim($mob," +");
   $em = $member->getElementsByTagName('email')->item(0)->nodeValue;
   if ($colid == "towpilot")
	$role=$role_tow;
   
   $q = "SELECT id from members where org = " .$org. " and surname = '" .$sur. "' and firstname ='" .$fir. "' and email = '" .$em. "'";
   $r=mysqli_query($con,$q);
   if ($r->num_rows > 0)
   { 
   	$row = mysqli_fetch_array($r);
        $translate[$tempid]=$row[0];
	echo "<member><tempid>".$tempid."</tempid><cell>".$cell."</cell><colid>".$colid."</colid><id>".$row[0]."</id><displayname>".$disp."</displayname></member>";
   }
   else
   {  

   $q = "INSERT INTO members (org,surname,firstname,displayname,class,phone_mobile,email,enable_text,enable_email) VALUES (" .$org.",'" . $sur . "','"  . $fir . "','" . $disp . "','" . $mem_class_assoc . "','" . $mob . "','" . $em . "','" . "0" . "','" . "0" . "')";  
   if(mysqli_query($con,$q) )
   {
      $memid=mysqli_insert_id($con);
      
      $translate[$tempid]=$memid;
      //We can now create some xml to retunr the person
      echo "<member><tempid>".$tempid."</tempid><cell>".$cell."</cell><colid>".$colid."</colid><id>".$memid."</id><displayname>".$disp."</displayname></member>";
         
      //We need to map the role type if they are a towy
      if ($role==$role_tow)
      {
         $q="INSERT INTO role_member (org,role_id,member_id) VALUES (" .$org. "," .$role. "," .$memid. ")";
         if (!mysqli_query($con,$q) )
            error_log ("SQL Error: " .  mysqli_error($con) . " QUERY WAS: " . $q);
      }

   }
   else
   {
      error_log ("SQL Error: " .  mysqli_error($con) . " QUERY WAS: " . $q);
      echo "<status>SQL ERROR INSERT MEMBER: " . mysqli_error($con) . " SQL: " . $q . "</status><updseq>" . $updseq . "</updseq></upd>";	
   }
   }
   
 }
 
 $list = $doc->getElementsByTagName ('flight');
 foreach ($list as $flight) 
 {
   
   
   $org = $flight->getElementsByTagName('org')->item(0)->nodeValue;
   $location = $flight->getElementsByTagName('location')->item(0)->nodeValue;
   $seq = $flight->getElementsByTagName('id')->item(0)->nodeValue;
   $launchtype = $flight->getElementsByTagName('launchtype')->item(0)->nodeValue;
   $plane = $flight->getElementsByTagName('plane')->item(0)->nodeValue;
   $glid = $flight->getElementsByTagName('glider')->item(0)->nodeValue;
   $towp = $flight->getElementsByTagName('towpilot')->item(0)->nodeValue;
   $townum = tranlateid($flight->getElementsByTagName('towpilot')->item(0)->nodeValue); 
   $p1num = tranlateid($flight->getElementsByTagName('p1')->item(0)->nodeValue); 
   $p2num = tranlateid($flight->getElementsByTagName('p2')->item(0)->nodeValue);
   $start = $flight->getElementsByTagName('start')->item(0)->nodeValue;
   $towland = $flight->getElementsByTagName('towland')->item(0)->nodeValue;
   $land = $flight->getElementsByTagName('land')->item(0)->nodeValue;
   $height = $flight->getElementsByTagName('height')->item(0)->nodeValue;
   $charge = $flight->getElementsByTagName('charges')->item(0)->nodeValue;
   $comments = $flight->getElementsByTagName('comments')->item(0)->nodeValue;
   $comments =  mysqli_real_escape_string($con, $comments);
   $deleted = $flight->getElementsByTagName('del')->item(0)->nodeValue;
  
   //Get flight type
   $flightType = $flightTypeGlider;   
   if (intval($height) == -1)
   {
       $flightType = $flightTypeCheck;
       $charge =  "c" . $noChargeOpt;
   }
   if (intval($height) == -2)
   {
       $flightType = $flightTypeRetrieve; 
   }

   //Calculate launch type
   if (substr($plane,0,1) == "t")
   {
       $launchtype = $launchTypeTow;
       $plane = ltrim($plane,"t");
   }
   else
   if (substr($plane,0,1) == "l")
   {
       $launchtype = ltrim($plane,"l");
       $plane = 0; 
   }
   else
   if (substr($plane,0,1) == "f")
   {
       $launchtype = 0;
       $plane = 0;
       $flightType = $flightTypeLandingFee; 
   }


   //Calculate who to charge to
   $bill1 = "null";
   $bill2 = "null";
   
   if (substr($charge,0,1) == "m")
   {
      $bill1 = $charge=ltrim($charge,"m");
      $charge = $billing_other_member;
   }
   else
   {
   	if (substr($charge,0,1) == "c")
      		 $charge=ltrim($charge,"c");
   
  

   	$q2 = "SELECT * FROM billingoptions where id = " . $charge;
   	$r2 = mysqli_query($con,$q2);
   	$row_cnt = $r2->num_rows;
   	$row = mysqli_fetch_array($r2);
   
   	if ($row_cnt > 0)
   	{
      		if ($row['bill_pic'] > 0)
           		$bill1 = $p1num;
      		if ($row['bill_p2'] > 0)
           		$bill2 = $p2num;
   	}
   }

   if ($bill1==0)
      $bill1="null";
   if ($bill2==0)
      $bill2="null";

   $q2 = "SELECT * FROM flights WHERE flights.org = ".$org." and localdate = " . $dateStr . " and seq = " . $seq;
   $r2 = mysqli_query($con,$q2);
   if (!$r2)
   {
        $strStatus= "SQL ERROR LOOKING FOR FLIGHT TO UPDATE: " . mysqli_error($con) . " SQL: " . $q2;
        error_log($strStatus);	
   }
   $row_cnt = $r2->num_rows;
   if ($row_cnt > 0)
   {
      
      //We all ready have a record so need to update it
      $q3= "UPDATE flights SET org=" . $org . ",localdate='" . $dateStr . "',location='" . $location . "',seq='" . $seq . "',type='" . $flightType . "',launchtype=";
      
      if ($launchtype == '0' || $launchtype == 0)
         $q3 .= "null";
      else
         $q3 .= $launchtype;
          
      $q3 .= ",updseq='" . $updseq . "',towplane=";

      if ($plane=='0' || $plane == 0)
         $q3 .= "null";
      else
         $q3 .= $plane;

      $q3 .= ",glider='" . $glid  . "',towpilot=";
      
      if ($townum =="null")
        $q3 .= "null";
      else
        $q3 .= "'" . $townum . "'";

      $q3 .= ",pic=";
      if ($p1num=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $p1num . "'";
      
      $q3 .= ",p2=";
      if ($p2num=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $p2num . "'";
      
      $q3 .= ",start='" . $start  . "',towland='" . $towland  . "',land='" . $land  . "',height='" . $height  . "',billing_option='" . $charge  . "'";
      
      $q3 .= ",billing_member1=";
      if ($bill1=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $bill1 . "'";

      $q3 .= ",billing_member2=";
      if ($bill2=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $bill2 . "'";

      $q3 .= ",comments='" . $comments ."' ";
      if ($deleted == "1")
          $q3 .= ",deleted=1 ";
      else
          $q3 .= ",deleted=0 ";

      $q3 .= "WHERE org = ". $org ." AND localdate='" . $dateStr . "' AND seq='" . $seq . "'";
      
      $r3 = mysqli_query($con,$q3);
      if (!$r3)
      {
	$strStatus= "SQL ERROR UPDATE FLIGHT: " . mysqli_error($con) . " SQL: " . $q3;
        error_log($strStatus);	
      }
   } 	
   else
   {
      
      //We create the record
      $q3 = "INSERT INTO flights (org,localdate,location,seq,type,updseq,launchtype,towplane,glider,towpilot,pic,p2,start,towland,land,height,billing_option,billing_member1,billing_member2,comments,deleted) VALUES (".$org.",'" . $dateStr . "','" . $location . "','" . $seq . "','" . $flightType . "','" . $updseq  . "'," . $launchtype  . ",";
      if ($plane=='0' || $plane == 0)
          $q3 .= "null";
      else
        $q3 .= $plane ;

      $q3 .= ",'" . $glid  . "',";
      
     if ($townum =="null")
        $q3 .= "null";
      else
        $q3 .= "'" . $townum . "'";
     
      $q3 .= "," ;
      if ($p1num=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $p1num . "'";
      
      $q3 .= ",";
      if ($p2num=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $p2num . "'";

      $q3 .= ",'" . $start  . "','" . $towland  . "','" . $land  . "','" . $height  . "','" . $charge  . "'";

     
      $q3 .= "," ;
      if ($bill1=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $bill1 . "'";
       
     

      $q3 .= "," ;
      if ($bill2=="null")
         $q3 .= "null";
      else
        $q3 .= "'" . $bill2 . "'";
       
      $q3 .= ",'" . $comments  . "' ";
      if ($deleted != "0")
	$q3 .= ",1";
      else
	$q3 .= ",0";
   
      $q3 .= ")"; 


      $r3 = mysqli_query($con,$q3);
      if (!$r3)
      {	
        $strStatus= "SQL ERROR INSERT FLIGHT: " . mysqli_error($con) . " SQL: " . $q3;
        error_log($strStatus);	
      }

   } 
 }  
 
 mysqli_close($con);
 echo "<diag>" . $strdiag . "</diag>";
 echo "<status>" . $strStatus . "</status><updseq>" . $updseq . "</updseq></upd>";	
}
?>
