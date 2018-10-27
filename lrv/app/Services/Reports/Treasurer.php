<?php

namespace App\Services\Reports;

use DateTimeImmutable;
use App\Models\Flight;
use App\Services\Wgc\Accounting;

class Treasurer
{
  public static function build($user, $organisation, $monthYearDate) {
    $dateStart = DateTimeImmutable::createFromMutable ($monthYearDate)->modify('first day of this month');
    $dateEnd   = DateTimeImmutable::createFromMutable ($monthYearDate)->modify('last day of this month');

    $dateStartStr = $dateStart->format('Ymd');
    $dateEndStr   = $dateEnd->format('Ymd');

    $flights = Flight::where(['org' => $organisation->id, 'finalised' => 1])
      ->where('localdate', '>=', $dateStartStr)
      ->where('localdate', '<=', $dateEndStr)
      ->where('deleted', 0);

    $rows = $flights->get()->map(function($flight){
      return Accounting::calcFlightCharges($flight);
    });

    return [
      'count' => $flights->count(),
      'rows' => $rows
    ];
  }
}