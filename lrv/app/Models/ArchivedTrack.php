<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedTrack extends Model
{
    protected $connection = 'tracks';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tracksarchive';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}