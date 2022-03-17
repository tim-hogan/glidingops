<?php
include './helpers/session_helpers.php';
session_start();
require_security_level(4);

include 'helpers.php';
header('Content-type: text/xml');
echo "<checks>";
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
  $someerrors=0;
  $datestr=$_GET["date"];
  $org=$_GET["org"];
  $shorttermclass=0;
  echo "<diag>" . $datestr . "</diag>";
  
  $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
  $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
  if (mysqli_connect_errno()) {
     echo "<err>Database open error:</err></checks>";
     exit();
  }

  $towlaunch = getTowLaunchType($con);
  $winchlaunch = getWinchLaunchType($con);
  $flightTypeGlider = getGlidingFlightType($con); 
  $flightTypeCheck = getCheckFlightType($con); 
  $flightTypeRetrieve = getRetrieveFlightType($con);
  $flightTypeLandingFee = getLandingFeeFlightType($con);
  $towChargeType = getTowChargeType($con,$org);
  $strNameShortTerm = 'Short Term';
  if ($org == 3)
    $strNameShortTerm = 'Associate';

   $q="SELECT id from membership_class where org = ".$org." and class = '".$strNameShortTerm."'";
   $r = mysqli_query($con,$q);
   if (1==mysqli_num_rows($r))
   {
       $row1 = mysqli_fetch_array($r);
       $shorttermclass = $row1[0];
   }
   echo "<diag>Short Term Class = " . $shorttermclass . "</diag>";

   //First get the roster for the date
   $q = <<<SQL
		SELECT 	flights.seq,flights.towplane, flights.glider, flights.towpilot, flights.pic, flights.p2, flights.height,(flights.start/1000),(flights.land/1000), (flights.land-flights.start),
				a.bill_pic , a.bill_p2 , a.bill_other, flights.billing_member1,flights.billing_member2, b.class , c.class, flights.launchtype, flights.id, flights.comments, a.name, flights.type,
				flights.towland, a.requires_comment, a.name 
		FROM flights LEFT JOIN billingoptions a ON a.id = flights.billing_option 
					 LEFT JOIN members b ON b.id = flights.billing_member1 
					 LEFT JOIN members c ON c.id = flights.billing_member2 
		WHERE flights.org = {$org} and flights.deleted != 1 and flights.localdate={$datestr};
SQL;
   // 0 flights.seq
   // 1 flights.towplane
   // 2 flights.glider
   // 3 flights.towpilot
   // 4 flights.pic
   // 5 flights.p2
   // 6 flights.height
   // 7 flights.start
   // 8 flights.land
   // 9 (flights.land-flights.start)
   //10 a.bill_pic
   //11 a.bill_p2
   //12 a.bill_other
   //13 flights.billing_member1
   //14 flights.billing_member2
   //15 b.class
   //16 c.class
   //17 flights.launchtype
   //18 id
   //19 comments
   //20 a.name
   //21 type
   //22 flights.towland
   //23 a.requires_comment
   //24 a.name
   
   $r = mysqli_query($con,$q);
   while ($row = mysqli_fetch_array($r) )
   {
       $errdet=0;
       $thisseq=$row[0];
       //if type is Check Flight or Retrieve then tow plane must be specified.       
       if ($row[21] != $flightTypeGlider && $row[17] != $towlaunch)
       {
           $errdet=1;
           if ($row[21] == $flightTypeCheck)
           	echo "<err>Flight Seq: " .$thisseq. " Check flight must have a tow plane</err>";
           if ($row[21] == $flightTypeRetrieve)
           	echo "<err>Flight Seq: " .$thisseq. " Retrieve flight must have a tow plane</err>";
       }

       if ($row[21] == $flightTypeRetrieve && $row[12] == 0)
       {
          //Billing other member must be set
           $errdet=1;       
           echo "<err>Flight Seq: " .$thisseq. " Retrieve flight must have billing field set to member</err>";
       }

       //Check we have a tow plane rego
       if ($row[17] == $towlaunch)
       {
         if ($row[1] < 1)
         {
           $errdet=1;
           echo "<err>Flight Seq: " .$thisseq. " Towplane rego missing</err>";
         }
       }
       //Check we have a Glider rego if flight type is glider
       if (strlen($row[2]) < 3 && $row[21] == $flightTypeGlider)
       {
          $errdet=1; 
          echo "<err>Flight Seq: " .$thisseq. " Glider rego missing or not 3 characters</err>";
       }
       //Check we have a tow pilot   
       if ($row[17] == $towlaunch ||  $row[17] == $winchlaunch )
       {
         if ($row[3] <= 0 || $row[3] == null)
         {
            $errdet=1; 
            echo "<err>Flight Seq: " .$thisseq. " Tow pilot or winch launcher not specified</err>";
         }
       }
       else
       {
          if ($row[3] > 0)
          {
             $errdet=1; 
             echo "<err>Flight Seq: " .$thisseq. " Tow pilot or winch launcher specified for self launch</err>";
          }
             
       }
       if ($row[21] == $flightTypeGlider)
       {
         if ($row[4] <= 0 || $row[4] == null)
         {
             if ($row[5] <= 0 || $row[5] == null)
             {
                 $errdet=1; 
                 echo "<err>Flight Seq: " .$thisseq. " No Glider Pilots specified</err>";
             }
             else
             {
	         $errdet=1; 
	         echo "<err>Flight Seq: " .$thisseq. " No pilot in command specified</err>";
             }
         }
       }
       //Check that PIC and P2 arent the same person       
       if ($row[4] > 0 && $row[5] > 0)
       {
           if ($row[4] == $row[5])
           {
               $errdet=1; 
               echo "<err>Flight Seq: " .$thisseq. " PIC and P2 are the same person</err>";
           }
       }
       //Check that we have correct names
       if ($row[5] == 3621)  // A Trial Flight
       {
              $errdet=1; 
              echo "<err>Flight Seq: " .$thisseq. " Please specify correct name for Trial (NOT A Trial Flight)</err>";
       }
       
       //Check we have a height
       if ($towChargeType==1)
       {
         if ($row[17] == $towlaunch &&  $row[21] == $flightTypeGlider)
         {
           if ($row[6] <= 0 || $row[6] == null)
           {
              $errdet=1; 
              echo "<err>Flight Seq: " .$thisseq. " Height not specified</err>";
           }
         }
         else
         {
            if ($row[6] > 0)
            {
              $errdet=1; 
              echo "<err>Flight Seq: " .$thisseq. " Height specified for non towplane launch</err>";
            }
         }
       }

       //FOR TOW TIME BASED CHARGING CHECK TOW LAND FOR CHECK FLIGHT
       if ($towChargeType==2) // Time based
       {
       	if ($row[7] <= 0 && $row[21] != $flightTypeLandingFee)
       	{
           $errdet=1; 
           echo "<err>Flight Seq: " .$thisseq. " Start takeoff time not recorded</err>";
       	}
   
       	if ($row[8] <= 0 && $row[21] != $flightTypeLandingFee && $row[21] != $flightTypeCheck)
       	{
           $errdet=1; 
           echo "<err>Flight Seq: " .$thisseq. " Landing time not recorded</err>";
       	}
       	   if ($row[22] <= 0 && $row[21] == $flightTypeCheck)
           {
             $errdet=1; 
             echo "<err>Flight Seq: " .$thisseq. " Tow land time not specified</err>";
          }

          if ($row[22] <= 0 && $row[21] != $flightTypeLandingFee && $row[17] == $towlaunch)
	  {
             $errdet=1; 
             echo "<err>Flight Seq: " .$thisseq. " Tow land time not specified</err>";
	  }


       }
 
       if ($towChargeType==1) //Height Based
       {
       	//Check we start and end time
       	if ($row[7] <= 0 && $row[21] != $flightTypeLandingFee)
       	{
           $errdet=1; 
           echo "<err>Flight Seq: " .$thisseq. " Start takeoff time not recorded</err>";
       	}
   
       	if ($row[8] <= 0 && ($row[21] != $flightTypeLandingFee || $row[21] != $flightTypeCheck))
       	{
           $errdet=1; 
           echo "<err>Flight Seq: " .$thisseq. " Landing time not recorded</err>";
       	}
       }
        //Check end time is greater than start
       if ($row[7] > 0 && $row[8] > 0)
       {
            ///Need to check that land is greater than start.
            if ($row[9] < 0)
            {
                $errdet=1; 
                echo "<err>Flight Seq: " .$thisseq. " Landing time must be after takeoff time</err>";
            }
       }
       //We must have a comment when expicitly required and if flight type is glider
       if ($row[21] == $flightTypeGlider && $row[23] == 1)
       {
           //We need a comment in the comment field.
           if (strlen($row[19]) == 0)
           {
                $errdet=1; 
                echo "<err>Flight Seq: " .$thisseq. " The selected billing option " .$row[24]. " requires a comment e.g. Voucher Number or Cash amount.</err>";
           }
       }


       //Check we have a pilot and matching billing option
       if ($row[10] != 0)  //This means we bill PIC then we must have both a PIC and a Billing person for PIC
       {
            if ($row[4] <= 0 || $row[4] == null)
	    {
                 if ($row[11] !=0)
                 {
                        $errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " Split charge between PIC/P2 but PIC not specified</err>";
                 }
      		 else
                 {
                        $errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " Charge PIC specified but there is no PIC</err>";
                 }
            }
            //Check we have either a bill_member1 or a bill_member2
            if ($row[13] <= 0 || $row[13] == null)
            {
	         if ($row[14] <= 0 || $row[14] == null)
                 {
                        $errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " No billing member set of billing</err>";
		 }
	    }
       }   
       //Check we have a pilot and matching billing option
       if ($row[11] != 0)  //This means we bill P2 then we must have both a P2 and a Billing person for P2
       {
            if ($row[5] <= 0 || $row[5] == null)
	    {
                 if ($row[10] !=0)
                 {
			$errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " Split charge between PIC/P2 but P2 not specified</err>";
		 }
      		 else
		 {
			$errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " Charge P2 specified but there is no P2</err>";
		 }
            }
            //Check we have either a bill_member1 or a bill_member2
            if ($row[13] <= 0 || $row[13] == null)
            {
	         if ($row[14] <= 0 || $row[14] == null)
		 {
			$errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " No billing member set of billing</err>";
		 }
	    }
       }
       
       if ($row[12] != 0) //This is checking that the someone else is billable.
       {
            //Check we have either a bill_member1 or a bill_member2
            if ($row[13] <= 0 || $row[13] == null)
            {
	         if ($row[14] <= 0 || $row[14] == null)
		 {
			$errdet=1; 
                 	echo "<err>Flight Seq: " .$thisseq. " Bill another member selected, but no member has been selected.</err>";
		 }
	    }
       }

       
       //If bill other and its set to either PIC and or P2 then needs to be changed to Bill Pic or p2
       if ($row[12] != 0)
       {
           if ($row[13] == $row[4])
           {
	      $errdet=1; 
              echo "<err>Flight Seq: " .$thisseq. " Bill another member selected, but other member is PIC, change Billing to Charge PIC</err>";
           }

           if ($row[13] == $row[5])
           {
	      $errdet=1; 
              echo "<err>Flight Seq: " .$thisseq. " Bill another member selected, but other member is P2, change Billing to Charge P2</err>";
           }
       }


       //Now we need to check that the billing members are not short term members.
       if ($row[13] != null && $row[15] == $shorttermclass)
       {
	//This is OK if a Visiting Pilot
        if (stripos($row[20],'VISITING') === false) 
        {
           $errdet=1;
           if ($row[21] == $flightTypeGlider)
               echo "<err>Flight Seq: " .$thisseq. " The billing option has been specified for a Short Term Member who has no account, you should use a Trial Billing Option.</err>";
           if ($row[21] == $flightTypeRetrieve)
               echo "<err>Flight Seq: " .$thisseq. " The billing option has been specified for a Short Term Member who has no account for a retrieve, you should use a valid member.</err>";

        }
       }  
       if ($row[14] != null && $row[16] == $shorttermclass)
       {
	//This is OK if a Visiting Pilot
        if (stripos($row[20],'VISITING') === false) 
        {
	 $errdet=1; 
         echo "<err>Flight Seq: " .$thisseq. " The billing option has been specified for a Short Term Member who has no account, you should use a Trial Billing Option. Row 20 = ".$row[20]."</err>";
        }
       }    
       
       //Check that glider hasn't had two flights
       $q6 = "SELECT flights.seq, (flights.start/1000) from flights where flights.org = ".$org." and flights.deleted != 1 and flights.localdate=" . $datestr . " and flights.glider = '" .$row[2]. "' and flights.id <> " . $row[18];
       $r6 = mysqli_query($con,$q6);
       while ($row6 = mysqli_fetch_array($r6) )
       {
            if ($row6[1] < $row[8]  && $row6[1] > $row[7])
            {
	       $errdet=1; 
               echo "<err>Flight Seq: " .$thisseq. " This same glider is flying at the same time as flight record ".$row6[0] . ", you can try and check GPS records for correct times.</err>";
            
            }
       }

       if ($errdet==0)
       {
          //the record is good so we can update it as finalised
	  $q1 = "UPDATE flights set finalised=1 where id = " . $row[18];
          $r2 = mysqli_query($con,$q1);
       }
       else
       {
          echo  "<diag>ERROR IN SEQ ".$thisseq."</diag>";
          $someerrors=1; 
       }   
   }

   if ($someerrors==0)
   {
        $q="DELETE FROM flights where deleted=1";
        mysqli_query($con,$q);
        
       //Now we need to reasign the sequences.
       $seqs = 1;
       $q="SELECT id from flights where flights.org = ".$org." and flights.deleted != 1 and flights.localdate=" . $datestr . " order by seq ASC";
       $r=mysqli_query($con,$q);
       while ($row = mysqli_fetch_array($r) )
       {
          $q1="UPDATE flights set seq=".$seqs." where id = " .$row[0];
          mysqli_query($con,$q1);
          $seqs = $seqs + 1;
       }
   }
   mysqli_close($con);
}
echo "</checks>";
?>
