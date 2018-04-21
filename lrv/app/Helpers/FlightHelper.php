<?php

namespace App\Helpers;

use App\Models\FlightType;
use App\Models\Track;
use App\Models\ArchivedTrack;

class FlightHelper {
  public static function towDuration($flight) {
    return intval($flight->towDuration / 1000);
  }

  public static function flightDuration($flight) {
    return intval($flight->flightDuration / 1000);
  }

  public static function startDate($flight) {
    $date = new \DateTime();
    $date->setTimestamp(intval(floor($flight->start / 1000)));
    return $date;
  }

  public static function towLandDate($flight) {
    $date = new \DateTime();
    $date->setTimestamp(intval(floor($flight->towland / 1000)));
    return $date;
  }

  public static function landDate($flight) {
    $date = new \DateTime();
    $date->setTimestamp(intval(floor($flight->land / 1000)));
    return $date;
  }

  public static function fullComments($flight)
    {
        $comments = $flight->comments;
        if ($flight->type == FlightType::checkFlightType()->id) {
            if (strlen($comments) > 0 ) {
              $comments .= " ";
            }
            $comments .= "Tow plane check flight";
        }

        if ($flight->type == FlightType::retrieveFlightType()->id) {
            if (strlen($comments) > 0 ) {
              $comments .= " ";
            }
            $comments .= "Retrieve Flight";
        }

        return $comments;
    }

    public static function hasTracks($flight)
    {
        $startDate = FlightHelper::startDate($flight)->format('Y-m-d H:i:s');
        $landDate = FlightHelper::landDate($flight)->format('Y-m-d H:i:s');

        $tracks = Track::where('glider', $flight->glider)
                    ->where('point_time', '>' , $startDate)
                    ->where('point_time', '<', $landDate);

        if(!$tracks->get()->isEmpty()) {
            return true;
        }

        $tracks = ArchivedTrack::where('glider', $flight->glider)
                    ->where('point_time', '>' , $startDate)
                    ->where('point_time', '<', $landDate);

        if(!$tracks->get()->isEmpty()) {
            return true;
        }

        return false;
    }

    public static function trackURI($flight) {
      $startDate = FlightHelper::startDate($flight)->format('Y-m-d H:i:s');
      $landDate = FlightHelper::landDate($flight)->format('Y-m-d H:i:s');

      return "/MyFlightMap.php?glider={$flight->glider}&from={$startDate}&to={$landDate}&flightid={$flight->id}";
    }
}