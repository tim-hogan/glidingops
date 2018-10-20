<?php

namespace App\Services\Wgc;

use App\Models\LaunchType;
use App\Models\Charge;
use App\Models\BillingOption;

class Accounting
{
  const CHARGE_WINCH = 'Winch';
  const CHARGE_JUNIOR_WINCH = 'Junior Winch';
  const CHARGE_GLIDER_PER_MINUTE = 'Glider per minute';
  const CHARGE_JUNIOR_GLIDER_PER_MINUTE = 'Junior Glider per minute';

  public static function calcFlightCharges($flight)
  {
    $charges = [];

    $gliderAircraft = $flight->gliderAircraft();
    if(!$gliderAircraft) {
      throw new \Exception("Glider {$flight->glider} not found.");
    }

    $memberToCharge = $flight->p2Member;
    if($flight->billingOption->name == BillingOption::CHARGE_PIC) {
      $memberToCharge = $flight->picMember;
    }

    $charges['glider'] = self::calcGliderCharge($flight, $gliderAircraft, $memberToCharge);
    if($flight->launchType == LaunchType::winchLaunchType()) {
      $charges['winchLaunch'] = self::calcWinchCharge($flight, $memberToCharge);
    }

    return [
      ['member' => $flight->p2Member, 'charges' => $charges]
    ];
  }

  private static function calcWinchCharge($flight, $memberToCharge)
  {
    $chargeName = Accounting::CHARGE_WINCH;
    if($memberToCharge->isJunior()) {
      $chargeName = Accounting::CHARGE_JUNIOR_WINCH;
    }
    $winchCharge = self::fetchCharge($chargeName, $flight);
    return $winchCharge->amount;
  }

  private static function calcGliderCharge($flight, $gliderAircraft, $memberToCharge)
  {
    $chargeName = Accounting::CHARGE_GLIDER_PER_MINUTE;
    if($memberToCharge->isJunior()) {
      $chargeName = Accounting::CHARGE_JUNIOR_GLIDER_PER_MINUTE;
    }
    $gliderCharge = self::fetchCharge($chargeName, $flight);

    $flightDuration = $flight->getFlightDuration();
    $minutes = min($flightDuration, $gliderAircraft->max_perflight_charge);

    return $minutes * $gliderCharge->amount;
  }

  private static function fetchCharge($chargeName, $flight) {
    $charge = $flight->organisation->charges()
                          ->where(['name' => $chargeName, 'location' => $flight->location])
                          ->validAt($flight->getStartDateTime())->first();
    if(!$charge) {
      throw new \Exception("{$chargeName} charge for location {$flight->location} not found.");
    }
    return $charge;
  }
}