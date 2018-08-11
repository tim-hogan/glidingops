<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // instructor
    const ROLE_AB_CAT_INSTRUCTOR    = 'A/B Cat Instructor';
    const ROLE_C_CAT_INSTRUCTOR     = 'C Cat Instructor';

    const ROLE_WINCH_DRIVER         = 'Winch Driver';
    const ROLE_LPC                  = 'Launch Point Controller';
    const ROLE_ENGINEER             = 'Engineer';
    const ROLE_CMT                  = 'Committee / Management Team';

    const ROLE_TOW_PILOT            = 'Tow Pilot';
    const ROLE_MEMBER               = 'Member';


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The members that belong to the role.
     */
    public function members()
    {
        return $this->belongsToMany('App\Models\Member', 'role_member');
    }

    public static function byName($name)
    {
        return self::where('name', $name)->first();
    }
}
