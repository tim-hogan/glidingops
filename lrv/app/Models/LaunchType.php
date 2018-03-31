<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaunchType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'launchtypes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
