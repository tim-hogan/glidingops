<?php
$DEBUG=0;
$diagtext="";
include 'helpers.php';
if ($_SERVER["REQUEST_METHOD"] == "POST")
{	
        
    header('Content-type: text/csv');
	$org = $_POST["org"];
    $inc = "./orgs/" . $org . "/accountrules.php"; 
    include $inc;
	$dateStart = new DateTime();
    $dateEnd = new DateTime();
    $dateStart->setDate($_POST["year"], $_POST["month"], 1);
    $month = $_POST["month"];
    $year = $_POST["year"];
    $month = $month + 1;
    if ($month > 12)
    {
        $month = 1;
        $year = $year + 1;
    }
    $dateEnd->setDate($year,$month,1);

    $dateStart2 = $dateStart->format('Ymd');
    $dateEnd2 = $dateEnd->format('Ymd');
	
    $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
	$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
	if (mysqli_connect_errno())
	{
 		echo "<p>Unable to connect to database</p>";
 		exit();
	}

    $towlaunch = getTowLaunchType($con);
    $selflaunch = getSelfLaunchType($con);
    $winchlaunch = getWinchLaunchType($con);
        
    $flightTypeGlider = getGlidingFlightType($con); 
    $flightTypeCheck = getCheckFlightType($con); 
    $flightTypeRetrieve = getRetrieveFlightType($con); 
        
    //find the juinor class id
	$juniorclass = getJuniorClass($con,$org);

	//find the No Charge id
	$NoChargeId = getNoChargeOpt($con);
	
	//find the Trial Flight ID's
	$trialFlightIds = getTrialFlightOpts($con);
	
    $towChargeType = getTowChargeType($con,$org);

	$clubgliders = array();

	//Build array of club gliders
	$q="SELECT rego_short FROM aircraft where aircraft.org = ".$org." and club_glider > 0";
	$r = mysqli_query($con,$q);
	$cnt = 0;
	while ($row = mysqli_fetch_array($r) )
	{
  		$clubgliders[$cnt]=$row[0];
  		$cnt++;
	}

	
    //We need to do the counts.
	$q="SELECT count(id) from flights where flights.org = ".$org." and flights.finalised > 0 and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2  ;       
	$r = mysqli_query($con,$q);
	$row = mysqli_fetch_array($r);
	$totalflights = $row[0];
        
	echo "NO CHARGE FLIGHTS,,,,,,,,,\r\n";
    echo "Bill To,Date,Location,Glider,PIC,P2,";
    if ($towChargeType==2)
        echo "Tow Time,";
    echo "Duration,";
    if ($towChargeType==2)
          echo "Launch type";
	else
          echo "Height";
    echo ",Type,Tow,Glider,Airways,Total,Notes\r\n";
	$q="SELECT flights.localdate,flights.glider, (flights.land-flights.start),flights.height, b.displayname, c.displayname,flights.comments, a.name, flights.launchtype, flights.towplane, flights.location, (flights.towland-flights.start) from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b on b.id = flights.p2 LEFT JOIN members c on c.id = flights.pic where flights.org = ".$org." and flights.type = ".$flightTypeGlider." and flights.finalised > 0 and flights.billing_option = ".$NoChargeId." and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2 . " order by localdate,seq ASC";
    $r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
    {
        echo "No Charge,";
        $datestrflight=$row[0];
        $flightDate = new DateTime();
        $flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));
        $strdate=$row[0];
        echo substr($datestrflight,6,2) . "/" . substr($datestrflight,4,2) . "/" . substr($datestrflight,0,4);
        echo ",";
        echo removecomma($row[10]);
        echo ",";
        echo removecomma($row[1]);
        echo ",";
        echo removecomma($row[5]);
        echo ",";
        echo removecomma($row[4]);
        echo ",";
        if ($towChargeType==2)
            echo strDuration($row[11]) . ",";
          
        echo strDuration($row[2]) . ",";
        $totMins = floor($row[2] / 60000);

        if ($row[8] == $towlaunch)
        {
        if ($towChargeType==2)
            echo "AEROTOW";
	    else
	        echo removecomma($row[3]);
        }
        if ($row[8] == $selflaunch)
	    echo "SELF";
		 
        if ($row[8] == $winchlaunch)
	        echo "WINCH";
        echo ",";
        echo removecomma($row[7]) . ",";

        $towcost=0.0;
        $towcost = CalcTowCharge2($org,$row[8],$row[9],$row[11],$row[3],"",1,0);          

	    if ($towcost < 0.00) echo "ERROR"; else echo sprintf("%01.2f",$towcost); echo ",";
        $glidcost =0.0;
        $glidcost = CalcGliderCharge($org,1,$row[1],0,0.00,0,$totMins,"");
        echo sprintf("%01.2f",$glidcost); echo ",";
	    $airwaycost=0.0;
        $airwaycost=CalcOtherCharges($org,$row[10],1,0,$juniorclass,$flightDate,0,0);
	    echo sprintf("%01.2f",$airwaycost); echo ",";    
        echo sprintf("%01.2f",($towcost+$glidcost+$airwaycost)); echo ",";
        echo removecomma($row[6]);
        echo ",";
        echo "\r\n";
    }


	echo ",,,,,,,,,\r\n";
    echo "TRIAL FLIGHTS,,,,,,,,,\r\n";
    echo "Bill To,Date,Location,Glider,PIC,P2,";
    if ($towChargeType==2)
        echo "Tow Time,";
	echo "Duration,";
    if ($towChargeType==2)
	    echo "Launch Type";
    else
        echo "Height";
    echo ",Type,Tow,Glider,Airways,Total,Notes\r\n";
	
	$q="SELECT flights.localdate,flights.glider, (flights.land-flights.start),flights.height, b.displayname, c.displayname,flights.comments, a.name, flights.launchtype, flights.towplane, flights.location , (flights.towland-flights.start) from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b on b.id = flights.p2 LEFT JOIN members c on c.id = flights.pic where flights.org = ".$org." and flights.finalised > 0 and flights.billing_option <> ".$NoChargeId." and flights.billing_option IN ('" . implode("','",$trialFlightIds) . "') and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2 . " order by localdate,seq ASC";
    $r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
    {
        echo "Trial,";
        $datestrflight=$row[0];
  	    $flightDate = new DateTime();
        $flightDate->setDate(substr( $datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));
        echo substr( $datestrflight,6,2) . "/" . substr( $datestrflight,4,2) . "/" . substr( $datestrflight,0,4) . ",";
	    echo removecomma($row[10]) . ",";
	    echo removecomma($row[1]) . ",";
	    echo removecomma($row[5]) . "," ;
	    echo removecomma($row[4]) . ",";
 
        if ($towChargeType==2)
            echo strDuration($row[11]) . ",";
        echo strDuration($row[2]) . ",";
        $totMins = floor($row[2] / 60000);

        if ($row[8] == $towlaunch)
        {
            if ($towChargeType==2)
                echo "AEROTOW";
	        else
	            echo removecomma($row[3]);
        }
        if ($row[8] == $selflaunch)
	        echo "SELF";
		 
        if ($row[8] == $winchlaunch)
	        echo "WINCH";
 
        echo ",";
        
        echo removecomma($row[7]) . ",";

        $towcost=0.0;
        $towcost = CalcTowCharge2($org,$row[8],$row[9],$row[11],$row[3],"",1,0);          

	    if ($towcost < 0.00) echo "ERROR"; else echo sprintf("%01.2f",$towcost); echo ",";
        $glidcost =0.0;
        $glidcost = CalcGliderCharge($org,1,$row[1],0,0.00,0,$totMins,"");
            echo sprintf("%01.2f",$glidcost); echo ",";
	    $airwaycost=0.0;
        $airwaycost=CalcOtherCharges($org,$row[10],1,0,$juniorclass,$flightDate,0,0);
	    echo sprintf("%01.2f",$airwaycost); echo ",";    
        echo sprintf("%01.2f",($towcost+$glidcost+$airwaycost)); echo ",";
	    echo removecomma($row[6]) . ",";
        echo "\r\n";

	}
	
	echo ",,,,,,,,,\r\n";
    echo "OTHER CLUBS,,,,,,,,,\r\n";
    echo "Bill To,Date,Location,Glider,PIC,P2,";
    if ($towChargeType==2)
        echo "Tow Time,";
	echo "Duration,";
    if ($towChargeType==2)
	    echo "Launch Type";
    else
        echo "Height";
    echo ",Type,Tow,Glider,Airways,Total,Notes\r\n";
	
	//Main Loop for other clubs
    $q="SELECT id, name FROM billingoptions WHERE other_club = 1 order by name ASC";
	$r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
    {	
		$q="SELECT flights.localdate,flights.glider, (flights.land-flights.start),flights.height, b.displayname, c.displayname,flights.comments, a.name, flights.launchtype, flights.towplane, flights.location , (flights.towland-flights.start) , seq from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b on b.id = flights.p2 LEFT JOIN members c on c.id = flights.pic where flights.org = ".$org." and flights.type = ".$flightTypeGlider." and flights.finalised > 0 and flights.billing_option = ".$row[0]." and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2 . " order by localdate,seq ASC";
		//0 localdate
		//1 glider
		//2 land-start
		//3 height
		//4 display name 1
		//5 display name 2
		//6 comments
		//7 billing option
		//8 launch type
		//9 two plane
		//10 location
		//11 tow land - start
		//12 seq	
		
		$r = mysqli_query($con,$q);
		while ($row2 = mysqli_fetch_array($r) )
		{				
			echo removecomma($row[1]). ",";
			echo removecomma($row2[0]). ",";
			echo removecomma($row2[10]). ",";
			echo removecomma($row2[1]). ",";
			echo removecomma($row2[4]). ",";
			echo removecomma($row2[5]). ",";
            if ($towChargeType==2)
                echo strDuration($row2[11]) . ",";
			echo strDuration($row2[2]) . ",";			
            if ($row2[8] == $towlaunch)
            {
                if ($towChargeType==2)
                    echo "AEROTOW";
	            else
	                echo removecomma($row2[3]);
            }			
			echo",";
		    if ($row2[8] == $selflaunch)
			    echo "SELF";
            if ($row2[8] == $winchlaunch)
			    echo "WINCH";			
			if ($row2[8] == $towlaunch)
				echo "AEROTOW";
			echo",";
			
	        $datestrflight=$row[0];
			$flightDate = new DateTime();
			$flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));			
			
            //Calculate tow charges
            $towcost=0.0;
			if ($row2[8] == $towlaunch){
				$clubGlid=0;
				if (in_array($row2[1],$clubgliders))
					$clubGlid=1;				
				$towcost = CalcTowCharge2($org,$row2[8],$row2[9],$row2[11],$row2[3],"",$clubGlid,0);
			}
			else
			if ($row2[8] == $winchlaunch)
				  $towcost = CalcWinchCharge($con,$org,$row2[19],$flightDate);
            if ($towcost < 0.00)
			    echo "ERROR";
		    else
				echo sprintf("%01.2f",$towcost);
			echo ",";
			
			$totMins = floor($row2[2] / 60000);
			$glidcost = CalcGliderCharge($org,1,$row2[1],0,0.00,0,$totMins,"");
			echo $glidcost.",";

			$airwaycost=CalcOtherCharges($org,$row2[10],1,0,$juniorclass,$flightDate,0,$row2[12]);
			echo $airwaycost.",";
;
			echo ($towcost+$glidcost+$airwaycost);	
			
			echo "\r\n";
		}			
		
	}
	

	echo ",,,,,,,,,\r\n";
	echo "MEMBERS,,,,,,,,,,,,,,\r\n";
    echo ",,,,,,,,,Bill Member,,,,,,Ledger if no Scheme\r\n";
    echo "Bill To,Date,Location,Glider,PIC,P2,";
    if ($towChargeType==2)
          echo "Tow Time,";
    echo "Duration,";
    if ($towChargeType==2)
	    echo "Launch Type";
    else
 	    echo "Height";
	echo ",Charge,Tow,Glider,Airways,Total,Notes,Scheme,Tow,Glider,Airways,Total\r\n";

    //Main Loop for members accounts
    $q="SELECT members.id, members.class , members.displayname, a.class from members LEFT JOIN membership_class a ON a.id = members.class  order by members.surname,members.firstname ASC";
        
	$r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
    {
        $totamount=0.0;
        $newmember = 1;
        $tablestart = 0;
        //Now do we have any flights to be billed to this member
        $q2 = "SELECT flights.localdate, flights.glider, (flights.land-flights.start),flights.height, flights.pic, flights.p2, a.name, a.bill_pic , a.bill_p2, a.bill_other, flights.comments, flights.billing_member1, flights.billing_member2 ,b.displayname , c.displayname ,d.displayname, e.displayname, flights.launchtype, flights.towplane, flights.location, flights.type , (flights.towland-flights.start) , seq from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b ON b.id = flights.billing_member1 LEFT JOIN members c ON c.id = flights.billing_member2 LEFT JOIN members d ON d.id = flights.pic LEFT JOIN members e ON e.id = flights.p2 where flights.org = ".$org." and flights.finalised > 0 and localdate >= '" . $dateStart2 . "' and localdate < '" . $dateEnd2 . "' and (billing_member1 = " . $row[0] . " or billing_member2 = " . $row[0] . ") order by localdate,seq ASC"; 
	//0 locadate
        //1 glider
        //2 land - start
        //3 height
        //4 pic
        //5 p2
        //6 billing options
        //7 a.bill pic
        //8 a.bill p2
        //9 a.bill other
        //10 comments
        //11 billing member 1
        //12 billing member 2
        //13 billing member 1 displayname
        //14 billing member 2 displayname
        //15 member name pic
        //16 member name p2
        //17 launch type
        //18 tow plane
        //19 locatione
        //20 type
        //21 tow land - start
        //22 seq    
	     
        $r2 = mysqli_query($con,$q2);
        while ($row2 = mysqli_fetch_array($r2))
	    {
            $comments="";
            $datestrflight=$row2[0];
  		    $flightDate = new DateTime();
  		    $flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));

		    $havememberflight=1;
		    echo removecomma($row[2]) , ",";

            echo substr($datestrflight,6,2) . "/" . substr($datestrflight,4,2) . "/" . substr($datestrflight,0,4) . ",";
		    echo removecomma($row2[19]) . ",";
            echo removecomma($row2[1]) . ",";
		    echo removecomma($row2[15]) . ","; 
            echo removecomma($row2[16]) . ","; 

            if ($towChargeType==2)
                    echo strDuration($row2[21]) . ",";
            echo strDuration($row2[2]) . ",";
                 
		    $totMins = floor($row2[2] / 60000);

            if ($row2[17] == $towlaunch && $row2[20] == $flightTypeGlider)
            {
                if ($towChargeType==2)
                    echo "AEROTOW";
	            else
	                echo removecomma($row2[3]);
            }	
		    if ($row2[17] == $selflaunch)
			    echo "SELF";
            if ($row2[17] == $winchlaunch)
			    echo "WINCH";
	        echo ",";
		    echo removecomma($row2[6]) . ",";

		
	        //Check that this person has an ecentive scheme valid for this date.
            $iRateGlider = 0.0;
		    $schemename="";
		    $iChargeTow=1;
		    $iChargeAirways=1;
            $iScheme = 0;
            if (MemberScheme($org,$row[0],$flightDate,$row2[1],$iRateGlider,$iChargeTow,$iChargeAirways,$schemename) > 0)
                $iScheme = 1;
		
                
            $SchemeCharge=0;
            $clubGlid=0;
            if (in_array($row2[1],$clubgliders))
    	        $clubGlid=1;

		    $is5050 = 0;
  		    //Is this a 50/50
  		    if ($row2[7] > 0 && $row2[8] > 0)
     			    $is5050=1;
		    if ($iScheme>0)
		          $SchemeCharge = 1;
  		

                
		    //If this person is on a scheme he/she must be PIC or P2
  		    if ($iScheme>0)
  		    {
     		    if ($row[0] == $row2[4] || $row[0] == $row2[5])
        		    $SchemeCharge = 1;
      		    else
                {
        		    $SchemeCharge = 0;
			        $iChargeAirways=1;
                }
  		    }
            //Calculate tow charges
            $towcost=0.0;
            if ($row2[20] == $flightTypeRetrieve)
                $towcost=CalcTowRetrieve($org,$row2[18],($row2[2]/1000));
		    else
            {
                if ($row2[17] == $towlaunch)
                    $towcost = CalcTowCharge2($org,$row2[17],$row2[18],$row2[21],$row2[3],$row[3],$clubGlid,$is5050);
                else
                    if ($row2[17] == $winchlaunch)
                    {
    
                    $towcost = CalcWinchCharge($con,$org,$row2[19],$flightDate);
                    }
                        
                $towcost2 = $towcost;                              
		        if ($SchemeCharge > 0 && $iChargeTow == 0)
                     $towcost=0.0;


            }
		    if ($towcost < 0.00)
			    echo "ERROR";
		    else
                echo sprintf("%01.2f",$towcost);
		    echo ",";

		    //Calculate glider charges
            $glidcost=0.0;
            $glidcost=CalcGliderCharge($org,$clubGlid,$row2[1],$SchemeCharge,$iRateGlider,$is5050,$totMins,$row[3]);
		    $glidcost2=CalcGliderCharge($org,$clubGlid,$row2[1],0,$iRateGlider,$is5050,$totMins,$row[3]);
   		    echo sprintf("%01.2f",$glidcost) . ",";
	        
		    //Calculate airways charges
		    $airways=0.00;
            $airways2=0.00;
            $airways = CalcOtherCharges($org,$row2[19],$clubGlid,$row[1],$juniorclass,$flightDate,$is5050,$row[0],$row2[22]);     		
            $airways2=$airways;	
		    if ($iChargeAirways == 0)
			    $airways=0.00;
        	echo sprintf("%01.2f",$airways) , ",";
		    
   		    echo sprintf("%01.2f",($towcost+$glidcost+$airways)) . ",";

		    $totamount = $totamount + ($towcost+$glidcost+$airways);
		    
            if ($is5050)
   		    {
     			 $comments = "Charge 50/50 with ";
     			 if ($row2[11] != $row[0])
          			$comments .= $row2[13];
      			 if ($row2[12] != $row[0])
          			$comments .= $row2[14];
		    }
       		$comments .= " " .$row2[10];
      		    
            if ($row2[20] == $flightTypeRetrieve)
            {
		        if (strlen($comments) > 0)
                    $comments .= " ";
                $comments .= "Charges for retrieve";
            }
            echo removecomma($comments) . ",";
  
		    
		    if ($SchemeCharge==1)
                echo removecomma($schemename);
      		echo ",";
	        //Now about stuff if there is no scheme
		    if ($towcost2 < 0.00)
			    echo "ERROR";
		    else
                echo sprintf("%01.2f",$towcost2);
		    echo ",";
		    echo sprintf("%01.2f",$glidcost2) . ",";

            echo sprintf("%01.2f",$airways2) . ",";
		    echo sprintf("%01.2f",($towcost2+$glidcost2+$airways2));

            echo "\r\n";
        }
	}
	
    mysqli_close($con);
}
if ($DEBUG>0) echo $diagtext;
?>
