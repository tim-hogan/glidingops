<?php

namespace App\Models\Types;

class TowChargeType
{
  const NOT_DEFINED   = 0;
  const HEIGHT_BASED  = 1;
  const TIME_BASED    = 2;

  protected $type;

  private function __construct($type)
  {
    $this->type = $type;
  }

  public function type() {
    return $this->type;
  }

  public static function heightBased() {
    return new TowChargeType(TowChargeType::HEIGHT_BASED);
  }

  public static function timeBased() {
    return new TowChargeType(TowChargeType::TIME_BASED);
  }

  public static function notDefined() {
    return new TowChargeType(TowChargeType::NOT_DEFINED);
  }

  public function isHeightBased() {
    return ($this->type == TowChargeType::HEIGHT_BASED);
  }

  public function isTimeBased() {
    return ($this->type == TowChargeType::TIME_BASED);
  }

  public function isNotDefined() {
    return ($this->type == TowChargeType::NOT_DEFINED);
  }
}