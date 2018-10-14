<?php

namespace App\Models;

use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use App\Models\Types\TowChargeType;

class Organisation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organisations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the members for the organisation.
     */
    public function members()
    {
        return $this->hasMany('App\Models\Member', 'org');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'org');
    }

    public function vectors()
    {
        return $this->hasMany(Vector::class);
    }

    public function timeZone() {
        return new DateTimeZone($this->timezone);
    }

    public function charges()
    {
        return $this->hasMany('App\Models\Charge', 'org');
    }

    /**
     * If timezone is missing we default to UTC
     */
    public function getTimezoneAttribute($value)
    {
        $timezone = trim($value);
        if(empty($timezone)) {
            return 'UTC';
        }

        return $timezone;
    }

    public function getTowChargeType()
    {
        if($this->tow_height_charging) {
          return TowChargeType::heightBased();
        }

        if($this->tow_time_based) {
          return TowChargeType::timeBased();
        }

        return TowChargeType::notDefined();
    }
}
