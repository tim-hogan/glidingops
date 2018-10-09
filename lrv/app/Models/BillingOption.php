<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingOption extends Model
{
    const CHARGE_P2             = "Charge P2";
    const CHARGE_PIC            = "Charge PIC";
    const TRIAL_CASH_ON_DAY     = "Trial Cash on Day";
    const TRIAL_CLUB_VOUCHER    = "Trial Club Voucher";
    const TRIAL_GRAB_ONE_TREAT  = "Trial Grab-one/Treat";
    const CHARGE_50_50          = "Charge 50/50";
    const VISITING_PILOT_PIC    = "Visiting Pilot PIC";
    const VISITING_PILOT_P2     = "Visiting Pilot P2";
    const NO_CHARGE             = "No Charge";
    const OTHER_MEMBER          = "Other Member";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'billingoptions';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}
