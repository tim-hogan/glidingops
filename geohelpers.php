<?php
function DistKM($lat,$long,$dlat,$dlong)
{
  $toRadians = (3.14159265358979 / 180.0);
  $latFrom = $lat * $toRadians;
  $longFrom = $long * $toRadians;
  $latTo = $dlat * $toRadians;
  $longTo = $dlong * $toRadians;
  $theta = 0;
  $theta = sin($latFrom) * sin($latTo) + (cos($latFrom) * cos($latTo) * cos($longFrom-$longTo)); 
  return (6378.15 * acos($theta));
}
function Bearing($lat1,$lon1,$lat2,$lon2)
{
  $d2r = 3.141592653585 / 180.0;
  $lat1 = $lat1*$d2r;
  $lon1 = $lon1*$d2r;
  $lat2 = $lat2*$d2r;
  $lon2 = $lon2*$d2r;

  $angle = - atan2( sin( $lon1 - $lon2 ) * cos( $lat2 ), cos( $lat1 ) * sin( $lat2 ) - sin( $lat1 ) * cos( $lat2 ) * cos( $lon1 - $lon2 ) );
  if ( $angle < 0.0 ) $angle  += 3.141592653585 * 2.0;
  if ( $angle > 3.141592653585 ) $angle -= 3.141592653585 * 2.0; 
  return $angle;
}

function DestLat($lat,$lon,$b,$r)
{
  $RR = 6378137.0;// earth's mean radius in meters
  return asin( sin($lat)*cos($r/$RR) + cos($lat)*sin($r/$RR)*cos($b) );
}

function DestLon($lat,$lon,$b,$r)
{
  $RR = 6378137.0;// earth's mean radius in meters
  return $lon + atan2(sin($b)*sin($r/$RR)*cos($lat), 
                             cos($r/$RR)-sin($lat)*sin($lat));
}

function drawArc($cenLat,$cenLon,$startLat,$startLon,$endLat,$endLon,$dist,$dir,&$ptsLat,&$ptsLon)
{
    $next=0;
    $dlat=0;
    $dlon=0;
    $deltaBearing=0;
    $numPoints = 32;
    $r2d = 180.0 / 3.141592653585;
    $d2r = 3.141592653585 / 180.0;
    $be1 = Bearing($cenLat,$cenLon,$startLat,$startLon);
    $be2 = Bearing($cenLat,$cenLon,$endLat,$endLon);
    $dist = $dist * 1852;
    if ($dir==0)  //Clockwise
    {
     if ($be1 > $be2) $be2 += (2.0*3.141592653585);
     $deltaBearing = $be2 - $be1;
     $deltaBearing = $deltaBearing/$numPoints;
    }
    else
    {
     if ($be2 > $be1) $be1 += (2.0*3.141592653585);
     $deltaBearing = $be1 - $be2;
     $deltaBearing = $deltaBearing/$numPoints;
    }
    $done1 = 0;
    for ($i=0; ($i < $numPoints+1); $i++) 
    { 
    if ($dir==0)  //Clockwise
    {
      $dlat = DestLat($cenLat*$d2r,$cenLon*$d2r,$be1 + $i*$deltaBearing,$dist) * $r2d;
      $dlon = DestLon($cenLat*$d2r,$cenLon*$d2r,$be1 + $i*$deltaBearing,$dist) * $r2d;
    }
    else
    {
      $dlat = DestLat($cenLat*$d2r,$cenLon*$d2r,$be1 - $i*$deltaBearing,$dist) * $r2d;
      $dlon = DestLon($cenLat*$d2r,$cenLon*$d2r,$be1 - $i*$deltaBearing,$dist) * $r2d;
    }
      $ptsLat[$next] = $dlat;
      $ptsLon[$next] = $dlon;
      $next++;    
    } 

}

function buildArea($db,$areaid,&$vertx,&$verty,$closeloop)
{
  $firstlat = 0;
  $firstlon = 0;
  $nextCCA = 0;
  $nextCWA = 0;
  $arc1 =  0;
  $arc2 =  0;
  $arc3 =  0;
  $arc4 =  0;
  $arc5 =  0;
  $cnt=0;
  $q="SELECT type,lattitude,longitude,arclat,arclon,arcdist from airspacecoords where airspace = " .$areaid. " order by seq";
  $r = mysqli_query($db,$q);
  while ($row = mysqli_fetch_array($r))
  {
    
    if ($firstlat == 0 && $firstlon == 0)
    {
        $firstlat = $row[1];
	$firstlon = $row[2];
    }  
    if ($nextCCA == 1 || $nextCWA == 1)
    {
        $Lats = array();
        $Lons = array();

        if ($nextCCA == 1)
             drawArc($arc1,$arc2,$arc3,$arc4,$row[1],$row[2],$arc5,1,$Lats,$Lons);
        else
             drawArc($arc1,$arc2,$arc3,$arc4,$row[1],$row[2],$arc5,0,$Lats,$Lons);

        $nextCCA=0;
        $nextCWA=0;

        for ($i=0;$i<count($Lats);$i++)
        {
            $vertx[$cnt] = $Lats[$i];
            $verty[$cnt] = $Lons[$i];
            $cnt++;
        }
       
    }
    else
    {
       if (strtoupper($row[0]) == 'GRC')
       {
            $vertx[$cnt] = $row[1];
            $verty[$cnt] = $row[2];
            $cnt++;
       }

       if (strtoupper($row[0]) == 'CCA')
       {
         $nextCCA = 1;
         $arc1 =  $row[3];
         $arc2 =  $row[4];
         $arc3 =  $row[1];
         $arc4 =  $row[2];
         $arc5 =  $row[5];
       }
       if (strtoupper($row[0]) == 'CWA')
       {
         $nextCWA = 1;
         $arc1 =  $row[3];
         $arc2 =  $row[4];
         $arc3 =  $row[1];
         $arc4 =  $row[2];
         $arc5 =  $row[5];
       }    
    }

  }
  if ($nextCCA == 1 || $nextCWA == 1)
  {
     $Lats = array();
     $Lons = array();

     if ($nextCCA == 1)
             drawArc($arc1,$arc2,$arc3,$arc4,$firstlat,$firstlon,$arc5,1,$Lats,$Lons);
        else
             drawArc($arc1,$arc2,$arc3,$arc4,$firstlat,$firstlon,$arc5,0,$Lats,$Lons);
      for ($i=0;$i<count($Lats);$i++)
      {
         $vertx[$cnt] = $Lats[$i];
         $verty[$cnt] = $Lons[$i];
         $cnt++;
      }  
  }
  else
  {
   if ($firstlat != 0 || $firstlon != 0)
   {
      $vertx[$cnt] = $firstlat;
      $verty[$cnt] = $firstlon;
      $cnt++;
   }
  }

  //If its not a closed loop we need to pop off the end.
  if (!$closeloop)
  {
     array_pop($vertx); 
     array_pop($verty); 
  }

}

function pointInArea($vertx,$verty,$testx,$testy)
{  
    $nv = count($vertx);
    $i = 0;
    $j = 0;
    $c = 0;
    for ($i = 0, $j = $nv-1; $i < $nv; $j = $i++) {
    if ( (($verty[$i]>$testy) != ($verty[$j]>$testy)) &&
     ($testx < ($vertx[$j]-$vertx[$i]) * ($testy-$verty[$i]) / ($verty[$j]-$verty[$i]) + $vertx[$i]) )
    {
       $c = !$c;
    }
  }
  return $c;
}
?>