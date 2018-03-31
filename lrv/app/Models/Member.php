<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $visible = ['id', 'member_id', 'org', 'firstname', 'surname', 'displayname'];

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
}
