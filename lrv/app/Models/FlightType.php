<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flighttypes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    private static function getFlightType($strType)
    {
      return FlightType::where('name', $strType)->first();
    }

    public static function glidingFlightType()
    {
        return FlightType::getFlightType('Glider');
    }

    public static function checkFlightType()
    {
        return FlightType::getFlightType('Tow plane check flight');
    }

    public static function retrieveFlightType()
    {
        return FlightType::getFlightType('Tow plane retrieve');
    }

    public static function landingFeeFlightType()
    {
        return FlightType::etFlightType('Landing Charge');
    }

}
