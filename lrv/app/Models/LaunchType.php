<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaunchType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'launchtypes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    private static function getLaunchType($strType)
    {
      return LaunchType::where('name', $strType)->first();
    }

    public static function towLaunchType(){
        return LaunchType::getLaunchType('Tow Plane');
    }

    public static function selfLaunchType(){
        return LaunchType::getLaunchType('Self Launch');
    }

    public static function winchLaunchType(){
        return LaunchType::getLaunchType('Winch');
    }
}
