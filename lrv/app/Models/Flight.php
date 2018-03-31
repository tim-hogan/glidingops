<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flights';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function picMember()
    {
        return $this->belongsTo('App\Models\Member', 'pic');
    }

    public function p2Member()
    {
        return $this->belongsTo('App\Models\Member', 'p2');
    }

    public function towPilotMember()
    {
        return $this->belongsTo('App\Models\Member', 'towpilot');
    }

    public function billingOption()
    {
        return $this->belongsTo('App\Models\BillingOption', 'billing_option');
    }

    public function towPlane()
    {
        return $this->belongsTo('App\Models\Aircraft', 'towplane');
    }

    public function launchType()
    {
        return $this->belongsTo('App\Models\LaunchType', 'launchtype');
    }
}
