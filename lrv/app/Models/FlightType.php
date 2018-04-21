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

    protected static $types = null;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    private static function getCachedTypes()
    {
        if(static::$types == null) {
           static::$types = collect([]);
        }
        return static::$types;
    }

    private static function getFlightType($strType)
    {
      return FlightType::where('name', $strType)->first();
    }

    public static function glidingFlightType()
    {
        $name = 'Glider';
        if(!static::getCachedTypes()->has($name)) {
            static::getCachedTypes()[$name] = FlightType::getFlightType($name);
        }
        return static::getCachedTypes()[$name];
    }

    public static function checkFlightType()
    {
        $name = 'Tow plane check flight';
        if(!static::getCachedTypes()->has($name)) {
            static::getCachedTypes()[$name] = FlightType::getFlightType($name);
        }
        return static::getCachedTypes()[$name];
    }

    public static function retrieveFlightType()
    {
        $name = 'Tow plane retrieve';
        if(!static::getCachedTypes()->has($name)) {
            static::getCachedTypes()[$name] = FlightType::getFlightType($name);
        }
        return static::getCachedTypes()[$name];
    }

    public static function landingFeeFlightType()
    {
        $name = 'Landing Charge';
        if(!static::getCachedTypes()->has($name)) {
            static::getCachedTypes()[$name] = FlightType::getFlightType($name);
        }
        return static::getCachedTypes()[$name];
    }
}
