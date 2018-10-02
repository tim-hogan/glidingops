<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateTimeZone;

class Member extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be visible in array/json representation.
     *
     * @var array
     */
    // protected $visible = ['id', 'member_id', 'org', 'firstname', 'surname', 'displayname'];

    /**
     * The roles that belong to the meber.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_member');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation', 'org');
    }

    public function flightsAsPIC()
    {
        return $this->hasMany('App\Models\Flight', 'pic');
    }

    public function flightsAsP2()
    {
        return $this->hasMany('App\Models\Flight', 'p2');
    }

    public function membershipStatus()
    {
        return $this->belongsTo('App\Models\MembershipStatus', 'status');
    }

    public function timeZone()
    {
        return new DateTimeZone($this->organisation->timezone);
    }

    public function birthdayDate()
    {
        $birth_date = DateTime::createFromFormat('Y-m-d', $this->date_of_birth);
        $birth_date->setTime(0,0,0);
        $birth_date->setTimeZone($this->timeZone());

        return $birth_date;
    }

    public function age($now = null)
    {
        if(is_null($now)) {
            $now = new DateTime('now', $this->timeZone());
        }

        $interval = $this->birthdayDate()->diff($now);
        return $interval->y;
    }

    public function isJunior($now = null)
    {
        return ($this->age($now) < 26);
    }
}