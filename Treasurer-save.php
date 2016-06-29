<?php session_start(); ?>
<?php $org=0; if(isset($_SESSION['org'])) $org=$_SESSION['org'];?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style><?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<style>
@media print {
    th {font-size: 10px;padding-left: 1em;}
    td {font-size: 10px;padding-left: 1em;}
    th.thname {font-size: 12px;padding-left: 0em;text-align: left;}
    h1 {font-size: 18px;}
    h2 {font-size: 16px;}
    h3 {font-size: 14px;}
    .indent1 {padding-left: 2em;}
    #print-button {display: none;}
    #divhdr {display: none;}
    #head {display: none;}
    @page {size: landscape;}
}
@media screen {
     th {font-size: 11px;padding-left: 16px;}
     td {font-size: 11px;padding-left: 16px;}
     th.thname {font-size: 14px;padding-left: 0px;text-align: left;}
     h1 {font-size: 20px;}
     h2 {font-size: 18px;}
     h3 {font-size: 16px;}
     .indent1 {padding-left: 20px;}
}
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
table {border-collapse: collapse;}
.right {text-align: right;}
.left {text-align: left;}
.bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
</style>
<script>function goBack() {window.history.back()}</script>
<script>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   echo "var year=" . $_POST["year"] . ";";
   echo "var month=" . $_POST["month"] . ";";
}
else
{
   echo "var year=0;"; 
   echo "var month=0;"; 
}
?>
function printit(){window.print();}
function ModForm(){var d = document.getElementById('inform');if (null !=d)d.setAttribute("action","/GlideAccounts.csv");}
function ModForm2(){var d = document.getElementById('inform');if (null !=d)d.setAttribute("action","/Treasurer.php");}
function s()
{
	console.log("In S");
        if (year != 0)
        {
           var y=document.getElementById('yrs').childNodes;
           for(x=0;x<y.length;x++)
	   {
               console.log("Opt Yr");
               if (y[x].value == year)
                  y[x].selected=true;
	   }
	}
	if (month != 0)
        {
           var y=document.getElementById('mts').childNodes;
           for(x=0;x<y.length;x++)
	   {
               console.log("Opt Yr");
               if (y[x].value == month)
                  y[x].selected=true;
	   }
	}
}
</script>
</head>
<body onload='s()'>
<?php
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
?>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<div id='divhdr'>
<form id='inform' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2>Select Month and Year</h2>
<select name='month' id='mts'>
<option value='1'>Jan</option>
<option value='2'>Feb</option>
<option value='3'>Mar</option>
<option value='4'>Apr</option>
<option value='5'>May</option>
<option value='6'>Jun</option>
<option value='7'>Jul</option>
<option value='8'>Aug</option>
<option value='9'>Sep</option>
<option value='10'>Oct</option>
<option value='11'>Nov</option>
<option value='12'>Dec</option>
</select>
<select name='year' id='yrs'>
<option value='2014'>2014</option>
<option value='2015' selected>2015</option>
<option value='2016'>2016</option>
<option value='2017'>2017</option>
</select>
<input type='hidden' name='org' value='<?php echo $_SESSION['org'];?>'>
<br><input type='submit' name='view' onclick='ModForm2()' value='View Report'>
<button form='inform' type='submit' name='export' onclick='ModForm()'>Export to Excel</button>
</form>
</div>
<?php $inc = "./orgs/" . $org . "/accountrules.php"; include $inc; ?>
<?php
$DEBUG=1;
$diagtext="";
include 'helpers.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{	
        
	
	$totalflights = 0;
	$totaltrials = 0;
	$totalmemflights=0;
        $total5050flights=0;
        $totalCheckFlights=0;
        $totalRetrieveFlights=0;
	$memberincome=0.0;

	
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

        $clubgliders = array();

	//Build array of club gliders
	$q="SELECT rego_short FROM aircraft where aircraft.org = ".$_SESSION['org']." and club_glider > 0";
	$r = mysqli_query($con,$q);
	$cnt = 0;
	while ($row = mysqli_fetch_array($r) )
	{
  		$clubgliders[$cnt]=$row[0];
  		$cnt++;
	}

	
        //We need to do the counts.
	$q="SELECT count(id) from flights where flights.org = ".$_SESSION['org']." and flights.finalised > 0 and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2  ;       
	$r = mysqli_query($con,$q);
	$row = mysqli_fetch_array($r);
	$totalflights = $row[0];

        echo "<h1>TREASURERS REPORT</h1>";
        echo "<h2>For " . $dateStart->format('F') . " " . $dateStart->format('Y') . "</h2>";
        
        echo "<h2>TUG ONLY CHECK FLIGHTS</h2>";
	$q="SELECT flights.localdate,flights.location,b.rego_short, a.displayname, (flights.land-flights.start),flights.comments from flights LEFT JOIN members a ON a.id = flights.towpilot LEFT JOIN aircraft b ON b.id = flights.towplane where flights.org = ".$_SESSION['org']." and flights.type = ".$flightTypeCheck." and flights.finalised > 0 and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2 . " order by localdate,seq ASC";
        $r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
        {
            $totalCheckFlights += 1;
            if ($totalCheckFlights == 1)
            {
		echo "<table>";
		echo "<tr><th>DATE</th><th>LOCATION</th><th>PLANE</th><th>PILOT</th><th>DURATION</th><th>NOTES</th></tr>";
            } 
	    $datestrflight=$row[0];
  	    $flightDate = new DateTime();
  	    $flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));
            
            echo "<tr>";
	    $strdate=$row[0];
            echo "<td>";echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);echo "</td>";
	    echo "<td>".$row[1]."</td>";
	    echo "<td>".$row[2]."</td>";
	    echo "<td>".$row[3]."</td>";
	    $duration = intval($row[4] / 1000);
            $hours = intval($duration / 3600);
            $mins = intval(($duration % 3600) / 60);
	    $totMins = (($hours*60) + $mins);
            $timeval = sprintf("%02d:%02d",$hours,$mins);
            echo "<td class='right'>";echo $timeval;echo "</td>";
	    echo "<td>".$row[5]."</td>";
            echo "</tr>";
        }
        echo "</table>";
	echo "<h2>NO CHARGE FLIGHTS</h2>";
	$totalfree = 0;
	$q="SELECT flights.localdate,flights.glider, (flights.land-flights.start),flights.height, b.displayname, c.displayname,flights.comments, a.name, flights.launchtype, flights.towplane, flights.location , (flights.towland-flights.start) from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b on b.id = flights.p2 LEFT JOIN members c on c.id = flights.pic where flights.org = ".$_SESSION['org']." and flights.type = ".$flightTypeGlider." and flights.finalised > 0 and flights.billing_option = ".$NoChargeId." and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2 . " order by localdate,seq ASC";
        $r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
        {
	   $totalfree = $totalfree + 1;
           if ($totalfree == 1)
           {
		echo "<table>";
		echo "<tr><th>DATE</th><th>LOCATION</th><th>GLIDER</th><th>DURATION</th><th>HEIGHT</th><th>PIC</th><th>P2</th><th>TYPE</th><th>TOW</th><th>GLIDER</th><th>AIRWAYS</th><th>TOTAL</th><th>NOTES</th></tr>";
           } 
		$datestrflight=$row[0];
  		$flightDate = new DateTime();
  		$flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));

		 echo "<tr>";
		 $strdate=$row[0];
  		 echo "<td>";echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);echo "</td>";
		 echo "<td>";echo $row[10];echo "</td>";
                 echo "<td class='right'>";echo $row[1];echo "</td>";
		 $duration = intval($row[2] / 1000);
  		 $hours = intval($duration / 3600);
  		 $mins = intval(($duration % 3600) / 60);
		 $totMins = (($hours*60) + $mins);
  		 $timeval = sprintf("%02d:%02d",$hours,$mins);
                 echo "<td class='right'>";echo $timeval;echo "</td>";
                 echo "<td class='right'>";echo $row[3];echo "</td>";
		 echo "<td class='right'>";echo $row[5];echo "</td>";
		 echo "<td class='right'>";echo $row[4];echo "</td>";
		 echo "<td>";echo $row[7];echo "</td>";
		 $towcost=0.0;
                 $towcost = CalcTowCharge($_SESSION['org'],$row[8],$towlaunch,$row[9],$row[11],$row[3],0,$juniorclass,1,0);
		 

                 if ($towcost < 0.00)
                 {
		    echo "<td class='right'>ERROR</td>";
                 }
		 else
		 {
                  echo "<td class='right'>$";
                  echo sprintf("%01.2f",$towcost);
		  echo "</td>";
		 }

		 $glidcost =0.0;
                 $glidcost = CalcGliderCharge($_SESSION['org'],1,$row[1],0,0.00,0,$totMins);
		 echo "<td class='right'>$";
                 echo sprintf("%01.2f",$glidcost);
		 echo "</td>";

                 $airwaycost=0.0;
                 $airwaycost=CalcAirwaysCharge($_SESSION['org'],$row[10],1,0,$juniorclass,$flightDate,0);                
		 echo "<td class='right'>$";
                 echo sprintf("%01.2f",$airwaycost);
		 echo "</td>";

		 echo "<td class='right'>$";
                 echo sprintf("%01.2f",($towcost+$glidcost+$airwaycost));
		 echo "</td>";

                 echo "<td>";echo $row[6];echo "</td>";
	}
	if ($totalfree == 0)
	   echo "<p class='indent1'>No flights this month</p>";
        else
	   echo "</table>";
        echo "<h2>TRIAL FLIGHTS</h2>";
	$havetrial = 0;

	$q="SELECT flights.localdate,flights.glider, (flights.land-flights.start),flights.height, b.displayname, c.displayname,flights.comments, a.name, flights.launchtype, flights.towplane, flights.location , (flights.towland-flights.start) from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b on b.id = flights.p2 LEFT JOIN members c on c.id = flights.pic where flights.org = ".$_SESSION['org']." and flights.finalised > 0 and flights.billing_option <> ".$NoChargeId." and a.bill_pic = 0 and a.bill_p2 = 0 and a.bill_other = 0 and localdate >= " . $dateStart2 . " and localdate < " . $dateEnd2 . " order by localdate,seq ASC";
        $r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
        {
		$totaltrials = $totaltrials + 1;
                if ($havetrial == 0)
		{
			
                        $havetrial=1;
			echo "<table>";
			echo "<tr><th>DATE</th><th>LOCATION</th><th>GLIDER</th><th>DURATION</th><th>HEIGHT</th><th>NAME</th><th>INSTRUCTOR</th><th>TYPE</th><th>TOW</th><th>GLIDER</th><th>AIRWAYS</th><th>TOTAL</th><th>NOTES</th></tr>";
		}
		$datestrflight=$row[0];
  		$flightDate = new DateTime();
  		$flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));

		 echo "<tr>";
		 $strdate=$row[0];
  		 echo "<td>";echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);echo "</td>";
		 echo "<td>";echo $row[10];echo "</td>";
                 echo "<td class='right'>";echo $row[1];echo "</td>";
		 $duration = intval($row[2] / 1000);
  		 $hours = intval($duration / 3600);
  		 $mins = intval(($duration % 3600) / 60);
		 $totMins = (($hours*60) + $mins);
  		 $timeval = sprintf("%02d:%02d",$hours,$mins);
                 echo "<td class='right'>";echo $timeval;echo "</td>";
                 echo "<td class='right'>";echo $row[3];echo "</td>";
		 echo "<td class='right'>";echo $row[4];echo "</td>";
		 echo "<td class='right'>";echo $row[5];echo "</td>";
		 echo "<td>";echo $row[7];echo "</td>";
		 $towcost=0.0;
                 $towcost = CalcTowCharge($_SESSION['org'],$row[8],$towlaunch,$row[9],$row[11],$row[3],0,$juniorclass,1,0);
		 if ($towcost < 0.00)
                 {
		    echo "<td class='right'>ERROR</td>";
                 }
		 else
		 {
                  echo "<td class='right'>$";
                  echo sprintf("%01.2f",$towcost);
		  echo "</td>";
		 }

		 $glidcost =0.0;
                 $glidcost = CalcGliderCharge($_SESSION['org'],1,$row[1],0,0.00,0,$totMins);
		 echo "<td class='right'>$";
                 echo sprintf("%01.2f",$glidcost);
		 echo "</td>";

                 $airwaycost=0.0;
                 $airwaycost=CalcAirwaysCharge($_SESSION['org'],$row[10],1,0,$juniorclass,$flightDate,0);                
		 echo "<td class='right'>$";
                 echo sprintf("%01.2f",$airwaycost);
		 echo "</td>";

		 echo "<td class='right'>$";
                 echo sprintf("%01.2f",($towcost+$glidcost+$airwaycost));
		 echo "</td>";

                 echo "<td>";echo $row[6];echo "</td>";
	
	}
	if ($havetrial != 0)
		echo "</table>";
	else
	   echo "<p class='indent1'>There were no trial flights for this month</p>";

	$havememberflight=0;
	echo "<h2>MEMBERS ACCOUNTS</h2>";
	echo "<table>";


        //Main Loop for members accounts
        $q="SELECT id, class , displayname from members WHERE members.org = " .$_SESSION['org']. " order by surname,firstname ASC";
        
	$r = mysqli_query($con,$q);
	while ($row = mysqli_fetch_array($r) )
        {
	     
	     $totamount=0.0;
             $newmember = 1;
             $tablestart = 0;
             //Now do we have any flights to be billed to thsi member
             $q2 = "SELECT flights.localdate, flights.glider, (flights.land-flights.start),flights.height, flights.pic, flights.p2, a.name, a.bill_pic , a.bill_p2, a.bill_other, flights.comments, flights.billing_member1, flights.billing_member2 ,b.displayname , c.displayname, d.displayname, e.displayname, flights.launchtype, flights.towplane, flights.location , flights.type , (flights.towland-flights.start) from flights LEFT JOIN billingoptions a ON a.id = flights.billing_option LEFT JOIN members b ON b.id = flights.billing_member1 LEFT JOIN members c ON c.id = flights.billing_member2 LEFT JOIN members d ON d.id = flights.pic LEFT JOIN members e ON e.id = flights.p2 where flights.org = ".$_SESSION['org']." and flights.finalised > 0 and localdate >= '" . $dateStart2 . "' and localdate < '" . $dateEnd2 . "' and (billing_member1 = " . $row[0] . " or billing_member2 = " . $row[0] . ") order by localdate,seq ASC"; 
	     
             $r2 = mysqli_query($con,$q2);
             while ($row2 = mysqli_fetch_array($r2))
	     {
                  $comments="";
		  $totalmemflights = $totalmemflights + 1;
                  $datestrflight=$row2[0];
  		  $flightDate = new DateTime();
  		  $flightDate->setDate(substr($datestrflight,0,4),substr($datestrflight,4,2),substr($datestrflight,6,2));

		 $havememberflight=1;

                 if ($newmember == 1)
                 {
		      echo "<tr><th class='thname' colspan=13>" . $row[2] . "<th></tr>";
                      
                      echo "<tr><th>DATE</th><th>LOCATION</th><th>GLIDER</th><th>PIC</th><th>P2</th><th>DURATION</th><th>HEIGHT</th><th>CHARGING</th><th>TOW</th><th>GLIDER</th><th>AIRWAYS</th><th>TOTAL</th><th>COMMENTS</th><th>INCENTIVE SCHEME USED</th></tr>";
                      $newmember = 0;
                      $tablestart=1; 
		 }
                 echo "<tr>";
		 $strdate=$row2[0];
  		 echo "<td>";echo substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);echo "</td>";
		 echo "<td>";echo $row2[19];echo "</td>";
                 echo "<td class='right'>";echo $row2[1];echo "</td>";
		 $duration = intval($row2[2] / 1000);
  		 $hours = intval($duration / 3600);
  		 $mins = intval(($duration % 3600) / 60);
		 $totMins = (($hours*60) + $mins);
  		 $timeval = sprintf("%02d:%02d",$hours,$mins);
                 echo "<td>";echo $row2[15];echo "</td>";
		 echo "<td>";echo $row2[16];echo "</td>";
		 echo "<td class='right'>";echo $timeval;echo "</td>";
                 echo "<td class='right'>";


		 if ($row2[17] == $towlaunch && $row2[20] == $flightTypeGlider)
			echo $row2[3];

		 if ($row2[17] == $selflaunch)
			echo "SELF";
		 
                 if ($row2[17] == $winchlaunch)
			echo "WINCH";
		 
                 echo "</td>";
		 echo "<td class='right'>";echo $row2[6];echo "</td>";

		//Check that this person has an ecentive scheme valid for this date.
                $iRateGlider = 0.0;
		$schemename="";
		$iChargeTow=1;
		$iChargeAirways=1;
                $iScheme = 0;
                if (MemberScheme($_SESSION['org'],$row[0],$flightDate,$row2[1],$iRateGlider,$iChargeTow,$iChargeAirways,$schemename) > 0)
                    $iScheme = 1;
						
                $clubGlid=0;
  		if (in_array($row2[1],$clubgliders))
    			$clubGlid=1;

		$is5050 = 0;
  		//Is this a 50/50
  		if ($row2[7] > 0 && $row2[8] > 0)
     			$is5050=1;
		
		if ($is5050>0)
                   $total5050flights = $total5050flights + 1;
                $SchemeCharge = $iScheme;
  		//If this person is on a scheme he/she must be PIC or P2
  		if ($iScheme)
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
                     $towcost=CalcTowRetrieve($_SESSION['org'],$row2[18],($row2[2]/1000));
                else
                {
                  $towcost = CalcTowCharge($_SESSION['org'],$row2[17],$towlaunch,$row2[18],$row2[21],$row2[3],$row[1],$juniorclass,$clubGlid,$is5050);
		  if ($SchemeCharge > 0 && $iChargeTow == 0)
                     $towcost=0.0;
                }
		if ($towcost < 0.00)
                {
			echo "<td class='right'>ERROR</td>";
                }
		else
		{
                  echo "<td class='right'>$";
                  echo sprintf("%01.2f",$towcost);
		  
                  echo "</td>";
		}

		$glidcost=0.0;
   		$airways=0.0;
  
		//Calculate glider charges
                $glidcost=0.0;
  		$glidcost=CalcGliderCharge($_SESSION['org'],$clubGlid,$row2[1],$SchemeCharge,$iRateGlider,$is5050,$totMins);
		echo "<td class='right'>$";
        	echo sprintf("%01.2f",$glidcost);
   		

		//Calculate airways charges
		$airways=0.00;
                $airways = CalcAirwaysCharge($_SESSION['org'],$row2[19],$clubGlid,$row[1],$juniorclass,$flightDate,$is5050);     		


                if ($iChargeAirways == 0)
			$airways=0.00;

                echo "<td class='right'>$";
        	echo sprintf("%01.2f",$airways);
        	echo "</td>";

		echo "<td class='right'>$";
   		echo sprintf("%01.2f",($towcost+$glidcost+$airways));
                echo "</td>";

		$totamount = $totamount + ($towcost+$glidcost+$airways);
		$memberincome = $memberincome + ($towcost+$glidcost+$airways);
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

                 echo "<td>";
                 echo $comments;
                 echo "</td>";
   
		 echo "<td>";
		 if ($SchemeCharge==1)
                    echo $schemename;
      		 echo "</td>";
	          
	       }
               if ($tablestart == 1)
	       {
                 echo "<tr><th class='left'>TOTAL</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th class='right'>$";
                 echo sprintf("%01.2f",$totamount);
                 echo "</th><th></th><th></th></tr>";
                
               }

	       
	}
	echo "</table>";
    if($havememberflight==0)
		   echo "<p class='indent1'>There were no members flights this month.</p>";


    echo "<h2>SUMMARY</h2>";
    echo "<table>";
    echo "<tr><td>MEMBER INCOME</td><td class='right'>$";
    echo sprintf("%01.2f",$memberincome);
    echo "</td></tr>";
    
    $totalmemflights = $totalmemflights - ($total5050flights / 2);
    
    echo "<tr><td>TOTAL TUG CHECK FLIGHTS</td><td class='right'>".$totalCheckFlights."</td></tr>";
    echo "<tr><td>TOTAL NO CHARGE FLIGHTS</td><td class='right'>".$totalfree."</td></tr>";
    echo "<tr><td>TOTAL TRIAL FLIGHTS</td><td class='right'>".$totaltrials."</td></tr>";
    echo "<tr><td>TOTAL MEMBERS FLIGHTS</td><td class='right'>".$totalmemflights."</td></tr>";
    echo "<tr><td>GRAND TOTAL</td><td class='right'>".$totalflights."</td></tr>";


    if (($totalCheckFlights+$totalfree+$totaltrials+$totalmemflights) != $totalflights)
    	echo "<tr><td></td><td class='right'>ERROR IN RPORT</td></tr>";
    echo "</table>";
    echo "<p></p>";
    echo "<button onclick='printit()' id='print-button'>Print Report</button>";

    mysqli_close($con);
}
?>





<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
