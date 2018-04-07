<?php

namespace App\Helpers;

class DateTimeFormat {
  public static function formatDateStr($strdate) {
    return substr($strdate,6,2) . "/" . substr($strdate,4,2) . "/" . substr($strdate,0,4);
  }

  public static function timeLocalFormat($d, $strTimeZone, $strFormat){
    if ($strTimeZone==NULL || empty($strTimeZone)) {
      $strTimeZone = 'UTC';
    }

    $date = new \DateTime();
    $date = $d;
    $date->setTimezone(new \DateTimeZone($strTimeZone));

    return $date->format($strFormat);
  }

  public static function duration($durationSeconds)
  {
    $hours = intval($durationSeconds / 3600);
    $mins = intval(($durationSeconds % 3600) / 60);
    $timeval = sprintf("%02d:%02d",$hours,$mins);
    return $timeval;
  }
}