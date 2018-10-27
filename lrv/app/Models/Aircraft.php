<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'aircraft';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = false;

  public function isClubGlider(){
    return $this->club_glider > 0;
  }
}
