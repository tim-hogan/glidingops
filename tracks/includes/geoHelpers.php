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