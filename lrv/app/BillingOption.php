<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingOption extends Model
{
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
