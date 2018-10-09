<?php

namespace App\Services\Wgc;

use App\Models\LaunchType;

class Accounting
{
  public static function calcFlightCharges($flight)
  {
    $charges = [
    ];

    $gliderAircraft = $flight->gliderAircraft();
    if(!$gliderAircraft) {
      throw new Exception("Glider {$flight->glider} not found.");
    }

    $charges['glider'] = self::calcGliderCharge($flight, $gliderAircraft);
    if($flight->launchType == LaunchType::winchLaunchType()) {
      $charges['winchLaunch'] = self::calcWinchCharge($flight);
    }

    return [
      ['member' => $flight->p2Member, 'charges' => $charges]
    ];
  }

  private static function calcWinchCharge($flight)
  {
    return 45;
  }

  private static function calcGliderCharge($flight, $gliderAircraft)
  {
    $flightDuration = $flight->getFlightDuration();
    $minutes = min($flightDuration, $gliderAircraft->max_perflight_charge);
    return $minutes * $gliderAircraft->charge_per_minute;
  }
}