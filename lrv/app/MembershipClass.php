<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MembershipClass extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'membership_class';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
