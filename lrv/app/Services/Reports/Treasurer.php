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

    $rows = $flights->get()->flatMap(function($flight){
      $flightCharges = Accounting::calcFlightCharges($flight);
      $warnings = $flightCharges['warnings'];

      $result = collect($flightCharges['chargedMembers'])->map(function($charges) use ($warnings, $flight){
        return array_merge([
          'warnings' => $warnings,
          'flight' => $flight,
        ], $charges);
      });

      if($result->isEmpty()) {
        $result = collect([[
          'warnings' => $warnings,
          'flight' => $flight,
          'member' => null,
          'charges' => []
        ]]);
      }

      return $result;
    });

    $unchargedFlights = $rows->filter(function($row){
      return empty($row['charges']);
    });

    $chargedFlights = $rows->filter(function($row){
      return !empty($row['charges']);
    })->groupBy(function($row){
      return $row['member']->displayname;
    });

    return [
      'count' => $flights->count(),
      'unchargedFlights' => $unchargedFlights,
      'chargedFlights' => $chargedFlights
    ];
  }
}