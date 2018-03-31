<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    function getTowChargeType()
    {
      //Returns 0 (Not defined)
      //Returns 1 (Height Based)
      //Returns 2 (Time bases)
      $org = App\Models\Organisation::find($org_id);
      if($org) {
        if($org->tow_height_charging == 1) {
          return 1;
        }

        if($org->tow_time_based) {
          return 2;
        }
      }

      return 0;
    }
}
