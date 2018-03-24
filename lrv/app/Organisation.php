<?php

namespace App;

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
        return $this->hasMany('App\Member', 'org');
    }

    public function users()
    {
        return $this->hasMany('App\User', 'org');
    }
}
