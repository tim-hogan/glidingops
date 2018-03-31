<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flighttypes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
